@extends('layouts.app')

@section('title', 'Manajemen Akun')
@section('header_title', 'Manajemen Akun Sistem')

@section('content')
<div class="card" style="margin-bottom: 25px;">
    <div class="card-header">
        <h2 style="margin: 0;"><i class="ti ti-user-plus" style="color: var(--primary); margin-right: 8px;"></i> Buat Akun Baru</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('accounts.store') }}" method="POST">
            @csrf
            <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                <div style="flex: 1; min-width: 250px;">
                    <label style="font-weight: bold; margin-bottom: 5px; display: block;">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div style="flex: 1; min-width: 250px;">
                    <label style="font-weight: bold; margin-bottom: 5px; display: block;">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div style="flex: 1; min-width: 250px;">
                    <label style="font-weight: bold; margin-bottom: 5px; display: block;">Password</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-weight: bold; margin-bottom: 5px; display: block;">Peran / Hak Akses</label>
                    <select name="role" class="form-control" required>
                        <option value="petugas">Admin</option>
                        <option value="member">Pengguna Perpustakaan</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>
                <div style="display: flex; align-items: flex-end; padding-bottom: 2px;">
                    <button type="submit" class="btn btn-primary"><i class="ti ti-plus"></i> Buat Akun</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Daftar Semua Akun</h2>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Peran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role === 'super_admin')
                                    <span class="badge badge-danger">Super Admin</span>
                                @elseif($user->role === 'petugas')
                                    <span class="badge badge-warning">Admin</span>
                                @else
                                    <span class="badge badge-secondary">Pengguna</span>
                                @endif
                            </td>
                            <td>
                                @if($user->role === 'super_admin' && auth()->id() !== $user->id)
                                    <form action="{{ route('accounts.demote', $user->id) }}" method="POST" onsubmit="return confirm('Turunkan Super Admin ini menjadi Admin?');" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-outline btn-sm">
                                            <i class="ti ti-chevron-down"></i> Turunkan Akses
                                        </button>
                                    </form>
                                @endif
                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('accounts.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini secara permanen?');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline btn-sm" style="color: var(--primary);">
                                            <i class="ti ti-trash"></i> Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
