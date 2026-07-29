@extends('layouts.app')

@section('title', 'Edit Profil')
@section('header_title', 'Pengaturan Profil Akun')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h2>Informasi Akun</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom: 20px;">
                <label style="font-weight: bold; margin-bottom: 5px; display: block;">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <small style="color: var(--primary);">{{ $message }}</small>
                @enderror
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="font-weight: bold; margin-bottom: 5px; display: block;">Alamat Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <small style="color: var(--primary);">{{ $message }}</small>
                @enderror
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="font-weight: bold; margin-bottom: 5px; display: block;">Password Baru (Opsional)</label>
                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                @error('password')
                    <small style="color: var(--primary);">{{ $message }}</small>
                @enderror
            </div>
            
            <div style="margin-bottom: 25px;">
                <label style="font-weight: bold; margin-bottom: 5px; display: block;">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru jika diisi">
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="ti ti-device-floppy"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
