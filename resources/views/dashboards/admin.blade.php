@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('header_title', 'Dashboard Super Admin')

@section('content')
<div class="welcome-banner">
    <h1>Selamat Datang di Portal Super Admin</h1>
    <p>Akses penuh sistem perpustakaan Literawaslu. Kelola buku, data anggota, petugas, dan pantau laporan transaksi.</p>
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
