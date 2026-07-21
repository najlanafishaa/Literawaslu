@extends('layouts.app')

@section('title', 'Manajemen Akun')
@section('header_title', 'Manajemen Akun Sistem')

@section('content')
<div class="card" style="margin-bottom: 25px;">
    <div class="card-header" style="background-color: var(--primary); color: white;">
        <h2 style="color: white; margin: 0;">Buat Akun Baru</h2>
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
                    <label style="font-weight: bold; margin-bottom: 5px; display: block;">Role</label>
                    <select name="role" class="form-control" required>
                        <option value="petugas">Admin Biasa (Petugas)</option>
                        <option value="member">Member Perpustakaan</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>
                <div style="display: flex; align-items: flex-end; padding-bottom: 2px;">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Buat Akun</button>
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
                        <th>Role</th>
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
                                    <span class="badge badge-primary" style="background-color: var(--primary); color: white;">Super Admin</span>
                                @elseif($user->role === 'petugas')
                                    <span class="badge badge-info" style="background-color: #0284c7; color: white;">Admin Biasa</span>
                                @else
                                    <span class="badge badge-secondary">Member</span>
                                @endif
                            </td>
                            <td>
                                @if($user->role === 'super_admin' && auth()->id() !== $user->id)
                                    <form action="{{ route('accounts.demote', $user->id) }}" method="POST" onsubmit="return confirm('Turunkan Super Admin ini menjadi Admin Biasa?');" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-outline btn-sm" style="color: var(--primary); border-color: rgba(227,30,36,0.3);">
                                            <i class="fa-solid fa-arrow-down"></i> Demote
                                        </button>
                                    </form>
                                @endif
                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('accounts.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini secara permanen?');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline btn-sm" style="color: #e31e24; border-color: rgba(227,30,36,0.3);">
                                            <i class="fa-solid fa-trash"></i> Hapus
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
