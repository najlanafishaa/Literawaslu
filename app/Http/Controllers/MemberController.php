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
        $defaultCategories = [
            'pemerintahan', 'november', 'hukum dan undang-undang', 'motivasi', 
            'politik', 'sosial', 'demokrasi', 'keagamaan', 'sengketa pemilu', 
            'riset pilkada', 'akuntansi', 'skripsi', 'laporan hasil pengawasan'
        ];
        $dbCategories = Category::orderBy('name')->pluck('name')->toArray();
        $categories = array_unique(array_merge($defaultCategories, $dbCategories));
        sort($categories);
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
        $pointHistories = $member ? $member->pointHistories : collect();
        return view('dashboards.member_rewards', compact('member', 'pointHistories'));
    }

    /**
     * Redeem rewards (exchange points to increase borrowing limit).
     * Rules: 100 pts -> 1 book limit, 200 pts -> 2 books limit, 300 pts -> 3 books limit.
     * Points do NOT decrease upon redemption.
     */
    public function redeem(Request $request)
    {
        $member = Auth::user()->member;
        $targetLimit = (int) $request->input('target_limit');

        if (!in_array($targetLimit, [1, 2, 3])) {
            return back()->with('error', 'Pilihan penukaran tidak valid.');
        }

        $requiredPoints = $targetLimit * 100;

        if ($member->points < $requiredPoints) {
            return back()->with('error', "Poin Anda tidak mencukupi. Butuh {$requiredPoints} poin untuk membuka batas {$targetLimit} buku.");
        }

        if ($member->borrow_limit >= $targetLimit) {
            return back()->with('error', "Batas peminjaman Anda sudah {$member->borrow_limit} buku.");
        }

        $member->borrow_limit = $targetLimit;
        $member->save();

        // Record history for point redemption
        \App\Models\PointHistory::create([
            'member_id' => $member->id,
            'type' => 'redeem',
            'points' => $requiredPoints,
            'description' => "Klaim peningkatan batas pinjam menjadi {$targetLimit} buku ({$requiredPoints} poin tercapai)",
        ]);

        return redirect()->route('member.rewards')->with('success', "Penukaran berhasil! Batas peminjaman Anda bertambah menjadi {$member->borrow_limit} buku.");
    }

    /**
     * Request online borrowing (requires admin verification before taking effect).
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

        // Rule: Maksimal 3 buku pinjaman aktif (pending / borrowed / terlambat)
        if ($member->hasReachedBorrowLimit()) {
            return back()->with('error', 'Anda telah mencapai batas maksimal 3 buku peminjaman online aktif. Kembalikan buku terlebih dahulu sebelum mengajukan peminjaman baru.');
        }

        // Cek apakah sudah ada permintaan pending untuk buku yang sama
        $alreadyRequested = Borrow::where('member_id', $member->id)
            ->where('book_id', $request->book_id)
            ->where('status', 'pending')
            ->exists();
        if ($alreadyRequested) {
            return back()->with('error', 'Anda sudah mengajukan permintaan peminjaman untuk buku ini. Tunggu persetujuan Admin/Petugas.');
        }

        // Rule: tidak bisa pinjam kalau ada sanksi belum diselesaikan
        if ($member->hasUnpaidFine()) {
            return back()->with('error', 'Anda memiliki sanksi ganti buku yang belum diselesaikan. Hubungi petugas perpustakaan.');
        }

        $book = Book::find($request->book_id);

        if ($book->available_stock <= 0) {
            return back()->with('error', 'Maaf, buku ini sedang tidak tersedia (stok habis).');
        }

        $loanDuration = SettingController::getSetting('loan_duration', 7);

        // Buat permintaan peminjaman dengan status 'pending' (menunggu verifikasi admin)
        Borrow::create([
            'member_id'   => $member->id,
            'book_id'     => $book->id,
            'borrow_date' => now(),
            'due_date'    => now()->addDays($loanDuration),
            'status'      => 'pending',
            'fine_amount' => 0,
            'fine_status' => 'none',
        ]);

        // Stok & poin BELUM dikurangi/ditambah — menunggu verifikasi admin

        return back()->with('success', "Permintaan peminjaman buku '{$book->title}' berhasil diajukan! Silakan tunggu persetujuan dari Petugas/Admin. Poin akan diberikan setelah buku dikembalikan.");
    }
}
