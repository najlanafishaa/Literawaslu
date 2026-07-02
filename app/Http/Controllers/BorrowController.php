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
        // Get all active borrowings
        $activeBorrows = Borrow::where('status', 'borrowed')
            ->with(['member.user', 'book'])
            ->orderBy('borrow_date', 'desc')
            ->get();

        // Get members and books for autocomplete/select options
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
            'barcode.required' => 'Barcode buku wajib diisi/dipindai.'
        ]);

        // Find member
        $member = Member::where('member_code', $request->member_code)->first();
        if (!$member) {
            return back()->with('error', "Member dengan kode '{$request->member_code}' tidak ditemukan.");
        }

        // Find book
        $book = Book::where('barcode', $request->barcode)->first();
        if (!$book) {
            return back()->with('error', "Buku dengan barcode '{$request->barcode}' tidak ditemukan.");
        }
        // Validate book availability (available stock > 0)
        if ($book->available_stock <= 0) {
            return back()->with('error', "Stok buku '{$book->title}' sedang habis.");
        }

        // Validate member loan limit
        $activeLoansCount = Borrow::where('member_id', $member->id)
            ->where('status', 'borrowed')
            ->count();

        if ($activeLoansCount >= $member->borrow_limit) {
            return back()->with('error', "Batas peminjaman untuk member '{$member->user->name}' telah tercapai (maksimal {$member->borrow_limit} buku).");
        }

        $loanDuration = SettingController::getSetting('loan_duration', 7);

        // Process checkout
        Borrow::create([
            'member_id' => $member->id,
            'book_id' => $book->id,
            'borrow_date' => Carbon::today(),
            'due_date' => Carbon::today()->addDays($loanDuration),
            'status' => 'borrowed',
        ]);

        // Update book availability stock
        $book->decrement('available_stock');
        $book->update(['is_available' => $book->available_stock > 0]);

        return back()->with('success', "Buku '{$book->title}' berhasil dipinjam oleh '{$member->user->name}'. Jatuh tempo pada " . Carbon::today()->addDays($loanDuration)->format('d M Y') . ".");
    }

    /**
     * Handle returning (Checkin) using barcode scan.
     */
    public function checkin(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
        ], [
            'barcode.required' => 'Scan/masukkan barcode buku untuk pengembalian.'
        ]);

        // Find book
        $book = Book::where('barcode', $request->barcode)->first();
        if (!$book) {
            return back()->with('error', "Buku dengan barcode '{$request->barcode}' tidak ditemukan.");
        }

        // Find active borrow record
        $borrow = Borrow::where('book_id', $book->id)
            ->where('status', 'borrowed')
            ->first();

        if (!$borrow) {
            return back()->with('error', "Buku '{$book->title}' tidak terdaftar dalam peminjaman aktif.");
        }

        $member = $borrow->member;
        $returnDate = Carbon::today();
        $dueDate = Carbon::parse($borrow->due_date);
        
        $isLate = $returnDate->greaterThan($dueDate);
        $pointsEarned = 0;

        $rewardPointsSetting = SettingController::getSetting('reward_points', 10);
        $lateFeeSetting = SettingController::getSetting('late_fee', 2000);

        // Reward points logic
        if (!$isLate) {
            $pointsEarned = $rewardPointsSetting;
            $member->points += $pointsEarned;
        }

        // Update loan status
        $borrow->update([
            'return_date' => $returnDate,
            'status' => 'returned',
        ]);

        // Increment member total loans
        $member->total_loans += 1;
        $member->save();

        // Mark book copy returned (increment available stock)
        $book->increment('available_stock');
        $book->update(['is_available' => $book->available_stock > 0]);

        $message = "Buku '{$book->title}' berhasil dikembalikan oleh '{$member->user->name}'.";
        if ($isLate) {
            $daysLate = $returnDate->diffInDays($dueDate);
            $fine = $daysLate * $lateFeeSetting;
            return back()->with('warning', $message . " (Terlambat {$daysLate} hari. Denda Keterlambatan: Rp " . number_format($fine, 0, ',', '.') . ". Member tidak mendapat poin reward).");
        } else {
            return back()->with('success', $message . " Member mendapatkan +{$pointsEarned} poin reward! Saldo poin saat ini: {$member->points}.");
        }
    }

    /**
     * Show general transaction history for Super Admin.
     */
    public function history()
    {
        $borrows = Borrow::with(['member.user', 'book'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboards.admin_borrows', compact('borrows'));
    }
}
