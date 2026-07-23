@extends('layouts.app')

@section('title', 'Dashboard Member')
@section('header_title', 'Dashboard')

@section('content')
<div class="welcome-banner" style="display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap;">
    <div style="position: relative; z-index: 5; flex: 1; min-width: 220px;">
        <h1>Halo, {{ auth()->user()->name }}!</h1>
        <p>Selamat datang kembali di Perpustakaan Literawaslu. Mari temukan buku favorit Anda hari ini.</p>
        <div style="margin-top: 16px; display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('catalog') }}" class="btn btn-primary btn-sm" style="background-color: var(--light); color: var(--primary); font-weight: 600;"><i class="fa-solid fa-magnifying-glass"></i> Jelajah Katalog</a>
            <a href="{{ route('member.card') }}" class="btn btn-secondary btn-sm" style="background-color: transparent; border: 1px solid var(--light); color: var(--light); font-weight: 600;"><i class="fa-solid fa-id-card"></i> Tampilkan Kartu</a>
        </div>
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
    
    <div class="stat-card" style="cursor: pointer;" onclick="openQuotaModal()">
        <div class="stat-info">
            <h3>Sisa Kuota Pinjam Online</h3>
            <p style="color: {{ ($remainingQuota ?? 3) > 0 ? '#16a34a' : 'var(--primary)' }};">
                {{ $remainingQuota ?? 3 }} / 3 Buku
            </p>
            <small style="font-size: 0.75rem; color: var(--gray-600);"><i class="fa-solid fa-circle-info"></i> Klik untuk rincian kuota</small>
        </div>
        <div class="stat-icon black">
            <i class="fa-solid fa-layer-group"></i>
        </div>
    </div>
    
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

<!-- Online Borrowing Section on Member Dashboard -->
<div class="card" style="margin-bottom: 25px;">
    <div class="card-header" style="background-color: var(--primary); color: white; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <h2 style="color: white; margin: 0;"><i class="fa-solid fa-laptop-code" style="margin-right: 8px;"></i> Peminjaman Buku Online</h2>
        <a href="{{ route('catalog') }}" class="btn btn-sm" style="background-color: white; color: var(--primary); font-weight: bold; border-radius: 20px;">
            <i class="fa-solid fa-plus-circle"></i> Ajukan Peminjaman via Katalog
        </a>
    </div>
    <div class="card-body">
        <div style="background-color: rgba(66, 133, 244, 0.08); border: 1px solid rgba(66, 133, 244, 0.2); padding: 15px; border-radius: var(--border-radius); margin-bottom: 20px; font-size: 0.85rem; color: var(--gray-700);">
            <i class="fa-solid fa-circle-info" style="color: #4285F4; margin-right: 6px;"></i>
            <strong>Ketentuan Peminjaman Online:</strong> Maksimal <strong>3 buku</strong> sekaligus. Masa berlaku peminjaman adalah <strong>7 hari</strong>. Pengajuan wajib disetujui Admin sebelum aktif.
        </div>


        <h4 style="font-size: 1rem; font-weight: 700; color: var(--dark); margin-bottom: 12px;">Daftar Pengajuan & Status Peminjaman Online Anda:</h4>
        @if(!isset($onlineBorrowRequests) || $onlineBorrowRequests->isEmpty())
            <div style="text-align: center; padding: 30px 20px; color: var(--gray-600);">
                <i class="fa-solid fa-book-open" style="font-size: 2.5rem; color: var(--gray-300); margin-bottom: 10px;"></i>
                <p style="font-weight: 600;">Belum ada riwayat pengajuan peminjaman online.</p>
                <a href="{{ route('catalog') }}" class="btn btn-primary btn-sm" style="margin-top: 10px; display: inline-block;">Pilih Buku dari Katalog</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Buku</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Jatuh Tempo (7 Hari)</th>
                            <th>Status Pengajuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($onlineBorrowRequests as $req)
                            <tr>
                                <td>
                                    <strong style="color: var(--dark);">{{ $req->book->title }}</strong>
                                    <div style="font-size: 0.8rem; color: var(--gray-600);">Oleh: {{ $req->book->author }}</div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($req->borrow_date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($req->due_date)->format('d M Y') }}</td>
                                <td>
                                    @if($req->status === 'pending')
                                        <span class="badge badge-warning" style="background-color: #fef3c7; color: #b45309; padding: 6px 10px; font-weight: 600;"><i class="fa-solid fa-clock"></i> Menunggu Verifikasi</span>
                                    @elseif($req->status === 'borrowed')
                                        <span class="badge badge-success" style="background-color: #dcfce7; color: #16a34a; padding: 6px 10px;"><i class="fa-solid fa-circle-check"></i> Disetujui (Sedang Dipinjam)</span>
                                    @elseif($req->status === 'returned')
                                        <span class="badge badge-secondary" style="padding: 6px 10px;"><i class="fa-solid fa-box-archive"></i> Selesai (Dikembalikan)</span>
                                    @elseif($req->status === 'rejected')
                                        <span class="badge badge-danger" style="background-color: #fee2e2; color: #dc2626; padding: 6px 10px;"><i class="fa-solid fa-circle-xmark"></i> Ditolak</span>
                                    @else
                                        <span class="badge badge-danger" style="padding: 6px 10px;">{{ ucfirst($req->status) }}</span>
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

<!-- Modal Rincian Kuota Pinjam Online -->
<div id="quotaModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background-color: white; border-radius: var(--border-radius); max-width: 550px; width: 100%; box-shadow: 0 10px 25px rgba(0,0,0,0.3); overflow: hidden; animation: fadeIn 0.2s ease;">
        <div style="background-color: var(--dark); color: white; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;"><i class="fa-solid fa-layer-group" style="color: var(--secondary); margin-right: 8px;"></i> Rincian Kuota Peminjaman Online</h3>
            <button type="button" onclick="closeQuotaModal()" style="background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div style="padding: 24px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div style="background-color: var(--gray-50); border: 1px solid var(--gray-200); border-radius: 10px; padding: 15px; text-align: center;">
                    <div style="font-size: 0.8rem; color: var(--gray-600); font-weight: 600;">KUOTA TERPAKAII</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: var(--primary); margin-top: 4px;">{{ $activeBorrows->count() }} Buku</div>
                </div>
                <div style="background-color: var(--gray-50); border: 1px solid var(--gray-200); border-radius: 10px; padding: 15px; text-align: center;">
                    <div style="font-size: 0.8rem; color: var(--gray-600); font-weight: 600;">SISA KUOTA</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: #16a34a; margin-top: 4px;">{{ $remainingQuota ?? 3 }} Buku</div>
                </div>
            </div>

            <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--dark); margin-bottom: 10px;"><i class="fa-solid fa-book" style="color: var(--primary); margin-right: 6px;"></i> Daftar Buku Sedang Dipinjam:</h4>
            @if($activeBorrows->isEmpty())
                <p style="font-size: 0.85rem; color: var(--gray-600); text-align: center; padding: 15px 0;">Tidak ada buku yang sedang dipinjam saat ini.</p>
            @else
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px;">
                    @foreach($activeBorrows as $b)
                        <li style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background-color: var(--gray-50); border-radius: 8px; border-left: 4px solid var(--primary);">
                            <div>
                                <strong style="font-size: 0.9rem; color: var(--dark); display: block;">{{ $b->book->title }}</strong>
                                <span style="font-size: 0.78rem; color: var(--gray-600);">Pengarang: {{ $b->book->author }}</span>
                            </div>
                            <span class="badge badge-warning" style="font-size: 0.75rem;">Jatuh tempo: {{ \Carbon\Carbon::parse($b->due_date)->format('d M Y') }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        <div style="background-color: var(--gray-50); padding: 14px 24px; text-align: right; border-top: 1px solid var(--gray-200);">
            <button type="button" onclick="closeQuotaModal()" class="btn btn-secondary btn-sm">Tutup</button>
        </div>
    </div>
</div>

@section('scripts')
<script>
    function openQuotaModal() {
        document.getElementById('quotaModal').style.display = 'flex';
    }
    function closeQuotaModal() {
        document.getElementById('quotaModal').style.display = 'none';
    }
    window.onclick = function(event) {
        const modal = document.getElementById('quotaModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
</script>
@endsection
@endsection
