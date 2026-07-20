<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Member;
use App\Models\Borrow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show role-specific dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'member') {
            $member = $user->member;

            $activeBorrows = Borrow::where('member_id', $member->id)
                ->where('status', 'borrowed')
                ->with('book')
                ->get();

            $totalBorrows = Borrow::where('member_id', $member->id)->count();
            $availableBooksCount = Book::where('is_available', true)->count();

            return view('dashboards.member', compact('member', 'activeBorrows', 'totalBorrows', 'availableBooksCount'));
        } else {
            // Build date filter
            [$startDate, $endDate, $filterLabel] = $this->resolveDateFilter($request);

            $borrowQuery = Borrow::query();
            if ($startDate && $endDate) {
                $borrowQuery->whereBetween('borrow_date', [$startDate, $endDate]);
            }

            $totalBooks = Book::count();
            $availableBooks = Book::where('is_available', true)->count();
            $borrowedBooks = Book::where('is_available', false)->count();
            $totalMembers = Member::count();

            $totalTransactions = (clone $borrowQuery)->count();

            $overdueCount = (clone $borrowQuery)
                ->whereIn('status', ['borrowed', 'terlambat'])
                ->where('due_date', '<', now()->toDateString())
                ->count();

            // Book replacement stats (count instead of sum)
            $totalFine = (clone $borrowQuery)->whereIn('fine_status', ['unpaid', 'paid'])->count();
            $unpaidFine = (clone $borrowQuery)->where('fine_status', 'unpaid')->count();
            $paidFine = (clone $borrowQuery)->where('fine_status', 'paid')->count();

            $recentBorrows = Borrow::with(['member.user', 'book'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $popularBooks = Borrow::select('book_id')
                ->selectRaw('count(book_id) as total')
                ->groupBy('book_id')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->with('book')
                ->get();

            if ($user->role === 'super_admin') {
                return view('dashboards.admin', compact(
                    'totalBooks', 'borrowedBooks', 'availableBooks',
                    'totalMembers', 'totalTransactions', 'overdueCount',
                    'recentBorrows', 'popularBooks',
                    'totalFine', 'unpaidFine', 'paidFine',
                    'filterLabel', 'startDate', 'endDate'
                ));
            } else {
                return view('dashboards.petugas', compact(
                    'totalBooks', 'borrowedBooks', 'availableBooks',
                    'totalMembers', 'totalTransactions', 'overdueCount',
                    'recentBorrows'
                ));
            }
        }
    }

    /**
     * Resolve date filter from request.
     * Returns [startDate, endDate, label].
     */
    private function resolveDateFilter(Request $request): array
    {
        $filter = $request->get('filter', 'all');

        switch ($filter) {
            case 'today':
                return [Carbon::today(), Carbon::today(), 'Hari Ini'];
            case 'week':
                return [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek(), 'Minggu Ini'];
            case 'month':
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth(), 'Bulan Ini'];
            case 'year':
                return [Carbon::now()->startOfYear(), Carbon::now()->endOfYear(), 'Tahun Ini'];
            case 'custom':
                $start = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : null;
                $end   = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : null;
                $label = ($start && $end) ? $start->format('d M Y') . ' – ' . $end->format('d M Y') : 'Kustom';
                return [$start, $end, $label];
            default:
                return [null, null, 'Semua Waktu'];
        }
    }
}
