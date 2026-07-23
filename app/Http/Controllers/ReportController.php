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

    /**
     * Render printable PDF report view.
     */
    public function exportPdf(Request $request)
    {
        [$startDate, $endDate, $filterLabel] = $this->resolveDateFilter($request);

        $borrowQuery = Borrow::query();
        if ($startDate && $endDate) {
            $borrowQuery->whereBetween('borrow_date', [$startDate, $endDate]);
        }

        $borrows = $borrowQuery->with(['member.user', 'book'])->orderBy('borrow_date', 'desc')->get();

        $totalBorrowCount = $borrows->count();

        $today = Carbon::today()->toDateString();
        $overdueBorrows = (clone $borrowQuery)->whereIn('status', ['borrowed', 'terlambat'])
            ->where('due_date', '<', $today)
            ->get();

        $returnedLateBorrows = $borrows->filter(function ($b) {
            return $b->status === 'returned' && $b->return_date && Carbon::parse($b->return_date)->greaterThan(Carbon::parse($b->due_date));
        })->values();

        $lateCount = $overdueBorrows->count() + $returnedLateBorrows->count();

        $totalFineAmount  = (clone $borrowQuery)->whereIn('fine_status', ['unpaid', 'paid'])->count();
        $unpaidFineAmount = (clone $borrowQuery)->where('fine_status', 'unpaid')->count();
        $paidFineAmount   = (clone $borrowQuery)->where('fine_status', 'paid')->count();

        return view('reports.pdf', compact(
            'borrows',
            'totalBorrowCount',
            'lateCount',
            'totalFineAmount',
            'paidFineAmount',
            'unpaidFineAmount',
            'filterLabel',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export borrowing activity report to Excel (CSV compatible format).
     */
    public function exportExcel(Request $request)
    {
        [$startDate, $endDate, $filterLabel] = $this->resolveDateFilter($request);

        $borrowQuery = Borrow::query();
        if ($startDate && $endDate) {
            $borrowQuery->whereBetween('borrow_date', [$startDate, $endDate]);
        }

        $borrows = $borrowQuery->with(['member.user', 'book'])->orderBy('borrow_date', 'desc')->get();

        $totalBorrowCount = $borrows->count();

        $today = Carbon::today()->toDateString();
        $overdueBorrows = (clone $borrowQuery)->whereIn('status', ['borrowed', 'terlambat'])
            ->where('due_date', '<', $today)
            ->get();

        $returnedLateBorrows = $borrows->filter(function ($b) {
            return $b->status === 'returned' && $b->return_date && Carbon::parse($b->return_date)->greaterThan(Carbon::parse($b->due_date));
        })->values();

        $lateCount = $overdueBorrows->count() + $returnedLateBorrows->count();

        $totalFineAmount  = (clone $borrowQuery)->whereIn('fine_status', ['unpaid', 'paid'])->count();
        $unpaidFineAmount = (clone $borrowQuery)->where('fine_status', 'unpaid')->count();
        $paidFineAmount   = (clone $borrowQuery)->where('fine_status', 'paid')->count();

        $filename = 'Laporan_Aktivitas_Perpustakaan_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($borrows, $filterLabel, $totalBorrowCount, $lateCount, $totalFineAmount, $paidFineAmount, $unpaidFineAmount) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel alignment
            fputs($file, "\xEF\xBB\xBF");

            // Header metadata
            fputcsv($file, ['LAPORAN REKAPITULASI HASIL PEMINJAMAN & PENGEMBALIAN BUKU']);
            fputcsv($file, ['PERPUSTAKAAN DIGITAL LITERAWASLU']);
            fputcsv($file, ['Periode Laporan:', $filterLabel]);
            fputcsv($file, ['Tanggal Dicetak:', now()->format('d M Y H:i')]);
            fputcsv($file, []);

            // Summary stats table
            fputcsv($file, ['--- RINGKASAN STATISTIK LAPORAN ---']);
            fputcsv($file, ['Total Peminjaman', 'Jumlah Keterlambatan', 'Total Sanksi Donasi Buku', 'Donasi Buku Dipenuhi', 'Donasi Buku Belum Dipenuhi']);
            fputcsv($file, [
                $totalBorrowCount . ' Transaksi',
                $lateCount . ' Kali',
                $totalFineAmount . ' Buku',
                $paidFineAmount . ' Buku',
                $unpaidFineAmount . ' Buku'
            ]);
            fputcsv($file, []);

            // Main CSV Header (10 columns)
            fputcsv($file, [
                'No',
                'Kode Member',
                'Nama Member',
                'Judul Buku',
                'Barcode Buku',
                'Tanggal Pinjam',
                'Tanggal Jatuh Tempo',
                'Tanggal Kembali',
                'Status Peminjaman',
                'Keterangan Keterlambatan / Sanksi'
            ]);

            foreach ($borrows as $index => $borrow) {
                $due = Carbon::parse($borrow->due_date);
                $returnDate = $borrow->return_date ? Carbon::parse($borrow->return_date) : null;
                
                $lateDays = 0;
                if ($returnDate && $returnDate->greaterThan($due)) {
                    $lateDays = (int) $returnDate->diffInDays($due);
                } elseif (!$returnDate && Carbon::now()->startOfDay()->greaterThan($due)) {
                    $lateDays = (int) Carbon::now()->startOfDay()->diffInDays($due);
                }

                $keterangan = 'Tepat Waktu';
                if ($lateDays > 0) {
                    if ($lateDays == 1) {
                        $keterangan = "Terlambat 1 hari (-10 Poin)";
                    } elseif ($lateDays == 2) {
                        $keterangan = "Terlambat 2 hari (-20 Poin)";
                    } elseif ($lateDays == 3) {
                        $keterangan = "Terlambat 3 hari (-30 Poin)";
                    } else {
                        $keterangan = "Terlambat {$lateDays} hari (Wajib Donasi 1 Buku Fisik)";
                    }
                }

                $statusText = match($borrow->status) {
                    'returned' => 'Dikembalikan',
                    'borrowed' => 'Sedang Dipinjam',
                    'pending' => 'Menunggu Verifikasi',
                    'terlambat' => 'Terlambat',
                    'rejected' => 'Ditolak',
                    default => ucfirst($borrow->status)
                };

                fputcsv($file, [
                    $index + 1,
                    $borrow->member ? $borrow->member->member_code : '-',
                    $borrow->member && $borrow->member->user ? $borrow->member->user->name : '-',
                    $borrow->book ? $borrow->book->title : '-',
                    $borrow->book ? $borrow->book->barcode : '-',
                    $borrow->borrow_date ? Carbon::parse($borrow->borrow_date)->format('d/m/Y') : '-',
                    $borrow->due_date ? Carbon::parse($borrow->due_date)->format('d/m/Y') : '-',
                    $borrow->return_date ? Carbon::parse($borrow->return_date)->format('d/m/Y') : 'Belum Kembali',
                    $statusText,
                    $keterangan
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
