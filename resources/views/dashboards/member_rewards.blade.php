@extends('layouts.app')

@section('title', 'Reward & Poin')
@section('header_title', 'Reward & Poin')

@section('content')
<div class="dashboard-grid">
    <!-- Left Column: Points Overview and Redeems -->
    <div>
        <div class="card" style="background: linear-gradient(135deg, var(--primary) 0%, #a81014 100%); color: var(--light); border: none; box-shadow: var(--shadow-premium);">
            <div class="card-body" style="padding: 35px; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h3 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.7); font-weight: 600;">Saldo Poin Anda</h3>
                    <p style="font-size: 3.2rem; font-weight: 800; margin-top: 5px; color: var(--secondary);">{{ $member->points }} <span style="font-size: 1.5rem; font-weight: 500; color: var(--light);">Poin</span></p>
                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 15px; font-size: 0.85rem; background-color: rgba(255,255,255,0.1); padding: 6px 12px; border-radius: 20px; width: fit-content;">
                        <i class="fa-solid fa-medal" style="color: var(--secondary);"></i>
                        Status: 
                        <strong>
                            @if($member->points >= 300)
                                Gold Member
                            @elseif($member->points >= 200)
                                Silver Member
                            @else
                                Bronze Member
                            @endif
                        </strong>
                    </div>
                </div>
                <div style="font-size: 5rem; color: rgba(255,255,255,0.15);">
                    <i class="fa-solid fa-award"></i>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Penukaran Reward (Batas Peminjaman)</h2>
            </div>
            <div class="card-body" style="padding: 25px; display: flex; flex-direction: column; gap: 15px;">

                {{-- Tier 1: 100 Poin = 1 Buku --}}
                <div style="border: 1px solid var(--gray-200); border-radius: var(--border-radius); padding: 15px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px; background-color: var(--gray-50);">
                    <div style="display: flex; gap: 15px; align-items: center;">
                        <div style="width: 45px; height: 45px; border-radius: 50%; background-color: rgba(241, 196, 15, 0.15); color: #b58b00; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                            <i class="fa-solid fa-book"></i>
                        </div>
                        <div>
                            <h4 style="font-weight: 600; color: var(--dark); font-size: 1rem;">Level 1: Batas 1 Buku</h4>
                            <p style="font-size: 0.8rem; color: var(--gray-600); margin-top: 2px;">Syarat: Memiliki minimal 100 Poin.</p>
                        </div>
                    </div>
                    <div>
                        @if($member->borrow_limit >= 1)
                            <span class="badge badge-success" style="padding: 6px 12px;"><i class="fa-solid fa-check"></i> Aktif</span>
                        @elseif($member->points >= 100)
                            <form action="{{ route('member.redeem') }}" method="POST">
                                @csrf
                                <input type="hidden" name="target_limit" value="1">
                                <button type="submit" class="btn btn-accent btn-sm"><i class="fa-solid fa-unlock"></i> Buka Batas 1 Buku</button>
                            </form>
                        @else
                            <button type="button" class="btn btn-outline btn-sm" disabled style="cursor: not-allowed; opacity: 0.6;"><i class="fa-solid fa-lock"></i> Butuh {{ 100 - $member->points }} Poin Lagi</button>
                        @endif
                    </div>
                </div>

                {{-- Tier 2: 200 Poin = 2 Buku --}}
                <div style="border: 1px solid var(--gray-200); border-radius: var(--border-radius); padding: 15px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px; background-color: var(--gray-50);">
                    <div style="display: flex; gap: 15px; align-items: center;">
                        <div style="width: 45px; height: 45px; border-radius: 50%; background-color: rgba(66, 133, 244, 0.15); color: #4285F4; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                            <i class="fa-solid fa-book-bookmark"></i>
                        </div>
                        <div>
                            <h4 style="font-weight: 600; color: var(--dark); font-size: 1rem;">Level 2: Batas 2 Buku</h4>
                            <p style="font-size: 0.8rem; color: var(--gray-600); margin-top: 2px;">Syarat: Memiliki minimal 200 Poin.</p>
                        </div>
                    </div>
                    <div>
                        @if($member->borrow_limit >= 2)
                            <span class="badge badge-success" style="padding: 6px 12px;"><i class="fa-solid fa-check"></i> Aktif</span>
                        @elseif($member->points >= 200)
                            <form action="{{ route('member.redeem') }}" method="POST">
                                @csrf
                                <input type="hidden" name="target_limit" value="2">
                                <button type="submit" class="btn btn-accent btn-sm"><i class="fa-solid fa-unlock"></i> Buka Batas 2 Buku</button>
                            </form>
                        @else
                            <button type="button" class="btn btn-outline btn-sm" disabled style="cursor: not-allowed; opacity: 0.6;"><i class="fa-solid fa-lock"></i> Butuh {{ 200 - $member->points }} Poin Lagi</button>
                        @endif
                    </div>
                </div>

                {{-- Tier 3: 300 Poin = 3 Buku --}}
                <div style="border: 1px solid var(--gray-200); border-radius: var(--border-radius); padding: 15px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px; background-color: var(--gray-50);">
                    <div style="display: flex; gap: 15px; align-items: center;">
                        <div style="width: 45px; height: 45px; border-radius: 50%; background-color: rgba(227, 30, 36, 0.15); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                            <i class="fa-solid fa-books"></i>
                        </div>
                        <div>
                            <h4 style="font-weight: 600; color: var(--dark); font-size: 1rem;">Level 3: Batas 3 Buku</h4>
                            <p style="font-size: 0.8rem; color: var(--gray-600); margin-top: 2px;">Syarat: Memiliki minimal 300 Poin.</p>
                        </div>
                    </div>
                    <div>
                        @if($member->borrow_limit >= 3)
                            <span class="badge badge-success" style="padding: 6px 12px;"><i class="fa-solid fa-check"></i> Aktif</span>
                        @elseif($member->points >= 300)
                            <form action="{{ route('member.redeem') }}" method="POST">
                                @csrf
                                <input type="hidden" name="target_limit" value="3">
                                <button type="submit" class="btn btn-accent btn-sm"><i class="fa-solid fa-unlock"></i> Buka Batas 3 Buku</button>
                            </form>
                        @else
                            <button type="button" class="btn btn-outline btn-sm" disabled style="cursor: not-allowed; opacity: 0.6;"><i class="fa-solid fa-lock"></i> Butuh {{ 300 - $member->points }} Poin Lagi</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Point History Table -->
        <div class="card" style="margin-top: 25px;">
            <div class="card-header">
                <h2><i class="fa-solid fa-clock-rotate-left" style="color: var(--primary); margin-right: 8px;"></i> Riwayat Poin Reward</h2>
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
                                                <span class="badge badge-success" style="background-color: #dcfce7; color: #16a34a;"><i class="fa-solid fa-plus-circle"></i> Tambah Poin</span>
                                            @elseif($history->type === 'deduct')
                                                <span class="badge badge-danger" style="background-color: #fee2e2; color: #dc2626;"><i class="fa-solid fa-minus-circle"></i> Pengurangan (Penalti)</span>
                                            @else
                                                <span class="badge badge-warning" style="background-color: #fef08a; color: #ca8a04;"><i class="fa-solid fa-award"></i> Penukaran Limit</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong style="color: {{ $history->type === 'earn' ? 'var(--success)' : ($history->type === 'deduct' ? 'var(--primary)' : 'var(--dark)') }};">
                                                {{ $history->type === 'earn' ? '+' : ($history->type === 'deduct' ? '-' : '') }}{{ $history->points }} Pts
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
    </div>

    <!-- Right Column: Rewards Information -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-circle-question" style="color: var(--primary); margin-right: 8px;"></i> Ketentuan & Cara Kerja Poin</h2>
        </div>
        <div class="card-body" style="padding: 25px;">
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div style="display: flex; gap: 15px;">
                    <div style="font-size: 1.25rem; color: var(--secondary); margin-top: 2px;"><i class="fa-solid fa-user-plus"></i></div>
                    <div>
                        <h4 style="font-size: 0.9rem; font-weight: 600;">1. Bonus Registrasi</h4>
                        <p style="font-size: 0.8rem; color: var(--gray-600); margin-top: 3px;">Saat pertama kali mendaftar akun baru, Anda langsung memperoleh <strong>10 Poin Reward</strong> secara gratis.</p>
                    </div>
                </div>

                <div style="display: flex; gap: 15px;">
                    <div style="font-size: 1.25rem; color: var(--primary); margin-top: 2px;"><i class="fa-solid fa-hand-holding-hand"></i></div>
                    <div>
                        <h4 style="font-size: 0.9rem; font-weight: 600;">2. Pengembalian Buku</h4>
                        <p style="font-size: 0.8rem; color: var(--gray-600); margin-top: 3px;">Meminjam 1 kali buku, setelah buku dikembalikan ke perpustakaan Anda memperoleh <strong>5 Poin Reward</strong>.</p>
                    </div>
                </div>

                <div style="display: flex; gap: 15px; border-top: 1px solid var(--gray-200); padding-top: 20px;">
                    <div style="font-size: 1.25rem; color: var(--primary); margin-top: 2px;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div>
                        <h4 style="font-size: 0.9rem; font-weight: 600;">3. Ketentuan Keterlambatan & Sanksi</h4>
                        <p style="font-size: 0.8rem; color: var(--gray-600); margin-top: 3px;">
                            Sistem tidak menerapkan denda berupa uang. Pengurangan poin & sanksi keterlambatan:<br>
                            &bull; Terlambat 1 hari: <strong>Pengurangan 10 poin</strong><br>
                            &bull; Terlambat 2 hari: <strong>Pengurangan 20 poin</strong><br>
                            &bull; Terlambat 3 hari: <strong>Pengurangan 30 poin</strong><br>
                            &bull; Terlambat lebih dari 3 hari: <strong>Wajib mendonasikan 1 buku fisik</strong> kepada perpustakaan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

