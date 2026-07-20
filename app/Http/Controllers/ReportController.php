<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Borrow;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Show general reports dashboard.
     */
    public function index(Request $request)
    {
        // Date filter
        $filter = $request->get('filter', 'all');
        [$startDate, $endDate, $filterLabel] = $this->resolveDateFilter($request);

        // 1. Members count
        $totalMembers = Member::count();
        $membersList = Member::with('user')->orderBy('created_at', 'desc')->get();

        // 2. Books availability stats
        $availableBooks = Book::where('is_available', true)->count();
        $borrowedBooksCount = Book::where('is_available', false)->count();
        $totalBooks = Book::count();

        // 3. Borrow stats
        $borrowQuery = Borrow::query();
        if ($startDate && $endDate) {
            $borrowQuery->whereBetween('borrow_date', [$startDate, $endDate]);
        }
        $allBorrows = (clone $borrowQuery)->with(['member.user', 'book'])->get();
        $totalBorrowCount = $allBorrows->count();

        // 4. Overdue (active loans that are late)
        $today = Carbon::today()->toDateString();
        $overdueBorrows = (clone $borrowQuery)->whereIn('status', ['borrowed', 'terlambat'])
            ->where('due_date', '<', $today)
            ->with(['member.user', 'book'])
            ->get();

        // 5. Returned late
        $returnedLateBorrows = $allBorrows->filter(function ($b) {
            return $b->status === 'returned' && $b->return_date && $b->return_date->greaterThan($b->due_date);
        })->values();

        // 6. Late count
        $lateCount = $overdueBorrows->count() + $returnedLateBorrows->count();

        // 7. Book replacement stats (count instead of sum)
        $totalFineAmount  = (clone $borrowQuery)->whereIn('fine_status', ['unpaid', 'paid'])->count();
        $unpaidFineAmount = (clone $borrowQuery)->where('fine_status', 'unpaid')->count();
        $paidFineAmount   = (clone $borrowQuery)->where('fine_status', 'paid')->count();

        // 8. Most borrowed books
        $popularBooks = Borrow::select('book_id')
            ->selectRaw('count(book_id) as total')
            ->groupBy('book_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->with('book')
            ->get();

        // 9. Monthly trends
        $monthlyTrends = [];
        foreach ($allBorrows as $b) {
            $monthKey = Carbon::parse($b->borrow_date)->format('Y-m (F)');
            $monthlyTrends[$monthKey] = ($monthlyTrends[$monthKey] ?? 0) + 1;
        }
        ksort($monthlyTrends);

        // 10. Reward stats per member
        $memberRewardStats = Member::with('user')
            ->orderBy('points', 'desc')
            ->limit(10)
            ->get();

        return view('reports.index', compact(
            'totalMembers', 'membersList',
            'availableBooks', 'borrowedBooksCount', 'totalBooks',
            'overdueBorrows', 'returnedLateBorrows', 'lateCount',
            'totalFineAmount', 'unpaidFineAmount', 'paidFineAmount',
            'popularBooks', 'monthlyTrends',
            'memberRewardStats', 'totalBorrowCount',
            'filterLabel', 'startDate', 'endDate', 'filter'
        ));
    }

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
