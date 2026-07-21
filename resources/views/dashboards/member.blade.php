@extends('layouts.app')

@section('title', 'Dashboard Member')
@section('header_title', 'Dashboard')

@section('content')
<div class="welcome-banner" style="display: flex; justify-content: space-between; align-items: center; gap: 20px;">
    <div style="position: relative; z-index: 5; flex: 1;">
        <h1>Halo, {{ auth()->user()->name }}!</h1>
        <p>Selamat datang kembali di Perpustakaan Literawaslu. Mari temukan buku favorit Anda hari ini.</p>
        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <a href="{{ route('catalog') }}" class="btn btn-primary btn-sm" style="background-color: var(--light); color: var(--primary);"><i class="fa-solid fa-magnifying-glass"></i> Jelajah Katalog</a>
            <a href="{{ route('member.card') }}" class="btn btn-secondary btn-sm" style="background-color: transparent; border: 1px solid var(--light); color: var(--light);"><i class="fa-solid fa-id-card"></i> Tampilkan Kartu</a>
        </div>
    </div>

    <div style="position: absolute; right: -50px; bottom: -50px; font-size: 15rem; color: rgba(255,255,255,0.05); transform: rotate(-15deg); pointer-events: none;">
        <i class="fa-solid fa-book"></i>
    </div>
</div>

<!-- Stats Dashboard Grid -->
<div class="grid-stats">
    <a href="{{ route('catalog') }}" class="stat-card" style="text-decoration: none; color: inherit; cursor: pointer;">
        <div class="stat-info">
            <h3>Koleksi Tersedia</h3>
            <p>{{ $availableBooksCount }} Buku</p>
        </div>
        <div class="stat-icon red">
            <i class="fa-solid fa-book"></i>
        </div>
    </a>
    
    <a href="{{ route('member.history') }}" class="stat-card" style="text-decoration: none; color: inherit; cursor: pointer;">
        <div class="stat-info">
            <h3>Total Peminjaman</h3>
            <p>{{ $totalBorrows }} Kali</p>
        </div>
        <div class="stat-icon black">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
    </a>
    
    <a href="{{ route('member.rewards') }}" class="stat-card" style="text-decoration: none; color: inherit; cursor: pointer;">
        <div class="stat-info">
            <h3>Poin Reward</h3>
            <p>{{ $member->points }} Poin</p>
        </div>
        <div class="stat-icon yellow">
            <i class="fa-solid fa-award"></i>
        </div>
    </a>
</div>

<div class="dashboard-grid">
    <!-- Left Column: Active Borrowings -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-hand-holding-hand" style="color: var(--primary); margin-right: 8px;"></i> Buku yang Sedang Dipinjam</h2>
            <span class="badge badge-warning">{{ $activeBorrows->count() }} Sedang Dipinjam</span>
        </div>
        <div class="card-body">
            @if($activeBorrows->isEmpty())
                <div style="text-align: center; padding: 40px 20px; color: var(--gray-600);">
                    <i class="fa-solid fa-circle-check" style="font-size: 2.5rem; color: #22c55e; margin-bottom: 15px;"></i>
                    <p style="font-weight: 500;">Anda tidak memiliki peminjaman aktif saat ini.</p>
                    <p style="font-size: 0.85rem; margin-top: 5px;">Silakan datangi petugas perpustakaan untuk meminjam buku.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Jatuh Tempo</th>
                                <th>Sisa Hari</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeBorrows as $borrow)
                                @php
                                    $due = \Carbon\Carbon::parse($borrow->due_date);
                                    $now = \Carbon\Carbon::now()->startOfDay();
                                    $diff = $now->diffInDays($due, false);
                                @endphp
                                <tr>
                                    <td>
                                        <div style="font-weight: 600; color: var(--dark);">{{ $borrow->book->title }}</div>
                                        <div style="font-size: 0.8rem; color: var(--gray-600);">{{ $borrow->book->author }}</div>
                                    </td>
                                    <td>{{ $borrow->borrow_date->format('d M Y') }}</td>
                                    <td style="{{ $diff < 0 ? 'color: var(--primary); font-weight: 600;' : '' }}">
                                        {{ $borrow->due_date->format('d M Y') }}
                                    </td>
                                    <td>
                                        @if($diff < 0)
                                            <span class="badge badge-danger">Terlambat {{ abs($diff) }} Hari</span>
                                        @elseif($diff == 0)
                                            <span class="badge badge-warning">Hari Ini!</span>
                                        @else
                                            <span class="badge badge-success">{{ $diff }} Hari Lagi</span>
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

    <!-- Right Column: Digital Card Quick View -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-id-card" style="color: var(--secondary); margin-right: 8px;"></i> Kartu Anggota Digital</h2>
        </div>
        <div class="card-body" style="padding: 20px;">
            <div class="digital-card-container">
                <!-- CARD FRONT (Matched with member_card.blade.php) -->
                <div class="card-front" style="position: relative; width: 100%; height: 260px; background: #b1b5b9 !important; color: #1A1A1A !important; border: 1px solid rgba(0,0,0,0.1); border-radius: 16px; padding: 25px; display: flex; flex-direction: column; justify-content: space-between; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
                    <!-- Elegant Inner Dashed Border Frame -->
                    <div style="position: absolute; top: 10px; left: 10px; right: 10px; bottom: 10px; border: 1px dashed rgba(26,26,26,0.15); border-radius: 12px; pointer-events: none; z-index: 2;"></div>
                    
                    <!-- Shiny Reflection Effect -->
                    <div style="position: absolute; top: -50%; right: -20%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 60%); border-radius: 50%; pointer-events: none;"></div>
                    
                    <!-- Center Watermark Logo Bawaslu -->
                    <img src="{{ asset('images/logo-bawaslu.png') }}" alt="Watermark Bawaslu" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); height: 150px; width: auto; opacity: 0.06; pointer-events: none; z-index: 1; filter: brightness(0);">
                    
                    <div class="digital-card-header" style="display: flex; justify-content: space-between; align-items: flex-start; z-index: 5;">
                        <div class="card-logo" style="display: flex; align-items: center; gap: 10px;">
                            <img src="{{ asset('images/logo-bawaslu.png') }}" alt="Logo Bawaslu" style="height: 52px; width: auto; object-fit: contain;">
                            <div style="font-size: 1.45rem; font-weight: 800; color: #1A1A1A; line-height: 1; font-family: 'Montserrat', sans-serif; letter-spacing: 0.5px;">
                                Literawaslu
                            </div>
                        </div>
                    </div>
                    
                    <div class="digital-card-body" style="margin-top: 10px; z-index: 5; display: flex; align-items: center; gap: 15px;">
                        <div>
                            <div class="member-name" style="font-size: 1.6rem; font-weight: 700; color: #1A1A1A; font-family: 'Montserrat', sans-serif; letter-spacing: 0.5px;">
                                {{ auth()->user()->name }}
                            </div>
                            <div class="member-id" style="font-size: 1.35rem; color: #1A1A1A; margin-top: 5px; font-family: monospace; letter-spacing: 2px; font-weight: bold;">
                                {{ $member->member_code }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="digital-card-footer" style="margin-top: 5px; display: flex; justify-content: space-between; align-items: flex-end; z-index: 5;">
                        <div class="card-info-item">
                            <label style="font-size: 0.68rem; text-transform: uppercase; color: rgba(0,0,0,0.55); display: block; letter-spacing: 1px; font-weight: 700; margin-bottom: 2px;">Berlaku Sampai</label>
                            <span style="font-size: 1rem; font-weight: 700; color: #1A1A1A; font-family: 'Montserrat', sans-serif;">{{ strtoupper($member->created_at->addYear(1)->locale('id')->translatedFormat('d F Y')) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div style="margin-top: 15px; text-align: center;">
                <a href="{{ route('member.card') }}" class="btn btn-outline btn-sm" style="width: 100%;">
                    <i class="fa-solid fa-expand"></i> Lihat Detail Kartu
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
