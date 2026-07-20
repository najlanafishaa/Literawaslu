@extends('layouts.app')

@section('title', 'Edit Member')
@section('header_title', 'Ubah Informasi Member')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h2><i class="fa-solid fa-user-pen" style="color: var(--primary); margin-right: 8px;"></i> Ubah Status Member</h2>
        <a href="{{ route('members.index') }}" class="btn btn-outline btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    <div class="card-body">
        @if($errors->any())
            <div style="background-color: rgba(var(--primary-rgb), 0.1); border: 1px solid var(--primary); color: var(--primary); padding: 12px; border-radius: var(--border-radius); font-size: 0.85rem; margin-bottom: 20px; font-weight: 500;">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('members.update', $member->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Kode Member</label>
                <input type="text" class="form-control" value="{{ $member->member_code }}" disabled style="background-color: var(--gray-100); font-family: monospace; font-weight: 700; cursor: not-allowed;">
            </div>

            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $member->user->name) }}" required>
            </div>

            <div class="form-group">
                <label>Alamat Email</label>
                <input type="email" class="form-control" value="{{ $member->user->email }}" disabled style="background-color: var(--gray-100); cursor: not-allowed;">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="points">Poin Reward</label>
                    <input type="number" name="points" id="points" class="form-control" value="{{ old('points', $member->points) }}" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="borrow_limit">Batas Maksimal Pinjam</label>
                    <input type="number" name="borrow_limit" id="borrow_limit" class="form-control" value="{{ old('borrow_limit', $member->borrow_limit) }}" min="1" max="10" required>
                </div>
            </div>
            <div class="form-group">
                <label for="role">Hak Akses / Peran</label>
                <select name="role" id="role" class="form-control" required>
                    <option value="user" {{ old('role', $member->user->role) === 'user' ? 'selected' : '' }}>Pengguna / Anggota</option>
                    <option value="admin" {{ old('role', $member->user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                <small style="color: var(--gray-600); margin-top: 5px; display: block;">Mengubah peran ke 'Admin' akan menghapus profil anggota ini namun tetap menyimpan akun pengguna.</small>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 15px;">
                <i class="fa-solid fa-save"></i> Perbarui Status Anggota
            </button>
        </form>
    </div>
</div>
@endsection
