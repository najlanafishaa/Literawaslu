@extends('layouts.app')

@section('title', 'Kelola Pengguna')
@section('header_title', 'Kelola Pengguna Perpustakaan')

@section('content')
<div class="card" style="margin-bottom: 25px;">
    <div class="card-body" style="padding: 20px;">
        <form action="{{ route('members.index') }}" method="GET" style="display: flex; gap: 10px; max-width: 500px;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama, email, atau kode pengguna..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i> Cari</button>
            @if(request('search'))
                <a href="{{ route('members.index') }}" class="btn btn-outline"><i class="ti ti-rotate"></i> Atur Ulang</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <h2>Daftar Pengguna Terdaftar</h2>
            <span class="badge badge-success">{{ $members->count() }} Pengguna</span>
        </div>
        @if(auth()->user()->role === 'super_admin')
            <a href="{{ route('members.create') }}" class="btn btn-primary btn-sm" style="display: flex; align-items: center; gap: 6px; padding: 8px 16px; font-size: 0.85rem; border-radius: var(--border-radius); text-decoration: none;">
                <i class="ti ti-user-plus"></i> Tambah Pengguna
            </a>
        @endif
    </div>
    
    <div class="card-body">
        @if($members->isEmpty())
        <p style="text-align: center; color: var(--gray-600); padding: 20px;">Tidak ada data pengguna aktif yang ditemukan.</p>
        @else
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Kode Pengguna</th>
                            <th>Nama</th>
                            <th>Email</th>
                            @if(auth()->user()->role === 'super_admin')
                                <th>Info Keamanan</th>
                            @endif
                            <th>Total Peminjaman</th>
                            <th>Reward Poin</th>
                            <th>Batas Pinjam</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $member)
                            <tr>
                                <td style="font-family: monospace; font-weight: 700; color: var(--dark);">{{ $member->member_code }}</td>
                                <td><strong>{{ $member->user->name }}</strong></td>
                                <td>{{ $member->user->email }}</td>
                                @if(auth()->user()->role === 'super_admin')
                                    <td>
                                        <div style="font-size: 0.8rem;">
                                            <div style="color: var(--gray-600);">Tanya: {{ $member->user->security_question }}</div>
                                            <div style="font-weight: 600; color: var(--dark);">Jawab: {{ $member->user->security_answer }}</div>
                                        </div>
                                    </td>
                                @endif
                                <td>{{ $member->total_loans }} Kali</td>
                                <td>
                                    <span class="badge badge-warning">{{ $member->points }}</span>
                                </td>
                                <td>{{ $member->borrow_limit }} Buku</td>
                                <td>
                                    @if($member->status === 'active')
                                        <span class="badge badge-success"><i class="ti ti-check"></i> Terverifikasi</span>
                                    @elseif($member->status === 'pending')
                                        <span class="badge badge-warning"><i class="ti ti-clock"></i> Menunggu Verifikasi</span>
                                    @elseif($member->status === 'rejected')
                                        <span class="badge badge-danger"><i class="ti ti-x"></i> Ditolak</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $member->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        @if(auth()->user()->role === 'super_admin')
                                            <a href="{{ route('members.edit', $member->id) }}" class="btn btn-outline btn-sm" title="Edit Pengguna" style="padding: 6px 10px;">
                                                <i class="ti ti-pencil"></i>
                                            </a>
                                            <form action="{{ route('members.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini dari sistem? Semua data relasi terkait juga akan terhapus.');" style="margin: 0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline btn-sm" title="Hapus Pengguna" style="padding: 6px 10px; color: var(--primary);">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        @endif
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
