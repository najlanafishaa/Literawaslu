@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('header_title', 'Dashboard Super Admin')

@section('content')
<div class="welcome-banner">
    <h1>Selamat Datang di Portal Super Admin</h1>
    <p>Akses penuh sistem perpustakaan Literawaslu. Kelola buku, data anggota, petugas, dan pantau laporan transaksi.</p>
</div>

<!-- Date Filter Panel -->
<div class="card" style="margin-bottom: 25px; margin-top: 25px;">
    <div class="card-body" style="padding: 15px 20px;">
        <form action="{{ route('dashboard') }}" method="GET" style="display: flex; justify-content: space-between; align-items: center; gap: 15px; flex-wrap: wrap; margin: 0;">
            <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                <span style="font-size: 0.85rem; font-weight: 700; color: var(--gray-700); margin-right: 5px;"><i class="fa-solid fa-calendar-days"></i> Filter Waktu:</span>
                <a href="{{ route('dashboard', ['filter' => 'all']) }}" class="btn {{ request('filter', 'all') === 'all' ? 'btn-primary' : 'btn-outline' }} btn-sm" style="padding: 6px 12px; font-size: 0.8rem;">Semua</a>
                <a href="{{ route('dashboard', ['filter' => 'today']) }}" class="btn {{ request('filter') === 'today' ? 'btn-primary' : 'btn-outline' }} btn-sm" style="padding: 6px 12px; font-size: 0.8rem;">Hari Ini</a>
                <a href="{{ route('dashboard', ['filter' => 'week']) }}" class="btn {{ request('filter') === 'week' ? 'btn-primary' : 'btn-outline' }} btn-sm" style="padding: 6px 12px; font-size: 0.8rem;">Minggu Ini</a>
                <a href="{{ route('dashboard', ['filter' => 'month']) }}" class="btn {{ request('filter') === 'month' ? 'btn-primary' : 'btn-outline' }} btn-sm" style="padding: 6px 12px; font-size: 0.8rem;">Bulan Ini</a>
                <a href="{{ route('dashboard', ['filter' => 'year']) }}" class="btn {{ request('filter') === 'year' ? 'btn-primary' : 'btn-outline' }} btn-sm" style="padding: 6px 12px; font-size: 0.8rem;">Tahun Ini</a>
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

<!-- Stats Dashboard Grid -->
<div class="grid-stats">
    <a href="{{ route('books.index') }}" class="stat-card" style="text-decoration: none; color: inherit; cursor: pointer;">
        <div class="stat-info">
            <h3>Total Koleksi Buku</h3>
            <p>{{ $totalBooks }} Buku</p>
        </div>
        <div class="stat-icon red">
            <i class="fa-solid fa-book"></i>
        </div>
    </a>
    
    <a href="{{ route('borrows.index') }}" class="stat-card" style="text-decoration: none; color: inherit; cursor: pointer;">
        <div class="stat-info">
            <h3>Sedang Dipinjam</h3>
            <p>{{ $borrowedBooks }} Buku</p>
        </div>
        <div class="stat-icon black">
            <i class="fa-solid fa-hand-holding-hand"></i>
        </div>
    </a>
    
    <a href="{{ route('members.index') }}" class="stat-card" style="text-decoration: none; color: inherit; cursor: pointer;">
        <div class="stat-info">
            <h3>Anggota Terdaftar</h3>
            <p>{{ $totalMembers }} Member</p>
        </div>
        <div class="stat-icon yellow">
            <i class="fa-solid fa-users"></i>
        </div>
    </a>
    
    <a href="{{ route('borrows.index') }}" class="stat-card" style="text-decoration: none; color: inherit; cursor: pointer;">
        <div class="stat-info">
            <h3>Keterlambatan</h3>
            <p style="{{ $overdueCount > 0 ? 'color: var(--primary);' : '' }}">{{ $overdueCount }} Transaksi</p>
        </div>
        <div class="stat-icon red" style="background-color: rgba(var(--primary-rgb), 0.05);">
            <i class="fa-solid fa-circle-exclamation"></i>
        </div>
    </a>
</div>

<!-- Stats Sanksi Grid -->
<div class="grid-stats" style="margin-top: 20px;">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Buku Pengganti</h3>
            <p>{{ $totalFine }} Buku</p>
        </div>
        <div class="stat-icon red">
            <i class="fa-solid fa-book-journal-whills"></i>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-info">
            <h3>Belum Diganti</h3>
            <p style="color: var(--primary);">{{ $unpaidFine }} Buku</p>
        </div>
        <div class="stat-icon yellow">
            <i class="fa-solid fa-circle-exclamation"></i>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-info">
            <h3>Sudah Diganti</h3>
            <p style="color: var(--success);">{{ $paidFine }} Buku</p>
        </div>
        <div class="stat-icon green" style="background-color: rgba(40,167,69,0.05);">
            <i class="fa-solid fa-circle-check" style="color: var(--success);"></i>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-info">
            <h3>Transaksi Periode Ini</h3>
            <p>{{ $totalTransactions }} Transaksi</p>
        </div>
        <div class="stat-icon black" style="background-color: rgba(0,0,0,0.05);">
            <i class="fa-solid fa-shuffle"></i>
        </div>
    </div>
</div>



<!-- Admin Quick Action Links -->
<div class="card" style="margin-bottom: 25px;">
    <div class="card-header">
        <h2><i class="fa-solid fa-compass" style="color: var(--primary); margin-right: 8px;"></i> Navigasi Pintar Kelola Data</h2>
    </div>
    <div class="card-body" style="padding: 20px; display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="{{ route('books.index') }}" class="btn btn-secondary" style="flex: 1; min-width: 150px;">
            <i class="fa-solid fa-book"></i> Kelola Data Buku
        </a>
        <a href="{{ route('members.index') }}" class="btn btn-secondary" style="flex: 1; min-width: 150px;">
            <i class="fa-solid fa-users"></i> Kelola Data Member
        </a>
        <a href="{{ route('officers.index') }}" class="btn btn-secondary" style="flex: 1; min-width: 150px;">
            <i class="fa-solid fa-user-shield"></i> Kelola Data Petugas
        </a>
        <a href="{{ route('reports.index') }}" class="btn btn-primary" style="flex: 1; min-width: 150px;">
            <i class="fa-solid fa-file-invoice-dollar"></i> Cetak & Lihat Laporan
        </a>
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
                <div class="table-responsive">
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
            <h2><i class="fa-solid fa-fire" style="color: var(--secondary); margin-right: 8px;"></i> Buku Terpopuler (Paling Sering Dipinjam)</h2>
        </div>
        <div class="card-body">
            @if($popularBooks->isEmpty())
                <p style="text-align: center; color: var(--gray-600); padding: 20px;">Belum ada data peminjaman populer.</p>
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
@endsection
