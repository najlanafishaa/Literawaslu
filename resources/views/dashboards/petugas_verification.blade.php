@extends('layouts.app')

@section('title', 'Verifikasi')
@section('header_title', 'Verifikasi Pengguna & Peminjaman')

@section('content')
<div class="card" style="margin-bottom: 25px;">
    <div class="card-header">
        <h2 style="margin: 0;"><i class="ti ti-user-check" style="color: var(--primary); margin-right: 8px;"></i> Verifikasi Pendaftaran Pengguna Baru</h2>
    </div>
    
    <div class="card-body">
        @if($pendingMembers->isEmpty())
            <p style="text-align: center; color: var(--gray-600); padding: 20px;">Tidak ada pendaftaran pengguna baru yang perlu diverifikasi.</p>
        @else
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Kode Pengguna</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Tgl Mendaftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingMembers as $member)
                            <tr>
                                <td style="font-weight: bold; color: var(--primary);">{{ $member->member_code }}</td>
                                <td>{{ $member->user->name }}</td>
                                <td>{{ $member->user->email }}</td>
                                <td>{{ $member->created_at->format('d M Y, H:i') }}</td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <form action="{{ route('verifications.member.approve', $member->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary btn-sm">
                                                <i class="ti ti-check"></i> Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('verifications.member.reject', $member->id) }}" method="POST" onsubmit="return confirm('Tolak pendaftaran member ini?');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline btn-sm">
                                                <i class="ti ti-x"></i> Tolak
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

<div class="card" style="margin-bottom: 25px;">
    <div class="card-header">
        <h2 style="margin: 0;"><i class="ti ti-device-laptop" style="color: var(--secondary); margin-right: 8px;"></i> Verifikasi Peminjaman Online</h2>
    </div>
    
    <div class="card-body">
        @if($pendingBorrows->isEmpty())
            <p style="text-align: center; color: var(--gray-600); padding: 20px;">Tidak ada permintaan peminjaman buku yang perlu diverifikasi.</p>
        @else
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Pengguna</th>
                            <th>Buku</th>
                            <th>Jenis Buku</th>
                            <th>Tgl Pinjam (Request)</th>
                            <th>Rencana Tgl Kembali</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingBorrows as $borrow)
                            <tr>
                                <td>
                                    <div style="font-weight: bold;">{{ $borrow->member->user->name }}</div>
                                    <small style="color: var(--gray-600);">{{ $borrow->member->member_code }}</small>
                                </td>
                                <td>
                                    <div style="font-weight: bold;">{{ $borrow->book->title }}</div>
                                    <small style="color: var(--gray-600);">Stok: {{ $borrow->book->available_stock }}</small>
                                </td>
                                <td>
                                    @if($borrow->book->is_online)
                                        <span class="badge badge-online">Online</span>
                                    @else
                                        <span class="badge badge-offline">Offline</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($borrow->borrow_date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($borrow->due_date)->format('d M Y') }}</td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <form action="{{ route('verifications.borrow.approve', $borrow->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary btn-sm">
                                                <i class="ti ti-check"></i> Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('verifications.borrow.reject', $borrow->id) }}" method="POST" onsubmit="return confirm('Tolak permintaan pinjaman ini?');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline btn-sm">
                                                <i class="ti ti-x"></i> Tolak
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



<div class="card">
    <div class="card-header">
        <h2 style="margin: 0;"><i class="ti ti-key" style="color: var(--primary); margin-right: 8px;"></i> Verifikasi Atur Ulang Kata Sandi Pengguna</h2>
    </div>
    
    <div class="card-body">
        @if($pendingResets->isEmpty())
            <p style="text-align: center; color: var(--gray-600); padding: 20px;">Tidak ada permintaan atur ulang kata sandi yang perlu diverifikasi.</p>
        @else
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Nama Pengguna</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Tgl Request</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingResets as $req)
                            <tr>
                                <td>
                                    <div style="font-weight: bold;">{{ $req->user->name }}</div>
                                    <small style="color: var(--gray-600);">{{ $req->user->member ? $req->user->member->member_code : 'N/A' }}</small>
                                </td>
                                <td>{{ $req->user->email }}</td>
                                <td>{{ $req->user->phone ?? '-' }}</td>
                                <td>{{ $req->created_at->format('d M Y, H:i') }}</td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <form action="{{ route('verifications.reset.approve', $req->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary btn-sm">
                                                <i class="ti ti-check"></i> Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('verifications.reset.reject', $req->id) }}" method="POST" onsubmit="return confirm('Tolak permintaan atur ulang kata sandi ini?');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline btn-sm">
                                                <i class="ti ti-x"></i> Tolak
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
@endsection
