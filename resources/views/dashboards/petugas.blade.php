@extends('layouts.app')

@section('title', 'Dashboard Petugas')
@section('header_title', 'Dashboard Petugas')

@section('content')
<div class="welcome-banner" style="display: flex; justify-content: space-between; align-items: center; gap: 20px;">
    <div style="position: relative; z-index: 5; flex: 1;">
        <h1>Selamat Datang, {{ auth()->user()->name }}</h1>
        <p>Gunakan panel ini untuk mengelola aktivitas peminjaman buku, scan pengembalian, dan melihat laporan bulanan.</p>
    </div>

</div>

<!-- Stats Dashboard Grid -->
<div class="grid-stats">
    <a href="{{ route('books.index') }}" class="stat-card" style="text-decoration: none; color: inherit; cursor: pointer;">
        <div class="stat-info">
            <h3>Koleksi Buku</h3>
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



<!-- Quick Actions -->
<div class="card" style="margin-bottom: 25px;">
    <div class="card-header">
        <h2><i class="fa-solid fa-bolt" style="color: var(--secondary); margin-right: 8px;"></i> Pintasan Aktivitas Transaksi</h2>
    </div>
    <div class="card-body" style="padding: 20px; display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="{{ route('borrows.index') }}" class="btn btn-primary" style="flex: 1; min-width: 200px;">
            <i class="fa-solid fa-hand-holding-hand"></i> Peminjaman & Pengembalian
        </a>
        <a href="{{ route('members.index') }}" class="btn btn-secondary" style="flex: 1; min-width: 200px;">
            <i class="fa-solid fa-users"></i> Lihat Data Member
        </a>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary" style="flex: 1; min-width: 200px;">
            <i class="fa-solid fa-calendar-days"></i> Laporan Aktivitas Bulanan
        </a>
    </div>
</div>

<!-- Recent Transactions Log -->
<div class="card">
    <div class="card-header">
        <h2><i class="fa-solid fa-history" style="color: var(--dark); margin-right: 8px;"></i> Log Peminjaman Terakhir</h2>
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
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentBorrows as $borrow)
                            <tr>
                                <td>
                                    <div style="font-weight: 600;">{{ $borrow->book->title }}</div>
                                    <div style="font-size: 0.8rem; color: var(--gray-600); font-family: monospace;">{{ $borrow->book->barcode }}</div>
                                </td>
                                <td>{{ $borrow->member->user->name }}</td>
                                <td>{{ $borrow->borrow_date->format('d M Y') }}</td>
                                <td>{{ $borrow->due_date->format('d M Y') }}</td>
                                <td>
                                    @if($borrow->status === 'returned')
                                        <span class="badge badge-success">Selesai</span>
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
@endsection
