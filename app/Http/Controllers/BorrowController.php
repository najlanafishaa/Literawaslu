<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\Member;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BorrowController extends Controller
{
    /**
     * Show borrowing & transaction portal for Petugas/Admins.
     */
    public function index()
    {
        $activeBorrows = Borrow::whereIn('status', ['borrowed', 'terlambat'])
            ->with(['member.user', 'book'])
            ->orderBy('borrow_date', 'desc')
            ->get();

        // Apply on-demand late point deductions for overdue borrows
        foreach ($activeBorrows as $borrow) {
            $this->applyLatePointDeduction($borrow);
        }

        $members = Member::with('user')->get();
        $books = Book::where('is_available', true)->get();

        return view('dashboards.petugas_borrows', compact('activeBorrows', 'members', 'books'));
    }

    /**
     * Handle borrowing (Checkout).
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'member_code' => 'required|string',
            'barcode' => 'required|string',
        ], [
            'member_code.required' => 'Kode member wajib diisi/dipindai.',
            'barcode.required' => 'Barcode buku wajib diisi/dipindai.',
        ]);

        // Find member
        $member = Member::where('member_code', $request->member_code)->first();
        if (!$member) {
            return back()->with('error', "Member dengan kode '{$request->member_code}' tidak ditemukan.");
        }

        // Validate if member is active
        if ($member->status !== 'active') {
            return back()->with('error', "Gagal memproses peminjaman. Akun member '{$member->user->name}' tidak aktif.");
        }

        // Rule: Member cannot borrow if they have an active borrow
        if ($member->hasActiveBorrow()) {
            return back()->with('error', "Member '{$member->user->name}' masih memiliki buku yang sedang dipinjam. Harus dikembalikan terlebih dahulu.");
        }

        // Rule: Member cannot borrow if they have unpaid fines
        if ($member->hasUnpaidFine()) {
            return back()->with('error', "Member '{$member->user->name}' memiliki denda yang belum dibayar. Selesaikan kewajiban terlebih dahulu.");
        }

        // Find book
        $book = Book::where('barcode', $request->barcode)->first();
        if (!$book) {
            return back()->with('error', "Buku dengan barcode '{$request->barcode}' tidak ditemukan.");
        }
        if ($book->available_stock <= 0) {
            return back()->with('error', "Stok buku '{$book->title}' sedang habis.");
        }

        $loanDuration = (int) SettingController::getSetting('loan_duration', 7);

        // Process checkout
        Borrow::create([
            'member_id' => $member->id,
            'book_id'   => $book->id,
            'borrow_date' => Carbon::today(),
            'due_date'    => Carbon::today()->addDays($loanDuration),
            'status'      => 'borrowed',
            'fine_amount' => 0,
            'fine_status' => 'none',
        ]);

        // +5 poin saat berhasil meminjam
        $member->increment('points', 5);

        // Update book availability stock
        $book->decrement('available_stock');
        $book->update(['is_available' => $book->available_stock > 0]);

        return back()->with('success', "Buku '{$book->title}' berhasil dipinjam oleh '{$member->user->name}'. Jatuh tempo: " . Carbon::today()->addDays($loanDuration)->format('d M Y') . ". (+5 poin reward)");
    }

    /**
     * Handle returning (Checkin) using barcode scan.
     */
    public function checkin(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
        ], [
            'barcode.required' => 'Scan/masukkan barcode buku untuk pengembalian.',
        ]);

        $book = Book::where('barcode', $request->barcode)->first();
        if (!$book) {
            return back()->with('error', "Buku dengan barcode '{$request->barcode}' tidak ditemukan.");
        }

        $borrow = Borrow::where('book_id', $book->id)
            ->whereIn('status', ['borrowed', 'terlambat'])
            ->first();

        if (!$borrow) {
            return back()->with('error', "Buku '{$book->title}' tidak terdaftar dalam peminjaman aktif.");
        }

        $member = $borrow->member;
        $returnDate = Carbon::today();
        $dueDate = Carbon::parse($borrow->due_date);

        $daysLate = $returnDate->greaterThan($dueDate)
            ? (int) $returnDate->diffInDays($dueDate)
            : 0;

        // Update return date & status
        $borrow->return_date = $returnDate;
        $borrow->status = 'returned';

        // Hitung deduction poin berdasarkan keterlambatan: 10 poin per hari
        $pointDeduction = $daysLate * 10;

        // Sanksi ganti buku berlaku jika terlambat mencapai 3 hari atau lebih
        if ($daysLate >= 3) {
            $borrow->fine_amount = 1; // Merepresentasikan 1 buku yang wajib diganti
            $borrow->fine_status = 'unpaid'; // Status 'unpaid' di UI akan menjadi 'Wajib Ganti Buku'
        } else {
            $borrow->fine_amount = 0;
            $borrow->fine_status = 'none';
        }

        $borrow->save();

        // Kurangi poin (tidak boleh minus)
        if ($pointDeduction > 0) {
            $member->points = max(0, $member->points - $pointDeduction);
        }

        $member->total_loans += 1;
        $member->save();

        // Kembalikan stok buku
        $book->increment('available_stock');
        $book->update(['is_available' => $book->available_stock > 0]);

        $message = "Buku '{$book->title}' berhasil dikembalikan oleh '{$member->user->name}'.";

        if ($daysLate >= 3) {
            return back()->with('warning', $message . " Terlambat {$daysLate} hari. Poin berkurang -{$pointDeduction}. Sanksi: Wajib Ganti Buku Fisik. Status: Menunggu Penggantian Buku.");
        } elseif ($daysLate > 0) {
            return back()->with('warning', $message . " Terlambat {$daysLate} hari. Poin berkurang -{$pointDeduction}. Saldo poin: {$member->points}.");
        } else {
            return back()->with('success', $message . " Pengembalian tepat waktu! Saldo poin: {$member->points}.");
        }
    }

    /**
     * Pay a fine for a borrow record.
     */
    public function payFine(Borrow $borrow)
    {
        if ($borrow->fine_status !== 'unpaid') {
            return back()->with('error', 'Tidak ada kewajiban ganti buku untuk transaksi ini.');
        }

        $member = $borrow->member;

        $borrow->update(['fine_status' => 'paid']);

        // +10 poin sebagai apresiasi menyelesaikan kewajiban
        $member->increment('points', 10);

        return back()->with('success', "Buku pengganti telah diterima dari '{$member->user->name}'. Member mendapat apresiasi +10 poin!");
    }

    public function history(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $startDate = null;
        $endDate = null;
        $filterLabel = 'Semua Waktu';

        switch ($filter) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today();
                $filterLabel = 'Hari Ini';
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                $filterLabel = 'Minggu Ini';
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $filterLabel = 'Bulan Ini';
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                $filterLabel = 'Tahun Ini';
                break;
            case 'custom':
                $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : null;
                $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : null;
                $filterLabel = ($startDate && $endDate) ? $startDate->format('d M Y') . ' – ' . $endDate->format('d M Y') : 'Kustom';
                break;
        }

        $query = Borrow::with(['member.user', 'book']);

        if ($startDate && $endDate) {
            $query->whereBetween('borrow_date', [$startDate, $endDate]);
        }

        $borrows = $query->orderBy('created_at', 'desc')->get();

        return view('dashboards.admin_borrows', compact('borrows', 'filterLabel'));
    }

    /**
     * Apply on-demand late point deductions for active overdue borrows.
     * This runs when the borrow list is loaded (no cron needed).
     * Points are deducted once per day tracked by a daily check via due_date.
     */
    private function applyLatePointDeduction(Borrow $borrow): void
    {
        if (!in_array($borrow->status, ['borrowed', 'terlambat'])) return;

        $today = Carbon::today();
        $dueDate = Carbon::parse($borrow->due_date);
        $daysLate = $today->greaterThan($dueDate) ? (int) $today->diffInDays($dueDate) : 0;

        if ($daysLate <= 0) return;

        $member = $borrow->member;

        $needsSave = false;

        // Hari ke-3 -> status jadi terlambat
        if ($daysLate >= 3 && $borrow->status !== 'terlambat') {
            $borrow->status = 'terlambat';
            $needsSave = true;
        }

        // On day 3, member must replace book
        if ($daysLate >= 3 && $borrow->fine_status === 'none') {
            $borrow->fine_amount = 1; // Represents 1 book to replace
            $borrow->fine_status = 'unpaid';
            $needsSave = true;
        }

        if ($needsSave) {
            $borrow->save();
        }
    }
}
