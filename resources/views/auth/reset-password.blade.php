@extends('layouts.app')

@section('title', 'Buat Kata Sandi Baru')

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

        <h3 style="text-align: center; margin-top: 15px; margin-bottom: 10px; font-weight: 600; color: var(--dark);">Buat Kata Sandi Baru</h3>
        <p style="font-size: 0.85rem; color: var(--gray-600); text-align: center; margin-bottom: 20px;">Silakan buat kata sandi baru Anda di bawah ini.</p>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            
            <div class="form-group">
                <label for="password">Kata Sandi Baru</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required autofocus style="padding-right: 40px;">
                    <button type="button" onclick="togglePassword('password', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--gray-500); cursor: pointer; padding: 0;">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                <div style="position: relative;">
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="••••••••" required style="padding-right: 40px;">
                    <button type="button" onclick="togglePassword('password_confirmation', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--gray-500); cursor: pointer; padding: 0;">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 15px;">
                <i class="fa-solid fa-key"></i> Perbarui Kata Sandi
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endsection
