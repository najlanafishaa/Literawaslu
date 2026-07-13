@extends('layouts.app')

@section('title', 'Kelola Admin & Petugas')
@section('header_title', 'Kelola Admin & Petugas')

@section('content')
<div class="card" style="margin-bottom: 25px;">
    <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; padding: 20px;">
        <p style="font-size: 0.9rem; color: var(--gray-600); margin: 0;">Gunakan halaman ini untuk mendaftarkan dan mengelola akun administrator (Super Admin) serta petugas sirkulasi.</p>
        <a href="{{ route('officers.create') }}" class="btn btn-secondary" style="text-decoration: none;">
            <i class="fa-solid fa-user-plus"></i> Tambah Akun Baru
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Daftar Akun Admin & Petugas</h2>
        <span class="badge badge-success">{{ $officers->count() }} Pengguna</span>
    </div>
    
    <div class="card-body">
        @if($officers->isEmpty())
            <p style="text-align: center; color: var(--gray-600); padding: 20px;">Belum ada akun admin atau petugas terdaftar.</p>
        @else
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>ID Pengguna</th>
                            <th>Nama Lengkap</th>
                            <th>Alamat Email</th>
                            <th>Peran / Hak Akses</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($officers as $officer)
                            <tr>
                                <td>#{{ $officer->id }}</td>
                                <td><strong>{{ $officer->name }}</strong></td>
                                <td>{{ $officer->email }}</td>
                                <td>
                                    @if($officer->role === 'super_admin')
                                        <span class="badge badge-success" style="background-color: rgba(var(--primary-rgb), 0.1); color: var(--primary); border: 1px solid rgba(var(--primary-rgb), 0.2);">Super Admin</span>
                                    @else
                                        <span class="badge badge-secondary" style="background-color: var(--dark); color: var(--light);">Petugas</span>
                                    @endif
                                </td>
                                <td>{{ $officer->created_at->format('d M Y') }}</td>
                                <td>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <a href="{{ route('officers.edit', $officer->id) }}" class="btn btn-outline btn-sm" title="Edit Akun" style="padding: 6px 10px;">
                                            <i class="fa-solid fa-user-gear"></i>
                                        </a>
                                        @if($officer->id !== auth()->id())
                                            <form action="{{ route('officers.destroy', $officer->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini? Akun tersebut tidak akan bisa masuk lagi.');" style="margin: 0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline btn-sm" title="Hapus Akun" style="padding: 6px 10px; color: var(--primary); border-color: rgba(227,30,36,0.2);">
                                                    <i class="fa-solid fa-user-slash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span style="font-size: 0.75rem; color: var(--gray-600); font-style: italic; padding-left: 5px;">Akun Anda</span>
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
