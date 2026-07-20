@extends('layouts.app')

@section('title', 'Laporan Perpustakaan')
@section('header_title', 'Laporan & Analitik')

@section('content')
<div class="welcome-banner" style="margin-bottom: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; width: 100%;">
        <div>
            <h1>Laporan Aktivitas Perpustakaan</h1>
            <p>Rekap statistik data anggota, sirkulasi peminjaman, buku populer, dan catatan keterlambatan.</p>
        </div>
        <button onclick="window.print()" class="btn btn-primary btn-sm" style="background-color: var(--light); color: var(--primary);">
            <i class="fa-solid fa-file-pdf"></i> Cetak Laporan
        </button>
    </div>
</div>

<!-- Date Filter Panel -->
<div class="card" style="margin-bottom: 25px;">
    <div class="card-body" style="padding: 15px 20px;">
        <form action="{{ route('reports.index') }}" method="GET" style="display: flex; justify-content: space-between; align-items: center; gap: 15px; flex-wrap: wrap; margin: 0;">
            <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                <span style="font-size: 0.85rem; font-weight: 700; color: var(--gray-700); margin-right: 5px;"><i class="fa-solid fa-calendar-days"></i> Filter Waktu Laporan:</span>
                <a href="{{ route('reports.index', ['filter' => 'all']) }}" class="btn {{ request('filter', 'all') === 'all' ? 'btn-primary' : 'btn-outline' }} btn-sm" style="padding: 6px 12px; font-size: 0.8rem;">Semua</a>
                <a href="{{ route('reports.index', ['filter' => 'today']) }}" class="btn {{ request('filter') === 'today' ? 'btn-primary' : 'btn-outline' }} btn-sm" style="padding: 6px 12px; font-size: 0.8rem;">Hari Ini</a>
                <a href="{{ route('reports.index', ['filter' => 'week']) }}" class="btn {{ request('filter') === 'week' ? 'btn-primary' : 'btn-outline' }} btn-sm" style="padding: 6px 12px; font-size: 0.8rem;">Minggu Ini</a>
                <a href="{{ route('reports.index', ['filter' => 'month']) }}" class="btn {{ request('filter') === 'month' ? 'btn-primary' : 'btn-outline' }} btn-sm" style="padding: 6px 12px; font-size: 0.8rem;">Bulan Ini</a>
                <a href="{{ route('reports.index', ['filter' => 'year']) }}" class="btn {{ request('filter') === 'year' ? 'btn-primary' : 'btn-outline' }} btn-sm" style="padding: 6px 12px; font-size: 0.8rem;">Tahun Ini</a>
            </div>
            
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <input type="hidden" name="filter" value="custom">
                <input type="date" name="start_date" class="form-control" style="width: auto; padding: 5px 10px; font-size: 0.8rem; height: 32px;" value="{{ request('start_date') }}" required>
                <span style="color: var(--gray-600); font-size: 0.8rem;">s/d</span>
                <input type="date" name="end_date" class="form-control" style="width: auto; padding: 5px 10px; font-size: 0.8rem; height: 32px;" value="{{ request('end_date') }}" required>
                <button type="submit" class="btn btn-secondary btn-sm" style="padding: 6px 12px; font-size: 0.8rem; height: 32px; background-color: var(--dark); border-color: var(--dark); color: white;">Filter</button>
            </div>
        </form>
        @if(isset($filterLabel))
            <div style="margin-top: 10px; font-size: 0.8rem; color: var(--gray-600); font-weight: bold;">
                <i class="fa-solid fa-filter" style="color: var(--primary); margin-right: 4px;"></i> Periode Aktif: {{ $filterLabel }}
            </div>
        @endif
    </div>
</div>

<!-- Laporan Metrics Grid -->
<div class="grid-stats" style="margin-bottom: 25px;">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Buku Dipinjam (Periode)</h3>
            <p>{{ $totalBorrowCount }} Kali</p>
        </div>
        <div class="stat-icon red">
            <i class="fa-solid fa-shuffle"></i>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Jumlah Keterlambatan</h3>
            <p style="{{ $lateCount > 0 ? 'color: var(--primary);' : '' }}">{{ $lateCount }} Kali</p>
        </div>
        <div class="stat-icon red" style="background-color: rgba(var(--primary-rgb), 0.05);">
            <i class="fa-solid fa-clock"></i>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Buku Pengganti</h3>
            <p>{{ $totalFineAmount }} Buku</p>
        </div>
        <div class="stat-icon red">
            <i class="fa-solid fa-book-journal-whills"></i>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Buku Sudah Diganti</h3>
            <p style="color: var(--success);">{{ $paidFineAmount }} Buku</p>
        </div>
        <div class="stat-icon green" style="background-color: rgba(40,167,69,0.05);">
            <i class="fa-solid fa-circle-check"></i>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Buku Belum Diganti</h3>
            <p style="color: var(--primary);">{{ $unpaidFineAmount }} Buku</p>
        </div>
        <div class="stat-icon yellow">
            <i class="fa-solid fa-circle-exclamation"></i>
        </div>
    </div>
</div>

<!-- Charts Grid -->
<div class="dashboard-grid" style="margin-bottom: 30px;">
    <!-- Monthly Borrowing Trend -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-chart-line" style="color: var(--primary); margin-right: 8px;"></i> Tren Peminjaman per Bulan</h2>
        </div>
        <div class="card-body" style="position: relative; height: 300px;">
            <canvas id="monthlyTrendChart"></canvas>
        </div>
    </div>

    <!-- Inventory Availability (Donut) -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-chart-pie" style="color: var(--secondary); margin-right: 8px;"></i> Ketersediaan Koleksi Buku</h2>
        </div>
        <div class="card-body" style="position: relative; height: 300px; display: flex; justify-content: center; align-items: center;">
            <div style="width: 220px; height: 220px;">
                <canvas id="availabilityChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Logs Tabs/Tables -->
<div class="card" style="margin-bottom: 30px;">
    <div class="card-header">
        <h2><i class="fa-solid fa-users" style="color: var(--primary); margin-right: 8px;"></i> Anggota Terdaftar</h2>
        <span class="badge badge-success">{{ $totalMembers }} Terdaftar</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Kode Member</th>
                        <th>Nama Anggota</th>
                        <th>Email</th>
                        <th>Total Pinjam</th>
                        <th>Poin</th>
                        <th>Tanggal Gabung</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($membersList as $m)
                        <tr>
                            <td style="font-family: monospace; font-weight: 700; color: #b58b00;">{{ $m->member_code }}</td>
                            <td><strong>{{ $m->user->name }}</strong></td>
                            <td>{{ $m->user->email }}</td>
                            <td>{{ $m->total_loans }} kali</td>
                            <td><span class="badge badge-warning">{{ $m->points }} Pts</span></td>
                            <td>{{ $m->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Left Column: Overdue and Late Return Audit -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-triangle-exclamation" style="color: var(--primary); margin-right: 8px;"></i> Catatan Keterlambatan Pengembalian</h2>
        </div>
        <div class="card-body">
            <h4 style="font-size: 0.95rem; font-weight: 600; color: var(--dark); margin-bottom: 12px;">Peminjaman Terlambat Saat Ini (Overdue):</h4>
            @if($overdueBorrows->isEmpty())
                <p style="font-size: 0.85rem; color: var(--gray-600); margin-bottom: 25px;">Tidak ada keterlambatan aktif saat ini.</p>
            @else
                <div class="table-responsive" style="margin-bottom: 25px;">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Buku</th>
                                <th>Member</th>
                                <th>Jatuh Tempo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($overdueBorrows as $ob)
                                <tr>
                                    <td>{{ $ob->book->title }}</td>
                                    <td>{{ $ob->member->user->name }}</td>
                                    <td style="color: var(--primary); font-weight: 600;">{{ $ob->due_date->format('d M Y') }}</td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            <h4 style="font-size: 0.95rem; font-weight: 600; color: var(--dark); margin-bottom: 12px; border-top: 1px solid var(--gray-200); padding-top: 20px;">Riwayat Pengembalian Terlambat:</h4>
            @if(empty($returnedLateBorrows))
                <p style="font-size: 0.85rem; color: var(--gray-600);">Tidak ada catatan pengembalian terlambat.</p>
            @else
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Buku</th>
                                <th>Member</th>
                                <th>Tanggal Pengembalian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($returnedLateBorrows as $rl)
                                <tr>
                                    <td>{{ $rl->book->title }}</td>
                                    <td>{{ $rl->member->user->name }}</td>
                                    <td style="color: #b58b00; font-weight: 500;">{{ $rl->return_date->format('d M Y') }} (Late)</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Popular Books Ranking -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-fire" style="color: var(--secondary); margin-right: 8px;"></i> Peringkat Buku Paling Sering Dipinjam</h2>
        </div>
        <div class="card-body">
            @if($popularBooks->isEmpty())
                <p style="text-align: center; color: var(--gray-600); padding: 20px;">Belum ada peringkat data.</p>
            @else
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 15px;">
                    @foreach($popularBooks as $index => $popular)
                        <li style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid var(--gray-100);">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span style="font-size: 1.1rem; font-weight: 700; color: {{ $index == 0 ? 'var(--secondary)' : ($index == 1 ? 'var(--primary)' : 'var(--gray-600)') }};">
                                    #{{ $index + 1 }}
                                </span>
                                <div>
                                    <div style="font-weight: 600; color: var(--dark);">{{ $popular->book->title }}</div>
                                    <div style="font-size: 0.8rem; color: var(--gray-600);">{{ $popular->book->author }}</div>
                                </div>
                            </div>
                            <span class="badge badge-success" style="font-weight: 700;">{{ $popular->total }}x Pinjam</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>

<!-- Reward Member Stats Table -->
<div class="card" style="margin-top: 30px; margin-bottom: 25px;">
    <div class="card-header">
        <h2><i class="fa-solid fa-medal" style="color: #f1c40f; margin-right: 8px;"></i> Statistik Reward Member (Top 10 Poin Terbanyak)</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th style="width: 80px;">Peringkat</th>
                        <th>Nama Anggota</th>
                        <th>Kode Anggota</th>
                        <th>Total Peminjaman</th>
                        <th>Saldo Poin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($memberRewardStats as $index => $mr)
                        <tr>
                            <td>
                                <strong>#{{ $index + 1 }}</strong>
                            </td>
                            <td><strong>{{ $mr->user->name }}</strong></td>
                            <td style="font-family: monospace;">{{ $mr->member_code }}</td>
                            <td>{{ $mr->total_loans }} Kali</td>
                            <td>
                                <span class="badge badge-warning" style="font-weight: bold; font-size: 0.82rem; padding: 4px 8px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-star"></i> {{ $mr->points }} Poin
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Monthly Borrow Trend Chart
        const trendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
        
        // Prepare chart data from PHP controller variables
        const monthlyLabels = [
            @foreach($monthlyTrends as $month => $count)
                "{{ substr($month, 5) }}",
            @endforeach
        ];
        
        const monthlyData = [
            @foreach($monthlyTrends as $month => $count)
                {{ $count }},
            @endforeach
        ];

        // Ensure chart has data
        if (monthlyLabels.length === 0) {
            monthlyLabels.push("Belum ada");
            monthlyData.push(0);
        }

        const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#D62027';
        const primaryColorRgb = getComputedStyle(document.documentElement).getPropertyValue('--primary-rgb').trim() || '214, 32, 39';

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: monthlyData,
                    borderColor: primaryColor,
                    backgroundColor: `rgba(${primaryColorRgb}, 0.1)`,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // 2. Book Availability Donut Chart
        const availCtx = document.getElementById('availabilityChart').getContext('2d');
        new Chart(availCtx, {
            type: 'doughnut',
            data: {
                labels: ['Tersedia', 'Sedang Dipinjam'],
                datasets: [{
                    data: [{{ $availableBooks }}, {{ $borrowedBooksCount }}],
                    backgroundColor: ['#22c55e', primaryColor],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: {
                                family: 'Outfit'
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    });
</script>

<style>
    @media print {
        .sidebar, .header-nav, .btn, .card-header .btn {
            display: none !important;
        }
        .main-wrapper {
            margin-left: 0 !important;
        }
        .content-body {
            padding: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endsection
