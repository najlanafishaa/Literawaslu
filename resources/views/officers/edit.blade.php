@extends('layouts.app')

@section('title', 'Edit Akun')
@section('header_title', 'Ubah Data Akun')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h2><i class="fa-solid fa-user-gear" style="color: var(--primary); margin-right: 8px;"></i> Ubah Kredensial Akun</h2>
        <a href="{{ route('officers.index') }}" class="btn btn-outline btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    <div class="card-body">
        @if($errors->any())
            <div style="background-color: rgba(var(--primary-rgb), 0.1); border: 1px solid var(--primary); color: var(--primary); padding: 12px; border-radius: var(--border-radius); font-size: 0.85rem; margin-bottom: 20px; font-weight: 500;">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('officers.update', $officer->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $officer->name) }}" required>
            </div>

            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $officer->email) }}" required>
            </div>

            <div class="form-group">
                <label for="password">Ganti Password (Opsional)</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password...">
                <small style="color: var(--gray-600); margin-top: 5px; display: block;">Hanya isi kolom ini jika ingin menyetel ulang kata sandi akun.</small>
            </div>

            <div class="form-group">
                <label for="role">Hak Akses / Peran</label>
                @if($officer->id === auth()->id())
                    <select class="form-control" disabled style="background-color: var(--gray-100); cursor: not-allowed;">
                        <option value="{{ $officer->role }}" selected>
                            {{ $officer->role === 'super_admin' ? 'Super Admin' : 'Petugas' }} (Akun Anda)
                        </option>
                    </select>
                    <input type="hidden" name="role" value="{{ $officer->role }}">
                    <small style="color: var(--gray-600); margin-top: 5px; display: block;">Anda tidak dapat mengubah peran atau hak akses akun Anda sendiri.</small>
                @else
                    <select name="role" id="role" class="form-control" required>
                        <option value="super_admin" {{ old('role', $officer->role) === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        <option value="petugas" {{ old('role', $officer->role) === 'petugas' ? 'selected' : '' }}>Petugas (Admin Biasa)</option>
                        <option value="member" {{ old('role', $officer->role) === 'member' ? 'selected' : '' }}>Member / Anggota</option>
                    </select>
                    <small style="color: var(--gray-600); margin-top: 5px; display: block;">Mengubah peran ke 'Member' akan membuat profil anggota baru untuk akun ini.</small>
                @endif
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 15px;">
                <i class="fa-solid fa-save"></i> Perbarui Akun
            </button>
        </form>
    </div>
</div>
@endsection
