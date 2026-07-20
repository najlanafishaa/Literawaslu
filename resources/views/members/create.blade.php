@extends('layouts.app')

@section('title', 'Tambah Member')
@section('header_title', 'Daftarkan Member Baru')

@section('content')
<div class="welcome-banner" style="margin-bottom: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; width: 100%;">
        <div>
            <h1>Registrasi Member Manual</h1>
            <p>Daftarkan akun anggota perpustakaan secara manual dari panel administrator.</p>
        </div>
        <div>
            <i class="fa-solid fa-user-plus" style="font-size: 3rem; color: var(--light); opacity: 0.9;"></i>
        </div>
    </div>
</div>

@if($errors->any())
    <div style="background-color: rgba(var(--primary-rgb), 0.1); border: 1px solid var(--primary); color: var(--primary); padding: 12px; border-radius: var(--border-radius); font-size: 0.85rem; margin-bottom: 20px; font-weight: 500;">
        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
    </div>
@endif

<div class="dashboard-grid" style="grid-template-columns: 1fr; gap: 25px; margin-bottom: 25px; max-width: 800px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-address-card" style="color: var(--primary); margin-right: 8px;"></i> Data Member Baru</h2>
        </div>
        <div class="card-body" style="padding: 25px;">
            <form action="{{ route('members.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
                @csrf
                
                <div class="form-group">
                    <label for="name" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; color: var(--dark);">
                        Nama Lengkap:
                    </label>
                    <input type="text" name="name" id="name" class="form-control" 
                           placeholder="Contoh: Ahmad Subardjo" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="email" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; color: var(--dark);">
                        Alamat Email:
                    </label>
                    <input type="email" name="email" id="email" class="form-control" 
                           placeholder="Contoh: ahmad@gmail.com" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label for="password" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; color: var(--dark);">
                        Kata Sandi Masuk:
                    </label>
                    <input type="password" name="password" id="password" class="form-control" 
                           placeholder="Minimal 6 karakter" required>
                    <small style="color: var(--gray-600); display: block; margin-top: 5px; font-size: 0.8rem;">
                        Kata sandi awal yang akan digunakan member untuk masuk ke sistem.
                    </small>
                </div>

                <div class="form-group">
                    <label for="security_question" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; color: var(--dark);">Pertanyaan Keamanan:</label>
                    <select name="security_question" id="security_question" class="form-control" required style="width: 100%;">
                        <option value="" disabled selected>-- Pilih Pertanyaan Keamanan --</option>
                        <option value="Siapa nama hewan peliharaan Anda?" {{ old('security_question') === 'Siapa nama hewan peliharaan Anda?' ? 'selected' : '' }}>Siapa nama hewan peliharaan Anda?</option>
                        <option value="Apa nama hewan favorit Anda?" {{ old('security_question') === 'Apa nama hewan favorit Anda?' ? 'selected' : '' }}>Apa nama hewan favorit Anda?</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="security_answer" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; color: var(--dark);">Jawaban Keamanan:</label>
                    <input type="text" name="security_answer" id="security_answer" class="form-control" placeholder="Tulis jawaban..." value="{{ old('security_answer') }}" required>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <a href="{{ route('members.index') }}" class="btn btn-outline" style="padding: 12px 30px; text-decoration: none;">Batal</a>
                    <button type="submit" class="btn btn-primary" style="padding: 12px 30px;">
                        <i class="fa-solid fa-user-plus"></i> Daftarkan Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
