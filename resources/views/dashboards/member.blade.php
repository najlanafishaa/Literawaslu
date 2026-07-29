@extends('layouts.app')

@section('title', 'Dashboard Pengguna')
@section('header_title', 'Dashboard')

@section('content')
<div class="welcome-banner">
    <div style="position: relative; z-index: 5; flex: 1; min-width: 220px;">
        <h1>Halo, {{ auth()->user()->name }}!</h1>
        <p>Selamat datang kembali di Perpustakaan Literawaslu. Mari temukan buku favorit Anda hari ini.</p>
        <div style="margin-top: 16px; display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('catalog') }}" class="btn btn-sm" style="background: var(--light); color: var(--primary); font-weight: 700; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"><i class="fa-solid fa-magnifying-glass"></i> Jelajah Katalog</a>
            <a href="{{ route('member.card') }}" class="btn btn-sm" style="background: transparent; border: 1px solid rgba(255,255,255,0.6); color: var(--light); font-weight: 600;"><i class="fa-solid fa-id-card"></i> Tampilkan Kartu</a>
        </div>
    </div>
</div>

<!-- Stats Dashboard Grid -->
<div class="grid-stats">
    <a href="{{ route('catalog') }}" class="stat-card card-red" style="text-decoration: none; cursor: pointer;">
        <div class="stat-info">
            <h3>KOLEKSI TERSEDIA</h3>
            <p>{{ $availableBooksCount }} Buku</p>
        </div>
        <div class="stat-icon">
            <i class="fa-solid fa-book"></i>
        </div>
    </a>
    
    <div class="stat-card card-dark" style="cursor: pointer; position: relative; overflow: hidden; display: flex; align-items: center;" onclick="openQuotaModal()">
        <div class="stat-info" style="width: 100%; z-index: 2;">
            <h3 style="font-size: 0.8rem; margin-bottom: 12px; opacity: 0.9;">SISA KUOTA PINJAM</h3>
            <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                <div style="background: var(--gray-50); padding: 8px 12px; border-radius: 8px; flex: 1; text-align: center; border: 1px solid var(--gray-200);">
                    <div style="font-size: 0.65rem; color: var(--gray-600); margin-bottom: 2px; letter-spacing: 0.5px; font-weight: 700;"><i class="fa-solid fa-tablet-screen-button" style="color: var(--secondary);"></i> ONLINE</div>
                    <div style="font-size: 1.3rem; font-weight: 800; color: var(--dark); line-height: 1;">{{ max(0, 3 - $activeBorrows->where('book.is_online', true)->count()) }}<span style="font-size: 0.8rem; color: var(--gray-600); font-weight: 500;">/3</span></div>
                </div>
                <div style="background: var(--gray-50); padding: 8px 12px; border-radius: 8px; flex: 1; text-align: center; border: 1px solid var(--gray-200);">
                    <div style="font-size: 0.65rem; color: var(--gray-600); margin-bottom: 2px; letter-spacing: 0.5px; font-weight: 700;"><i class="fa-solid fa-book-bookmark" style="color: var(--primary);"></i> OFFLINE</div>
                    <div style="font-size: 1.3rem; font-weight: 800; color: var(--dark); line-height: 1;">{{ max(0, 1 - $activeBorrows->where('book.is_online', false)->count()) }}<span style="font-size: 0.8rem; color: var(--gray-600); font-weight: 500;">/1</span></div>
                </div>
            </div>
            <small style="opacity: 0.8; font-size: 0.75rem; display: flex; align-items: center; gap: 5px;"><i class="fa-solid fa-hand-pointer"></i> Klik rincian &amp; aturan</small>
        </div>
        <div class="stat-icon" style="position: absolute; right: -15px; bottom: -20px; font-size: 6rem; opacity: 0.05; z-index: 1;">
            <i class="fa-solid fa-layer-group"></i>
        </div>
    </div>
    
    <a href="{{ route('member.rewards') }}" class="stat-card card-yellow" style="text-decoration: none; cursor: pointer;">
        <div class="stat-info">
            <h3>POIN HADIAH</h3>
            <p>{{ $member->points }} Poin</p>
            @php
                $pts = $member->points;
                $badgeClass = $pts >= 300 ? 'badge-gold' : ($pts >= 200 ? 'badge-silver' : 'badge-bronze');
                $statusName = $pts >= 300 ? 'Emas' : ($pts >= 200 ? 'Perak' : 'Perunggu');
            @endphp
            <div style="margin-top: 4px;">
                <span class="badge {{ $badgeClass }}" style="font-size: 0.7rem; padding: 2px 8px;">
                    {{ $statusName }}
                </span>
            </div>
        </div>
        <div class="stat-icon">
            <i class="fa-solid fa-trophy"></i>
        </div>
    </a>
</div>

<div class="card" style="margin-bottom: 24px; overflow: hidden;">
    <div style="background: linear-gradient(135deg, var(--primary) 0%, #a01419 100%); color: #ffffff; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; padding: 16px 20px;">
        <h2 style="margin: 0; color: #ffffff !important; display: flex; align-items: center; gap: 8px; font-size: 1.15rem;"><i class="fa-solid fa-book-open" style="color: #ffffff !important;"></i> Pengajuan & Status Peminjaman</h2>
        <a href="{{ route('catalog') }}" class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: #ffffff !important; border: 1px solid rgba(255,255,255,0.4); font-weight: 600;">
            <i class="fa-solid fa-book-circle-arrow-right"></i> Ajukan Peminjaman via Katalog
        </a>
    </div>
    <div class="card-body">
        <div style="background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px 20px; border-radius: 4px; margin-bottom: 22px; color: #78350f; display: flex; gap: 15px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <i class="fa-solid fa-lightbulb" style="font-size: 1.5rem; color: #f59e0b; margin-top: 2px;"></i>
            <div>
                <strong style="display: block; margin-bottom: 6px; font-size: 0.95rem;">Catatan Ketentuan Peminjaman:</strong>
                <ul style="margin: 0; padding-left: 20px; font-size: 0.85rem; line-height: 1.6;">
                    <li><strong>Buku Online:</strong> Maks. 3 buku (otomatis kembali saat jatuh tempo).</li>
                    <li><strong>Buku Offline:</strong> Maks. 1 buku (wajib dikembalikan fisik ke perpus).</li>
                    <li><strong>Durasi & Persetujuan:</strong> Durasi maks. 7 hari dan pengajuan wajib disetujui Admin.</li>
                </ul>
            </div>
        </div>

        <h4 style="font-size: 1rem; font-weight: 700; color: var(--dark); margin-bottom: 12px;">Daftar Pengajuan & Status Peminjaman Anda:</h4>
        @if(!isset($onlineBorrowRequests) || $onlineBorrowRequests->isEmpty())
            <div style="text-align: center; padding: 30px 20px; color: var(--gray-600);">
                <i class="fa-solid fa-book-open" style="font-size: 2.5rem; color: var(--gray-300); margin-bottom: 10px;"></i>
                <p style="font-weight: 600;">Belum ada riwayat pengajuan peminjaman.</p>
                <a href="{{ route('catalog') }}" class="btn btn-primary btn-sm" style="margin-top: 10px; display: inline-block;">Pilih Buku dari Katalog</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Buku</th>
                            <th>Jenis Buku</th>
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
                                <td>
                                    @if($req->book->is_online)
                                        <span class="badge badge-online">Online</span>
                                    @else
                                        <span class="badge badge-offline">Offline</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($req->borrow_date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($req->due_date)->format('d M Y') }}</td>
                                <td>
                                    @if($req->status === 'pending')
                                        <span class="badge badge-pending"><i class="fa-solid fa-hourglass-half"></i> Menunggu Verifikasi</span>
                                    @elseif($req->status === 'borrowed')
                                        <span class="badge badge-success"><i class="fa-solid fa-bookmark"></i> Disetujui (Sedang Dipinjam)</span>
                                    @elseif($req->status === 'returned')
                                        <span class="badge badge-secondary"><i class="fa-solid fa-box-archive"></i> Selesai (Dikembalikan)</span>
                                    @elseif($req->status === 'rejected')
                                        <span class="badge badge-danger"><i class="fa-solid fa-ban"></i> Ditolak</span>
                                    @else
                                        <span class="badge badge-danger">{{ ucfirst($req->status) }}</span>
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
    <div class="card" style="overflow: hidden;">
        <div style="background: linear-gradient(135deg, var(--primary) 0%, #a01419 100%); color: #ffffff; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; padding: 14px 20px;">
            <h2 style="margin: 0; color: #ffffff !important; font-size: 1rem; display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-hand-holding-hand" style="color: #ffffff !important;"></i> Buku yang Sedang Dipinjam</h2>
            <span class="badge" style="background: rgba(255,255,255,0.25); color: #ffffff !important; font-size: 0.75rem;">{{ $activeBorrows->count() }} Sedang Dipinjam</span>
        </div>
        <div class="card-body">
            @if($activeBorrows->isEmpty())
                <div style="text-align: center; padding: 40px 20px; color: var(--gray-600);">
                    <i class="fa-solid fa-book-open-reader" style="font-size: 2.5rem; color: #9CA3AF; margin-bottom: 15px;"></i>
                    <p style="font-weight: 500;">Anda tidak memiliki peminjaman aktif saat ini.</p>
                    <p style="font-size: 0.85rem; margin-top: 5px;">Silakan datangi admin perpustakaan untuk meminjam buku.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Buku</th>
                                <th>Jenis Buku</th>
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
                                    <td>
                                        @if($borrow->book->is_online)
                                            <span class="badge badge-online">Online</span>
                                        @else
                                            <span class="badge badge-offline">Offline</span>
                                        @endif
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
    <div class="card" style="overflow: hidden;">
        <div style="background: linear-gradient(135deg, var(--primary) 0%, #a01419 100%); color: #ffffff; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; padding: 14px 20px;">
            <h2 style="margin: 0; color: #ffffff !important; font-size: 1rem; display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-id-card" style="color: #ffffff !important;"></i> Kartu Anggota Digital</h2>
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
                            <div style="font-size: 1.45rem; font-weight: 800; color: #1A1A1A; line-height: 1; letter-spacing: 0.5px;">
                                Literawaslu
                            </div>
                        </div>
                    </div>
                    
                    <div class="digital-card-body" style="margin-top: 10px; z-index: 5; display: flex; align-items: center; gap: 15px;">
                        <div>
                            <div class="member-name" style="font-size: 1.6rem; font-weight: 700; color: #1A1A1A; letter-spacing: 0.5px;">
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
                            <span style="font-size: 1rem; font-weight: 700; color: #1A1A1A;">{{ strtoupper($member->created_at->addYear(1)->locale('id')->translatedFormat('d F Y')) }}</span>
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

<!-- Modal Rincian Kuota Peminjaman -->
<div id="quotaModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(15,23,42,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background-color: var(--light); border-radius: var(--border-radius); max-width: 600px; width: 100%; box-shadow: var(--box-shadow-md); overflow: hidden;">
        <div style="background-color: var(--primary); color: #ffffff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #ffffff;"><i class="fa-solid fa-layer-group" style="margin-right: 8px;"></i> Rincian Kuota Peminjaman</h3>
            <button type="button" onclick="closeQuotaModal()" style="background: none; border: none; color: #ffffff; font-size: 1.2rem; cursor: pointer;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div style="padding: 24px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
                <!-- KUOTA ONLINE CARD -->
                <div class="card-primary-red" style="border-radius: var(--border-radius); padding: 16px; text-align: center;">
                    <div style="font-size: 0.8rem; font-weight: 800; letter-spacing: 0.5px; opacity: 0.9;">KUOTA ONLINE</div>
                    <div style="display: flex; justify-content: space-around; margin-top: 10px; align-items: center;">
                        <div>
                            <span style="font-size: 0.72rem; opacity: 0.85; display: block;">Terpakai</span>
                            <span style="font-size: 1.4rem; font-weight: 800;">{{ $activeBorrows->where('book.is_online', true)->count() }} / 3</span>
                        </div>
                        <div style="border-left: 1px solid rgba(255,255,255,0.3); height: 30px;"></div>
                        <div>
                            <span style="font-size: 0.72rem; opacity: 0.85; display: block;">Sisa</span>
                            <span style="font-size: 1.4rem; font-weight: 800;">{{ max(0, 3 - $activeBorrows->where('book.is_online', true)->count()) }}</span>
                        </div>
                    </div>
                </div>

                <!-- KUOTA OFFLINE CARD -->
                <div class="card-primary-red" style="border-radius: var(--border-radius); padding: 16px; text-align: center;">
                    <div style="font-size: 0.8rem; font-weight: 800; letter-spacing: 0.5px; opacity: 0.9;">KUOTA OFFLINE</div>
                    <div style="display: flex; justify-content: space-around; margin-top: 10px; align-items: center;">
                        <div>
                            <span style="font-size: 0.72rem; opacity: 0.85; display: block;">Terpakai</span>
                            <span style="font-size: 1.4rem; font-weight: 800;">{{ $activeBorrows->where('book.is_online', false)->count() }} / 1</span>
                        </div>
                        <div style="border-left: 1px solid rgba(255,255,255,0.3); height: 30px;"></div>
                        <div>
                            <span style="font-size: 0.72rem; opacity: 0.85; display: block;">Sisa</span>
                            <span style="font-size: 1.4rem; font-weight: 800;">{{ max(0, 1 - $activeBorrows->where('book.is_online', false)->count()) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sedang Dipinjam (Online) -->
            <h4 style="font-size: 0.92rem; font-weight: 700; color: var(--dark); margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                <span class="badge badge-online">Online</span> Sedang Dipinjam (Online)
            </h4>
            @php $onlineBorrows = $activeBorrows->filter(fn($b) => $b->book->is_online); @endphp
            @if($onlineBorrows->isEmpty())
                <p style="font-size: 0.82rem; color: var(--gray-600); margin-bottom: 20px;">Tidak ada buku online yang sedang dipinjam.</p>
            @else
                <ul style="list-style: none; padding: 0; margin: 0 0 20px 0; display: flex; flex-direction: column; gap: 8px;">
                    @foreach($onlineBorrows as $b)
                        <li style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background-color: var(--gray-50); border-radius: 8px; border-left: 4px solid var(--secondary);">
                            <div>
                                <strong style="font-size: 0.88rem; color: var(--dark); display: block;">{{ $b->book->title }}</strong>
                                <span style="font-size: 0.76rem; color: var(--gray-600);">Pengarang: {{ $b->book->author }}</span>
                            </div>
                            <span class="badge badge-warning" style="font-size: 0.75rem;">Jatuh tempo: {{ \Carbon\Carbon::parse($b->due_date)->format('d M Y') }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            <!-- Sedang Dipinjam (Offline) -->
            <h4 style="font-size: 0.92rem; font-weight: 700; color: var(--dark); margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                <span class="badge badge-offline">Offline</span> Sedang Dipinjam (Offline)
            </h4>
            @php $offlineBorrows = $activeBorrows->filter(fn($b) => !$b->book->is_online); @endphp
            @if($offlineBorrows->isEmpty())
                <p style="font-size: 0.82rem; color: var(--gray-600); margin-bottom: 10px;">Tidak ada buku offline yang sedang dipinjam.</p>
            @else
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px;">
                    @foreach($offlineBorrows as $b)
                        <li style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background-color: var(--gray-50); border-radius: 8px; border-left: 4px solid var(--primary);">
                            <div>
                                <strong style="font-size: 0.88rem; color: var(--dark); display: block;">{{ $b->book->title }}</strong>
                                <span style="font-size: 0.76rem; color: var(--gray-600);">Pengarang: {{ $b->book->author }}</span>
                            </div>
                            <span class="badge badge-danger" style="font-size: 0.75rem;">Kembalikan Fisik di Perpustakaan</span>
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
