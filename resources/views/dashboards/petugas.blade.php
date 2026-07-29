@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('header_title', 'Dashboard Admin')

@section('content')
<div class="welcome-banner">
    <div style="position:relative; z-index:5; flex:1; min-width:200px;">
        <h1>Selamat Datang, {{ auth()->user()->name }}</h1>
        <p>Gunakan panel ini untuk mengelola aktivitas peminjaman buku, scan pengembalian, dan melihat laporan bulanan.</p>
    </div>
</div>

<!-- Stats Dashboard Grid -->
<div class="grid-stats" style="margin-bottom:20px;">
    <a href="{{ route('books.index') }}" class="stat-card card-red" style="text-decoration:none; cursor:pointer;">
        <div class="stat-info">
            <h3>Total Buku</h3>
            <p>{{ $totalBooks }} Buku</p>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-book-open"></i></div>
    </a>
    <a href="{{ route('members.index') }}" class="stat-card card-dark" style="text-decoration:none; cursor:pointer;">
        <div class="stat-info">
            <h3>Total Pengguna</h3>
            <p>{{ $totalMembers }} Pengguna</p>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
    </a>
    <a href="{{ route('borrows.index') }}" class="stat-card card-yellow" style="text-decoration:none; cursor:pointer;">
        <div class="stat-info">
            <h3>Total Peminjaman</h3>
            <p>{{ $totalTransactions }} Transaksi</p>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-hand-holding-hand"></i></div>
    </a>
    <a href="{{ route('borrows.index') }}" class="stat-card card-green" style="text-decoration:none; cursor:pointer;">
        <div class="stat-info">
            <h3>Total Pengembalian</h3>
            <p>{{ $totalReturns ?? 0 }} Selesai</p>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-arrow-right-to-bracket"></i></div>
    </a>
    <a href="{{ route('borrows.index') }}" class="stat-card card-red" style="text-decoration:none; cursor:pointer;">
        <div class="stat-info">
            <h3>Buku Terlambat</h3>
            <p style="{{ $overdueCount > 0 ? 'color: #d62027;' : '' }}">{{ $overdueCount }} Transaksi</p>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-hourglass-end"></i></div>
    </a>
</div>

<!-- Monthly Borrowing Trend Chart Card -->
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <h2><i class="fa-solid fa-chart-line" style="color:var(--primary); margin-right:8px;"></i> Tren Peminjaman per Bulan</h2>
    </div>
    <div class="card-body" style="position:relative; height:240px;">
        <canvas id="petugasMonthlyChart"></canvas>
    </div>
</div>

<!-- Quick Actions -->
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <h2><i class="fa-solid fa-bolt" style="color:var(--secondary); margin-right:8px;"></i> Pintasan Aktivitas Transaksi</h2>
    </div>
    <div class="card-body" style="display:flex; gap:12px; flex-wrap:wrap;">
        <a href="{{ route('borrows.index') }}" class="btn btn-primary" style="flex:1; min-width:140px;">
            <i class="fa-solid fa-hand-holding-hand"></i> Peminjaman &amp; Pengembalian
        </a>
        <a href="{{ route('members.index') }}" class="btn btn-secondary" style="flex:1; min-width:140px;">
            <i class="fa-solid fa-users"></i> Lihat Data Pengguna
        </a>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary" style="flex:1; min-width:140px;">
            <i class="fa-solid fa-calendar-days"></i> Laporan Bulanan
        </a>
    </div>
</div>

<!-- Recent Transactions Log -->
<div class="card">
    <div class="card-header">
        <h2><i class="fa-solid fa-history" style="color:var(--dark); margin-right:8px;"></i> Log Peminjaman Terakhir</h2>
    </div>
    <div class="card-body" style="padding:0;">
        @if($recentBorrows->isEmpty())
            <p style="text-align:center; color:var(--gray-600); padding:30px;">Belum ada riwayat transaksi peminjaman.</p>
        @else
            <div class="table-responsive" style="-webkit-overflow-scrolling:touch;">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Buku</th>
                            <th>Peminjam</th>
                            <th>Tgl Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentBorrows as $borrow)
                            <tr>
                                <td>
                                    <div style="font-weight:600;">{{ $borrow->book->title }}</div>
                                    <div style="font-size:0.78rem; color:var(--gray-600); font-family:monospace;">{{ $borrow->book->barcode }}</div>
                                </td>
                                <td>{{ $borrow->member->user->name }}</td>
                                <td>{{ $borrow->borrow_date->format('d M Y') }}</td>
                                <td>{{ $borrow->due_date->format('d M Y') }}</td>
                                <td>
                                    @if($borrow->status === 'returned')
                                        <span class="badge badge-success">Selesai</span>
                                    @elseif($borrow->status === 'borrowed')
                                        <span class="badge badge-warning">Dipinjam</span>
                                    @elseif($borrow->status === 'pending')
                                        <span class="badge badge-info" style="background:rgba(59,130,246,.12);color:#1d4ed8;border:1px solid #bfdbfe;">Menunggu</span>
                                    @elseif($borrow->status === 'terlambat')
                                        <span class="badge badge-danger">Terlambat</span>
                                    @elseif($borrow->status === 'rejected')
                                        <span class="badge badge-danger">Ditolak</span>
                                    @else
                                        <span class="badge badge-warning">{{ ucfirst($borrow->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const monthlyData = @json($monthlyTrends ?? []);
        const labels = Object.keys(monthlyData);
        const dataValues = Object.values(monthlyData);

        const ctx = document.getElementById('petugasMonthlyChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels.length > 0 ? labels : ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    datasets: [{
                        label: 'Jumlah Peminjaman Buku',
                        data: dataValues.length > 0 ? dataValues : [0, 0, 0, 0, 0, 0],
                        borderColor: '#D62027',
                        backgroundColor: 'rgba(214, 32, 39, 0.08)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#F5B025',
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
@endsection
