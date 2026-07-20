<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\Member;
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

        // Search by title, author, or barcode
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $books = $query->orderBy('title', 'asc')->get();
        $categories = [
            'Pemerintahan',
            'Hukum dan Undang-Undang',
            'Politik',
            'Demokrasi',
            'Sosial',
            'Keagamaan',
            'Sengketa Pemilu',
            'Riset Pilkada',
            'Akuntansi',
            'Skripsi',
            'Laporan Hasil Pengawasan',
            'Motivasi',
            'Novel'
        ];

        return view('dashboards.member_catalog', compact('books', 'categories'));
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

        return view('dashboards.member_history', compact('borrows', 'totalLoans'));
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
        
        // Cost of +1 limit is 50 points
        $cost = 50;
        
        if ($member->points < $cost) {
            return back()->with('error', "Poin Anda tidak mencukupi untuk melakukan penukaran. Butuh {$cost} poin, poin saat ini: {$member->points}.");
        }

        // Deduct points, increase limit
        $member->points -= $cost;
        $member->borrow_limit += 1;
        $member->save();

        return redirect()->route('member.rewards')->with('success', "Penukaran berhasil! Batas maksimal peminjaman Anda bertambah menjadi {$member->borrow_limit} buku.");
    }

    /**
     * Request online borrowing (creates pending borrow request).
     */
    public function requestBorrow(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id'
        ]);

        $member = Auth::user()->member;
        
        if ($member->status === 'pending') {
            return back()->with('error', 'Akun Anda sedang menunggu verifikasi oleh Admin. Anda belum bisa meminjam buku.');
        }
        if ($member->status === 'rejected') {
            return back()->with('error', 'Pendaftaran Anda ditolak. Hubungi petugas perpustakaan.');
        }

        $book = Book::find($request->book_id);
        
        if ($book->available_stock <= 0) {
            return back()->with('error', 'Maaf, buku ini sedang tidak tersedia (stok habis).');
        }

        // Validate member loan limit
        $activeLoansCount = Borrow::where('member_id', $member->id)
            ->whereIn('status', ['borrowed', 'pending'])
            ->count();

        if ($activeLoansCount >= $member->borrow_limit) {
            return back()->with('error', "Batas peminjaman Anda sudah tercapai (maksimal {$member->borrow_limit} buku sekaligus).");
        }

        // Auto-approve: langsung proses peminjaman tanpa pending
        $loanDuration = SettingController::getSetting('loan_duration', 7);
        Borrow::create([
            'member_id' => $member->id,
            'book_id' => $book->id,
            'borrow_date' => now(),
            'due_date' => now()->addDays($loanDuration),
            'status' => 'borrowed',
        ]);

        // Kurangi stok buku
        $book->decrement('available_stock');
        $book->update(['is_available' => $book->available_stock > 0]);

        // Tambah total pinjaman dan poin
        $member->increment('total_loans');
        $member->increment('points', 10);

        return back()->with('success', "Peminjaman buku '{$book->title}' berhasil! Harap diambil ke perpustakaan. Jatuh tempo: " . now()->addDays($loanDuration)->format('d M Y') . ".");
    }
}
