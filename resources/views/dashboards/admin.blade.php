@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('header_title', 'Dashboard Super Admin')

@section('content')
<div class="welcome-banner" style="display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap;">
    <div style="position: relative; z-index: 5; flex: 1; min-width: 220px;">
        <h1>Selamat Datang di Portal Super Admin</h1>
        <p>Akses penuh sistem perpustakaan Literawaslu. Kelola buku, data anggota, petugas, dan pantau laporan transaksi.</p>
    </div>
</div>

<!-- Stats Dashboard Grid -->
<div class="grid-stats" style="margin-bottom: 25px;">
    <a href="{{ route('books.index') }}" class="stat-card" style="text-decoration: none; color: inherit; cursor: pointer;">
        <div class="stat-info">
            <h3>Total Buku</h3>
            <p>{{ $totalBooks }} Buku</p>
        </div>
        <div class="stat-icon red">
            <i class="fa-solid fa-book"></i>
        </div>
    </a>
    
    <a href="{{ route('members.index') }}" class="stat-card" style="text-decoration: none; color: inherit; cursor: pointer;">
        <div class="stat-info">
            <h3>Total Member</h3>
            <p>{{ $totalMembers }} Member</p>
        </div>
        <div class="stat-icon yellow">
            <i class="fa-solid fa-users"></i>
        </div>
    </a>
    
    <a href="{{ route('borrows.history') }}" class="stat-card" style="text-decoration: none; color: inherit; cursor: pointer;">
        <div class="stat-info">
            <h3>Total Peminjaman</h3>
            <p>{{ $totalTransactions }} Transaksi</p>
        </div>
        <div class="stat-icon black">
            <i class="fa-solid fa-hand-holding-hand"></i>
        </div>
    </a>
    
    <a href="{{ route('borrows.history') }}" class="stat-card" style="text-decoration: none; color: inherit; cursor: pointer;">
        <div class="stat-info">
            <h3>Total Pengembalian</h3>
            <p style="color: #16a34a;">{{ $totalReturns ?? 0 }} Selesai</p>
        </div>
        <div class="stat-icon green" style="background-color: rgba(34, 197, 94, 0.1);">
            <i class="fa-solid fa-circle-check"></i>
        </div>
    </a>

    <a href="{{ route('borrows.history') }}" class="stat-card" style="text-decoration: none; color: inherit; cursor: pointer;">
        <div class="stat-info">
            <h3>Jumlah Buku Terlambat</h3>
            <p style="{{ $overdueCount > 0 ? 'color: var(--primary);' : '' }}">{{ $overdueCount }} Transaksi</p>
        </div>
        <div class="stat-icon red" style="background-color: rgba(var(--primary-rgb), 0.05);">
            <i class="fa-solid fa-circle-exclamation"></i>
        </div>
    </a>
</div>

<!-- Monthly Borrowing Trend Chart Card -->
<div class="card" style="margin-bottom: 25px;">
    <div class="card-header">
        <h2><i class="fa-solid fa-chart-line" style="color: var(--primary); margin-right: 8px;"></i> Tren Peminjaman per Bulan</h2>
    </div>
    <div class="card-body" style="position: relative; height: 300px;">
        <canvas id="dashboardMonthlyChart"></canvas>
    </div>
</div>

<!-- Admin Quick Action Links -->
<div class="card" style="margin-bottom: 25px;">
    <div class="card-header">
        <h2><i class="fa-solid fa-compass" style="color: var(--primary); margin-right: 8px;"></i> Navigasi Pintar Kelola Data</h2>
    </div>
    <div class="card-body" style="padding: 16px 20px; display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="{{ route('books.index') }}" class="btn btn-secondary" style="flex: 1; min-width: 140px;">
            <i class="fa-solid fa-book"></i> Kelola Data Buku
        </a>
        <a href="{{ route('members.index') }}" class="btn btn-secondary" style="flex: 1; min-width: 140px;">
            <i class="fa-solid fa-users"></i> Kelola Data Member
        </a>
        <a href="{{ route('officers.index') }}" class="btn btn-secondary" style="flex: 1; min-width: 140px;">
            <i class="fa-solid fa-user-shield"></i> Kelola Data Petugas
        </a>
        <a href="{{ route('reports.index') }}" class="btn btn-primary" style="flex: 1; min-width: 140px;">
            <i class="fa-solid fa-file-invoice-dollar"></i> Cetak &amp; Lihat Laporan
        </a>
    </div>
</div>

<!-- Online Borrow Approval Section -->
<div class="card" style="margin-bottom: 25px;">
    <div class="card-header" style="background-color: var(--secondary); color: white; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <h2 style="color: white; margin: 0;"><i class="fa-solid fa-list-check" style="margin-right: 8px;"></i> Daftar Pengajuan Peminjaman Online</h2>
        <span class="badge badge-light" style="color: var(--dark); font-weight: bold;">{{ isset($pendingBorrowsList) ? $pendingBorrowsList->count() : 0 }} Pengajuan Menunggu</span>
    </div>
    <div class="card-body">
        @if(!isset($pendingBorrowsList) || $pendingBorrowsList->isEmpty())
            <p style="text-align: center; color: var(--gray-600); padding: 20px;">Tidak ada pengajuan peminjaman online yang perlu disetujui saat ini.</p>
        @else
            <div class="table-responsive" style="-webkit-overflow-scrolling: touch;">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Nama Member</th>
                            <th>Judul Buku</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Status Pengajuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingBorrowsList as $borrow)
                            <tr>
                                <td>
                                    <strong style="color: var(--dark);">{{ $borrow->member->user->name }}</strong>
                                    <div style="font-size: 0.8rem; color: #b58b00; font-weight: 600;">{{ $borrow->member->member_code }}</div>
                                </td>
                                <td>
                                    <strong style="color: var(--primary);">{{ $borrow->book->title }}</strong>
                                    <div style="font-size: 0.78rem; color: var(--gray-600);">Barcode: {{ $borrow->book->barcode }} &bull; Stok: {{ $borrow->book->available_stock }}</div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($borrow->borrow_date)->format('d M Y, H:i') }}</td>
                                <td>
                                    <span class="badge badge-warning" style="background-color: #fef08a; color: #ca8a04;"><i class="fa-solid fa-clock"></i> Menunggu Persetujuan</span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                        <form action="{{ route('verifications.borrow.approve', $borrow->id) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm" style="background-color: #22c55e; color: white; padding: 6px 12px; font-weight: 600;">
                                                <i class="fa-solid fa-check"></i> Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('verifications.borrow.reject', $borrow->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menolak pengajuan ini?');" style="margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm" style="background-color: var(--primary); color: white; padding: 6px 12px; font-weight: 600;">
                                                <i class="fa-solid fa-xmark"></i> Tolak
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="dashboard-grid">
    <!-- Left Column: Recent Borrowing Log -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-clock-rotate-left" style="color: var(--primary); margin-right: 8px;"></i> Transaksi Peminjaman Terbaru</h2>
        </div>
        <div class="card-body">
            @if($recentBorrows->isEmpty())
                <p style="text-align: center; color: var(--gray-600); padding: 20px;">Belum ada riwayat transaksi peminjaman.</p>
            @else
                <div class="table-responsive" style="-webkit-overflow-scrolling: touch;">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Buku</th>
                                <th>Peminjam</th>
                                <th>Tanggal Pinjam</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBorrows as $borrow)
                                <tr>
                                    <td>{{ $borrow->book->title }}</td>
                                    <td>{{ $borrow->member->user->name }}</td>
                                    <td>{{ $borrow->borrow_date->format('d M Y') }}</td>
                                    <td>
                                        @if($borrow->status === 'returned')
                                            <span class="badge badge-success">Dikembalikan</span>
                                        @else
                                            <span class="badge badge-warning">Dipinjam</span>
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

    <!-- Right Column: Most Borrowed Books -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-fire" style="color: var(--secondary); margin-right: 8px;"></i> Buku Terpopuler</h2>
        </div>
        <div class="card-body">
            @if($popularBooks->isEmpty())
                <p style="text-align: center; color: var(--gray-600); padding: 20px;">Belum ada data peminjaman populer.</p>
            @else
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 15px; padding: 0; margin: 0;">
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const monthlyData = @json($monthlyTrends ?? []);
        const labels = Object.keys(monthlyData);
        const dataValues = Object.values(monthlyData);

        const ctx = document.getElementById('dashboardMonthlyChart');
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

