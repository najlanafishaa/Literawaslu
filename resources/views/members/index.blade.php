@extends('layouts.app')

@section('title', 'Kelola Member')
@section('header_title', 'Kelola Anggota Perpustakaan')

@section('content')
<div class="card" style="margin-bottom: 25px;">
    <div class="card-body" style="padding: 20px;">
        <form action="{{ route('members.index') }}" method="GET" style="display: flex; gap: 10px; max-width: 500px;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama, email, atau kode member..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
            @if(request('search'))
                <a href="{{ route('members.index') }}" class="btn btn-outline"><i class="fa-solid fa-rotate-left"></i> Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <h2>Daftar Anggota Terdaftar</h2>
            <span class="badge badge-success">{{ $members->count() }} Anggota</span>
        </div>
        @if(auth()->user()->role === 'super_admin')
            <a href="{{ route('members.create') }}" class="btn btn-primary btn-sm" style="display: flex; align-items: center; gap: 6px; padding: 8px 16px; font-size: 0.85rem; border-radius: var(--border-radius); text-decoration: none;">
                <i class="fa-solid fa-user-plus"></i> Tambah Member
            </a>
        @endif
    </div>
    
    <div class="card-body">
        @if($members->isEmpty())
            <p style="text-align: center; color: var(--gray-600); padding: 20px;">Tidak ada data member yang ditemukan.</p>
        @else
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Kode Member</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Total Peminjaman</th>
                            <th>Reward Poin</th>
                            <th>Batas Pinjam</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $member)
                            <tr>
                                <td style="font-family: monospace; font-weight: 700; color: #b58b00;">{{ $member->member_code }}</td>
                                <td><strong>{{ $member->user->name }}</strong></td>
                                <td>{{ $member->user->email }}</td>
                                <td>
                                    @if($member->status === 'active')
                                        <span class="badge badge-success" style="background-color: #dcfce7; color: #16a34a;"><i class="fa-solid fa-check"></i> Terverifikasi</span>
                                    @elseif($member->status === 'pending')
                                        <span class="badge badge-warning" style="background-color: #fef08a; color: #ca8a04;"><i class="fa-solid fa-clock"></i> Pending</span>
                                    @elseif($member->status === 'rejected')
                                        <span class="badge badge-danger" style="background-color: #fee2e2; color: #dc2626;"><i class="fa-solid fa-xmark"></i> Ditolak</span>
                                    @endif
                                </td>
                                <td>{{ $member->total_loans }} Kali</td>
                                <td>
                                    <span class="badge badge-warning" style="font-weight: 700;">{{ $member->points }} Pts</span>
                                </td>
                                <td>{{ $member->borrow_limit }} Buku</td>
                                <td>
                                    @if($member->is_verified)
                                        <span class="badge badge-success">Terverifikasi</span>
                                    @else
                                        <span class="badge badge-danger">Belum Terverifikasi</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                         @if(!$member->is_verified)
                                             <form action="{{ route('members.verify', $member->id) }}" method="POST" style="margin: 0;">
                                                 @csrf
                                                 <button type="submit" class="btn btn-secondary btn-sm" title="Terima Pendaftaran" style="padding: 6px 10px; font-size: 0.8rem; background-color: var(--secondary); border-color: var(--secondary); color: var(--light);">
                                                     <i class="fa-solid fa-user-check"></i> Terima
                                                 </button>
                                             </form>
                                             <form action="{{ route('members.reject', $member->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menolak dan menghapus pendaftaran member ini?');" style="margin: 0;">
                                                 @csrf
                                                 <button type="submit" class="btn btn-outline btn-sm" title="Tolak Pendaftaran" style="padding: 6px 10px; font-size: 0.8rem; color: var(--primary); border-color: rgba(227,30,36,0.3);">
                                                     <i class="fa-solid fa-user-xmark"></i> Tolak
                                                 </button>
                                             </form>
                                         @endif

                                        @if(auth()->user()->role === 'super_admin')
                                            <a href="{{ route('members.edit', $member->id) }}" class="btn btn-outline btn-sm" title="Edit Member" style="padding: 6px 10px;">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <form action="{{ route('members.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus member ini dari sistem? Semua data relasi terkait juga akan terhapus.');" style="margin: 0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline btn-sm" title="Hapus Member" style="padding: 6px 10px; color: var(--primary); border-color: rgba(227,30,36,0.2);">
                                                    <i class="fa-solid fa-trash"></i>
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
