@extends('layouts.app')

@section('title', 'Pertanyaan Keamanan')

@section('content')
<div class="auth-wrapper">
    <div class="auth-box">
        <div class="auth-logo" style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
            <img src="{{ asset('images/logo-bawaslu.png') }}" alt="Logo Bawaslu" style="height: 60px; width: auto; object-fit: contain; margin-bottom: 5px;">
            <div style="font-size: 1.8rem; font-weight: 700; color: var(--dark); line-height: 1;">
                Litera<span style="color: var(--primary);">waslu</span>
            </div>
            <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-600); font-weight: 700; line-height: 1;">
                Bawaslu Prov. Lampung
            </div>
        </div>

        <h3 style="text-align: center; margin-top: 15px; margin-bottom: 10px; font-weight: 600; color: var(--dark);">Verifikasi Keamanan</h3>
        <p style="font-size: 0.85rem; color: var(--gray-600); text-align: center; margin-bottom: 20px;">Silakan jawab pertanyaan keamanan di bawah ini untuk mengajukan reset password.</p>

        @if(session('error'))
            <div style="background-color: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px; border-radius: var(--border-radius); font-size: 0.85rem; margin-bottom: 20px; font-weight: 500;">
                <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('password.security_verify') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ $user->email }}">
            
            <div class="form-group" style="background-color: var(--gray-100); padding: 15px; border-radius: var(--border-radius); margin-bottom: 20px;">
                <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--gray-600); font-weight: bold; display: block; margin-bottom: 5px;">Pertanyaan Keamanan Anda</span>
                <strong style="color: var(--dark); font-size: 0.95rem;">{{ $user->security_question }}</strong>
            </div>

            <div class="form-group">
                <label for="security_answer">Jawaban Anda</label>
                <input type="text" name="security_answer" id="security_answer" class="form-control" placeholder="Masukkan jawaban Anda..." required autofocus autocomplete="off">
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 15px;">
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fa-solid fa-key"></i> Verifikasi Jawaban & Buat Password Baru
                </button>
                <button type="submit" name="request_admin" value="1" class="btn btn-outline" style="width: 100%; border-color: var(--gray-300); color: var(--dark);" onclick="return confirm('Ajukan bantuan reset password secara manual ke Admin/Petugas perpustakaan?');">
                    <i class="fa-solid fa-headset"></i> Lupa Jawaban? Ajukan Reset ke Admin
                </button>
            </div>
        </form>

        <div style="text-align: center; margin-top: 25px; font-size: 0.85rem; color: var(--gray-600);">
            Kembali ke <a href="{{ route('password.request') }}" style="color: var(--primary); font-weight: 600; text-decoration: none; border-bottom: 1px dashed var(--primary);">Ubah Email</a>
        </div>
    </div>
</div>
@endsection
