@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('header_title', 'Dashboard Super Admin')

@section('content')
<<<<<<< HEAD
<div class="welcome-banner">
    <h1>Selamat Datang di Portal Super Admin</h1>
    <p>Akses penuh sistem perpustakaan Literawaslu. Kelola buku, data anggota, petugas, dan pantau laporan transaksi.</p>
=======
<div class="welcome-banner" style="display: flex; justify-content: space-between; align-items: center; gap: 20px;">
    <div style="position: relative; z-index: 5; flex: 1;">
        <h1>Selamat Datang di Portal Super Admin</h1>
        <p>Akses penuh sistem perpustakaan Literawaslu. Kelola buku, data anggota, petugas, dan pantau laporan transaksi.</p>
    </div>
    @if(auth()->user()->avatar)
        <div style="position: relative; z-index: 5;">
            <img src="{{ asset(auth()->user()->avatar) }}" alt="Foto Profil" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255,255,255,0.8); box-shadow: 0 8px 24px rgba(0,0,0,0.2);">
        </div>
    @endif
>>>>>>> origin/pr-1
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

@if($unverifiedMembers->isNotEmpty())
    <div class="card" style="margin-bottom: 25px; border: 1px solid rgba(227,30,36,0.15); box-shadow: 0 4px 20px rgba(227,30,36,0.05);">
        <div class="card-header" style="background-color: rgba(227,30,36,0.02); display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(227,30,36,0.1);">
            <h2 style="color: var(--primary); display: flex; align-items: center; gap: 8px; margin: 0; font-size: 1.1rem; font-weight: 700;">
                <i class="fa-solid fa-user-clock" style="color: var(--primary);"></i> Anggota Menunggu Verifikasi
            </h2>
            <span class="badge badge-danger" style="background-color: var(--primary); color: var(--light); font-weight: 700;">{{ $unverifiedMembers->count() }} Baru</span>
        </div>
        <div class="card-body" style="padding: 15px 20px;">
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Kode Member</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Tanggal Registrasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unverifiedMembers as $unverified)
                            <tr>
                                <td style="font-family: monospace; font-weight: 700; color: #b58b00;">{{ $unverified->member_code }}</td>
                                <td><strong>{{ $unverified->user->name }}</strong></td>
                                <td>{{ $unverified->user->email }}</td>
                                <td>{{ $unverified->created_at->format('d M Y') }}</td>
                                <td>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <form action="{{ route('members.verify', $unverified->id) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary btn-sm" style="background-color: var(--secondary); border-color: var(--secondary); color: var(--light); padding: 6px 12px; font-size: 0.8rem; border-radius: var(--border-radius); cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                                <i class="fa-solid fa-user-check"></i> Terima
                                            </button>
                                        </form>
                                        <form action="{{ route('members.reject', $unverified->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menolak dan menghapus pendaftaran member ini?');" style="margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn btn-outline btn-sm" style="color: var(--primary); border-color: rgba(227,30,36,0.3); padding: 6px 12px; font-size: 0.8rem; border-radius: var(--border-radius); cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                                <i class="fa-solid fa-user-xmark"></i> Tolak
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

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
