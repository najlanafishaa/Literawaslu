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

        if ($request->filled('borrow_type')) {
            if ($request->borrow_type === 'online_only') {
                $query->whereNotNull('drive_link')->where('stock', 0);
            } elseif ($request->borrow_type === 'offline_only') {
                $query->whereNull('drive_link');
            } elseif ($request->borrow_type === 'both') {
                $query->whereNotNull('drive_link')->where('stock', '>', 0);
            }
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

        if ($member) {
            Borrow::syncActiveBorrowStates($member);
            $member->refresh();
        }

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
        if ($member) {
            Borrow::syncActiveBorrowStates($member);
            $member->refresh();
        }
        $pointHistories = $member ? $member->pointHistories : collect();
        return view('dashboards.member_rewards', compact('member', 'pointHistories'));
    }

    /**
     * Show notifications & admin replies page.
     */
    public function notifications()
    {
        $user = Auth::user();
        $member = $user->member;

        $activeBorrows = $member ? Borrow::where('member_id', $member->id)
            ->whereIn('status', ['borrowed', 'terlambat'])
            ->with('book')
            ->get() : collect();

        $userQuestions = \App\Models\Question::where('email', $user->email)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboards.member_notifications', compact('member', 'activeBorrows', 'userQuestions'));
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

        Borrow::syncActiveBorrowStates($member);
        $member->refresh();

        if ($member->status === 'pending') {
            return back()->with('error', 'Akun Anda sedang menunggu verifikasi. Anda belum bisa meminjam buku.');
        }
        if ($member->status === 'rejected') {
            return back()->with('error', 'Pendaftaran Anda ditolak. Hubungi petugas perpustakaan.');
        }

        $book = Book::find($request->book_id);
        $borrowMode = $request->input('borrow_mode');

        // Determine if this specific request is for online access or offline physical borrow
        $isOnlineRequest = ($borrowMode === 'online') || (empty($borrowMode) && !empty($book->drive_link) && $book->available_stock <= 0);

        if ($isOnlineRequest) {
            // Count active online borrows for member
            $activeOnlineCount = Borrow::where('member_id', $member->id)
                ->whereIn('status', ['pending', 'borrowed', 'terlambat'])
                ->whereHas('book', function ($q) {
                    $q->whereNotNull('drive_link')->where('drive_link', '!=', '');
                })->count();

            if ($activeOnlineCount >= 3) {
                return back()->with('error', 'Pengajuan tidak dapat diproses. Anda telah mencapai batas maksimal 3 buku online dalam waktu bersamaan.');
            }
        } else {
            // Count active offline borrows for member
            $activeOfflineCount = Borrow::where('member_id', $member->id)
                ->whereIn('status', ['pending', 'borrowed', 'terlambat'])
                ->whereHas('book', function ($q) {
                    $q->whereNull('drive_link')->orWhere('drive_link', '');
                })->count();

            if ($activeOfflineCount >= 1) {
                return back()->with('error', 'Pengajuan tidak dapat diproses. Anda telah mencapai batas maksimal 1 buku offline dalam waktu bersamaan.');
            }
        }

        // Cek apakah sudah ada permintaan pending untuk buku yang sama
        $alreadyRequested = Borrow::where('member_id', $member->id)
            ->where('book_id', $request->book_id)
            ->where('status', 'pending')
            ->exists();
        if ($alreadyRequested) {
            return back()->with('error', 'Pengajuan tidak dapat diproses. Silakan periksa kembali ketentuan peminjaman atau hubungi Admin.');
        }

        if ($member->status === 'suspended' || $member->points <= 0) {
            return back()->with('error', 'Akun Anda dibekukan karena poin Anda telah habis atau kewajiban belum diselesaikan.');
        }

        if ($member->hasUnpaidFine()) {
            return back()->with('error', 'Pengajuan tidak dapat diproses. Silakan periksa kembali ketentuan peminjaman atau hubungi Admin.');
        }

        $book = Book::find($request->book_id);

        if ($book->available_stock <= 0 && empty($book->drive_link)) {
            return back()->with('error', 'Maaf, buku fisik ini sedang tidak tersedia (stok habis).');
        }

        $loanDuration = 7; // Fixed 7 days for online according to business rules

        // Buat permintaan peminjaman dengan status 'pending'
        Borrow::create([
            'member_id'   => $member->id,
            'book_id'     => $book->id,
            'borrow_date' => now(),
            'due_date'    => now()->addDays($loanDuration),
            'status'      => 'pending',
            'fine_amount' => 0,
            'fine_status' => 'none',
        ]);

        return back()->with('success', "Pengajuan peminjaman berhasil dikirim. Permintaan Anda akan diproses oleh Admin.");
    }
}
