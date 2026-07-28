@extends('layouts.app')

@section('title', 'Hadiah & Poin')
@section('header_title', 'Hadiah & Poin')

@section('content')
<!-- Saldo Poin Card -->
<div class="card" style="background-color: var(--primary); color: #ffffff; border: 1px solid var(--primary); margin-bottom: 25px;">
    <div class="card-body reward-hero-body" style="padding: 24px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <span style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: rgba(255,255,255,0.85); font-weight: 600; display: block;">Saldo Poin Anda</span>
            <div style="font-size: 3.5rem; font-weight: 800; margin-top: 2px; color: #ffffff; line-height: 1;">
                {{ $member->points }} <span style="font-size: 1.4rem; font-weight: 600; color: rgba(255,255,255,0.9);">Poin</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px; margin-top: 15px; font-size: 0.85rem; background-color: rgba(0,0,0,0.2); padding: 6px 14px; border-radius: var(--border-radius); width: fit-content;">
                <i class="fa-solid fa-medal" style="color: var(--secondary);"></i>
                <span style="color: #ffffff;">Status:</span> 
                @php
                    $pts = $member->points;
                    $badgeClass = $pts >= 300 ? 'badge-gold' : ($pts >= 200 ? 'badge-silver' : 'badge-bronze');
                    $statusName = $pts >= 300 ? 'Tingkat Emas' : ($pts >= 200 ? 'Tingkat Perak' : 'Tingkat Perunggu');
                @endphp
                <span class="badge {{ $badgeClass }}" style="padding: 4px 10px; font-size: 0.78rem;">
                    {{ $statusName }}
                </span>
            </div>
        </div>
        <div class="reward-hero-icon-wrap" style="font-size: 4.5rem; color: rgba(255,255,255,0.25);">
            <i class="fa-solid fa-trophy"></i>
        </div>
    </div>
</div>

<!-- Penukaran Reward Card -->
<div class="card">
    <div class="card-header">
        <h2>Penukaran Hadiah (Batas Peminjaman)</h2>
    </div>
    <div class="card-body" style="padding: 25px; display: flex; flex-direction: column; gap: 15px;">

        {{-- Tier 1: 100 Poin = 1 Buku --}}
        <div class="reward-tier-row">
            <div class="reward-tier-content">
                <div style="width: 45px; height: 45px; border-radius: 50%; background-color: rgba(var(--secondary-rgb), 0.15); color: var(--secondary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-book"></i>
                </div>
                <div>
                    <h4 style="font-weight: 600; color: var(--dark); font-size: 1rem;">Tingkat 1: Batas 1 Buku</h4>
                    <p style="font-size: 0.8rem; color: var(--gray-600); margin-top: 2px;">Syarat: Memiliki minimal 100 Poin.</p>
                </div>
            </div>
            <div class="reward-tier-btn-wrap">
                @if($member->borrow_limit >= 1)
                    <span class="badge badge-success"><i class="fa-solid fa-check"></i> Aktif</span>
                @elseif($member->points >= 100)
                    <form action="{{ route('member.redeem') }}" method="POST" style="width: 100%;">
                        @csrf
                        <input type="hidden" name="target_limit" value="1">
                        <button type="submit" class="btn btn-secondary btn-sm btn-block-mobile"><i class="fa-solid fa-unlock"></i> Buka Batas 1 Buku</button>
                    </form>
                @else
                    <button type="button" class="btn btn-outline btn-sm btn-block-mobile" disabled style="cursor: not-allowed; opacity: 0.6;"><i class="fa-solid fa-lock"></i> Butuh {{ 100 - $member->points }} Poin Lagi</button>
                @endif
            </div>
        </div>

        {{-- Tier 2: 200 Poin = 2 Buku --}}
        <div class="reward-tier-row">
            <div class="reward-tier-content">
                <div style="width: 45px; height: 45px; border-radius: 50%; background-color: rgba(var(--primary-rgb), 0.15); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-book-bookmark"></i>
                </div>
                <div>
                    <h4 style="font-weight: 600; color: var(--dark); font-size: 1rem;">Tingkat 2: Batas 2 Buku</h4>
                    <p style="font-size: 0.8rem; color: var(--gray-600); margin-top: 2px;">Syarat: Memiliki minimal 200 Poin.</p>
                </div>
            </div>
            <div class="reward-tier-btn-wrap">
                @if($member->borrow_limit >= 2)
                    <span class="badge badge-success"><i class="fa-solid fa-check"></i> Aktif</span>
                @elseif($member->points >= 200)
                    <form action="{{ route('member.redeem') }}" method="POST" style="width: 100%;">
                        @csrf
                        <input type="hidden" name="target_limit" value="2">
                        <button type="submit" class="btn btn-secondary btn-sm btn-block-mobile"><i class="fa-solid fa-unlock"></i> Buka Batas 2 Buku</button>
                    </form>
                @else
                    <button type="button" class="btn btn-outline btn-sm btn-block-mobile" disabled style="cursor: not-allowed; opacity: 0.6;"><i class="fa-solid fa-lock"></i> Butuh {{ 200 - $member->points }} Poin Lagi</button>
                @endif
            </div>
        </div>

        {{-- Tier 3: 300 Poin = 3 Buku --}}
        <div class="reward-tier-row">
            <div class="reward-tier-content">
                <div style="width: 45px; height: 45px; border-radius: 50%; background-color: rgba(var(--primary-rgb), 0.15); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-books"></i>
                </div>
                <div>
                    <h4 style="font-weight: 600; color: var(--dark); font-size: 1rem;">Tingkat 3: Batas 3 Buku</h4>
                    <p style="font-size: 0.8rem; color: var(--gray-600); margin-top: 2px;">Syarat: Memiliki minimal 300 Poin.</p>
                </div>
            </div>
            <div class="reward-tier-btn-wrap">
                @if($member->borrow_limit >= 3)
                    <span class="badge badge-success"><i class="fa-solid fa-check"></i> Aktif</span>
                @elseif($member->points >= 300)
                    <form action="{{ route('member.redeem') }}" method="POST" style="width: 100%;">
                        @csrf
                        <input type="hidden" name="target_limit" value="3">
                        <button type="submit" class="btn btn-secondary btn-sm btn-block-mobile"><i class="fa-solid fa-unlock"></i> Buka Batas 3 Buku</button>
                    </form>
                @else
                    <button type="button" class="btn btn-outline btn-sm btn-block-mobile" disabled style="cursor: not-allowed; opacity: 0.6;"><i class="fa-solid fa-lock"></i> Butuh {{ 300 - $member->points }} Poin Lagi</button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Point History Table Card -->
<div class="card">
    <div class="card-header">
        <h2><i class="fa-solid fa-clock-rotate-left" style="color: var(--primary); margin-right: 8px;"></i> Riwayat Poin Hadiah</h2>
    </div>
    <div class="card-body">
        @if(!isset($pointHistories) || $pointHistories->isEmpty())
            <p style="text-align: center; color: var(--gray-600); padding: 25px;">Belum ada riwayat perolehan atau perubahan poin.</p>
        @else
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Poin</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pointHistories as $history)
                            <tr>
                                <td>{{ $history->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    @if($history->type === 'earn')
                                        <span class="badge badge-success"><i class="fa-solid fa-plus-circle"></i> Tambah Poin</span>
                                    @elseif($history->type === 'deduct')
                                        <span class="badge badge-danger"><i class="fa-solid fa-minus-circle"></i> Pengurangan (Penalti)</span>
                                    @else
                                        <span class="badge badge-warning"><i class="fa-solid fa-hourglass-half"></i> Penukaran Limit</span>
                                    @endif
                                </td>
                                <td>
                                    <strong style="color: {{ $history->type === 'earn' ? 'var(--dark)' : ($history->type === 'deduct' ? 'var(--primary)' : 'var(--dark)') }};">
                                        {{ $history->type === 'earn' ? '+' : ($history->type === 'deduct' ? '-' : '') }}{{ $history->points }}
                                    </strong>
                                </td>
                                <td>{{ $history->description }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Rewards Information Card -->
<div class="card" style="overflow: hidden; margin-top: 25px;">
    <div style="background: var(--primary); color: #ffffff; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between;">
        <h2 style="color: #ffffff; margin: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-circle-question" style="color: #ffffff;"></i> Ketentuan & Cara Kerja Poin
        </h2>
    </div>
    <div class="card-body" style="padding: 25px; background: #ffffff; color: var(--dark);">
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div class="reward-info-item" style="display: flex; gap: 15px; align-items: flex-start;">
                <div style="font-size: 1.4rem; color: var(--primary); margin-top: 2px;"><i class="fa-solid fa-user-plus"></i></div>
                <div>
                    <h4 style="font-size: 1rem; font-weight: 700; color: var(--dark);">Bonus Registrasi</h4>
                    <p style="font-size: 0.88rem; color: var(--gray-700); margin-top: 4px;">
                        Pengguna baru memperoleh <strong style="color: var(--primary);">10 poin</strong> setelah akun berhasil diverifikasi.
                    </p>
                </div>
            </div>

            <div class="reward-info-item" style="display: flex; gap: 15px; align-items: flex-start;">
                <div style="font-size: 1.4rem; color: var(--primary); margin-top: 2px;"><i class="fa-solid fa-hand-holding-hand"></i></div>
                <div>
                    <h4 style="font-size: 1rem; font-weight: 700; color: var(--dark);">Peminjaman Buku</h4>
                    <p style="font-size: 0.88rem; color: var(--gray-700); margin-top: 4px;">
                        Setiap peminjaman yang telah selesai dan dikembalikan sesuai ketentuan memperoleh poin: Buku Offline (<strong style="color: var(--primary);">5 poin</strong>), Buku Online (<strong style="color: var(--primary);">1 poin</strong>).
                    </p>
                </div>
            </div>

            <div class="reward-info-item" style="display: flex; gap: 15px; align-items: flex-start; border-top: 1px solid var(--gray-200); padding-top: 20px;">
                <div style="font-size: 1.4rem; color: #d62027; margin-top: 2px;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div>
                    <h4 style="font-size: 1rem; font-weight: 700; color: var(--dark);">Keterlambatan</h4>
                    <p style="font-size: 0.88rem; color: var(--gray-700); margin-top: 4px; line-height: 1.6;">
                        Tidak ada denda uang.<br>
                        Sanksi:<br>
                        &bull; Terlambat 1 hari &rarr; <strong style="color: #d62027;">-10 poin</strong><br>
                        &bull; Terlambat 2 hari &rarr; <strong style="color: #d62027;">-10 poin</strong><br>
                        &bull; Terlambat 3 hari &rarr; <strong style="color: #d62027;">-10 poin</strong><br>
                        &bull; Lebih dari 3 hari &rarr; <strong style="color: #d62027;">wajib mendonasikan 1 buku fisik</strong>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
