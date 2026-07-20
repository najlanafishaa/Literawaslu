<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\Member;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SettingController;

class MemberController extends Controller
{
    /**
     * Show book catalog.
     */
    public function catalog(Request $request)
    {
        $query = Book::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $books = $query->with('reviews')->orderBy('title', 'asc')->get();
        $categories = Category::orderBy('name')->pluck('name');

        $member = Auth::user()->member;

        // Get books this member is eligible to review: returned OR borrowed for 7 days or more
        $returnedBookIds = Borrow::where('member_id', $member->id)
            ->where(function ($q) {
                $q->where('status', 'returned')
                  ->orWhere('borrow_date', '<=', now()->subDays(7));
            })
            ->pluck('book_id')
            ->toArray();

        // Get books this member has already reviewed
        $reviewedBookIds = $member->reviews()->pluck('book_id')->toArray();

        return view('dashboards.member_catalog', compact('books', 'categories', 'member', 'returnedBookIds', 'reviewedBookIds'));
    }

    /**
     * Show digital membership card.
     */
    public function card()
    {
        $member = Auth::user()->member;
        return view('dashboards.member_card', compact('member'));
    }

    /**
     * Show borrowing history.
     */
    public function history()
    {
        $member = Auth::user()->member;

        $borrows = Borrow::where('member_id', $member->id)
            ->with('book')
            ->orderBy('borrow_date', 'desc')
            ->get();

        $totalLoans = $member->total_loans;

        return view('dashboards.member_history', compact('borrows', 'totalLoans', 'member'));
    }

    /**
     * Show rewards page.
     */
    public function rewards()
    {
        $member = Auth::user()->member;
        return view('dashboards.member_rewards', compact('member'));
    }

    /**
     * Redeem rewards (exchange points to increase borrowing limit).
     */
    public function redeem(Request $request)
    {
        $member = Auth::user()->member;

        $cost = 50;

        if ($member->points < $cost) {
            return back()->with('error', "Poin Anda tidak mencukupi. Butuh {$cost} poin, poin saat ini: {$member->points}.");
        }

        $member->points -= $cost;
        $member->borrow_limit += 1;
        $member->save();

        return redirect()->route('member.rewards')->with('success', "Penukaran berhasil! Batas peminjaman Anda bertambah menjadi {$member->borrow_limit} buku.");
    }

    /**
     * Request online borrowing.
     */
    public function requestBorrow(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $member = Auth::user()->member;

        if ($member->status === 'pending') {
            return back()->with('error', 'Akun Anda sedang menunggu verifikasi. Anda belum bisa meminjam buku.');
        }
        if ($member->status === 'rejected') {
            return back()->with('error', 'Pendaftaran Anda ditolak. Hubungi petugas perpustakaan.');
        }

        // Rule: tidak bisa pinjam kalau ada pinjaman aktif
        if ($member->hasActiveBorrow()) {
            return back()->with('error', 'Anda masih memiliki buku yang sedang dipinjam. Kembalikan terlebih dahulu sebelum meminjam buku lain.');
        }

        // Rule: tidak bisa pinjam kalau ada denda belum dibayar
        if ($member->hasUnpaidFine()) {
            return back()->with('error', 'Anda memiliki denda yang belum dibayar. Selesaikan kewajiban terlebih dahulu.');
        }

        $book = Book::find($request->book_id);

        if ($book->available_stock <= 0) {
            return back()->with('error', 'Maaf, buku ini sedang tidak tersedia (stok habis).');
        }

        $loanDuration = SettingController::getSetting('loan_duration', 7);

        Borrow::create([
            'member_id'  => $member->id,
            'book_id'    => $book->id,
            'borrow_date' => now(),
            'due_date'    => now()->addDays($loanDuration),
            'status'      => 'borrowed',
            'fine_amount' => 0,
            'fine_status' => 'none',
        ]);

        // Kurangi stok
        $book->decrement('available_stock');
        $book->update(['is_available' => $book->available_stock > 0]);

        // +5 poin saat pinjam (bukan +10 seperti sebelumnya)
        $member->increment('total_loans');
        $member->increment('points', 5);

        return back()->with('success', "Peminjaman buku '{$book->title}' berhasil! Jatuh tempo: " . now()->addDays($loanDuration)->format('d M Y') . ". (+5 poin reward)");
    }
}
