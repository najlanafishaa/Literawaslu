@extends('layouts.app')

@section('title', 'Tambah Akun')
@section('header_title', 'Registrasi Akun Baru')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h2><i class="fa-solid fa-user-plus" style="color: var(--primary); margin-right: 8px;"></i> Buat Akun Admin / Petugas</h2>
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

        <form action="{{ route('officers.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Nama Lengkap" value="{{ old('name') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="contoh@literawaslu.com" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="password">Password Default</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 6 karakter..." required>
                <small style="color: var(--gray-600); margin-top: 5px; display: block;">Berikan password sementara. Pengguna dapat menggantinya nanti.</small>
            </div>

            <div class="form-group">
                <label for="role">Peran / Hak Akses</label>
                <select name="role" id="role" class="form-control" required>
                    <option value="petugas" {{ old('role') === 'petugas' ? 'selected' : '' }}>Petugas (Admin Biasa)</option>
                    <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
                <small style="color: var(--gray-600); margin-top: 5px; display: block;">Peran menentukan tingkat kewenangan akses fitur sistem.</small>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 15px;">
                <i class="fa-solid fa-user-shield"></i> Daftarkan Akun
            </button>
        </form>
    </div>
</div>
@endsection
