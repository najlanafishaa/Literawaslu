@extends('layouts.app')

@section('title', 'Masuk')

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

        @if(session('warning'))
            <div class="alert alert-warning" style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: 16px;">
                <i class="fa-solid fa-clock-rotate-left" style="font-size: 1.4rem; margin-bottom: 8px; display: block; color: var(--secondary);"></i>
                {{ session('warning') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: 16px;">
                <i class="fa-solid fa-circle-xmark" style="font-size: 1.4rem; margin-bottom: 8px; display: block; color: var(--primary);"></i>
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success" style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: 16px;">
                <i class="fa-solid fa-circle-check" style="font-size: 1.4rem; margin-bottom: 8px; display: block; color: #15803d;"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="nama@literawaslu.com" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label for="password" style="margin-bottom: 0;">Password</label>
                    <a href="{{ route('password.request') }}" style="font-size: 0.8rem; color: var(--primary); text-decoration: none; font-weight: 500;">Lupa Password?</a>
                </div>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required style="padding-right: 40px;">
                    <button type="button" onclick="togglePassword('password', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--gray-500); cursor: pointer; padding: 0;">
                        <i class="fa-regular fa-eye-slash"></i>
                    </button>
                </div>
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 8px; margin-bottom: 25px;">
                <input type="checkbox" name="remember" id="remember" style="accent-color: var(--primary); width: 16px; height: 16px; cursor: pointer;">
                <label for="remember" style="margin-bottom: 0; font-size: 0.85rem; cursor: pointer; user-select: none; color: var(--gray-700);">Ingat saya di perangkat ini</label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk Sekarang
            </button>
        </form>

        <div style="text-align: center; margin-top: 25px; font-size: 0.85rem; color: var(--gray-600);">
            Belum punya akun? <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 600; text-decoration: none; border-bottom: 1px dashed var(--primary);">Daftar Pengguna</a>
        </div>
        
        <!-- Role Quick Switcher for Demo -->
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--gray-200); text-align: center;">
            <p style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--gray-600); margin-bottom: 10px; letter-spacing: 0.5px;">Akun Demo (Password: 123):</p>
            <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                <button type="button" class="btn btn-outline btn-sm" onclick="quickFill('admin@literawaslu.com')" style="padding: 4px 8px; font-size: 0.7rem;">Super Admin</button>
                <button type="button" class="btn btn-outline btn-sm" onclick="quickFill('petugas@literawaslu.com')" style="padding: 4px 8px; font-size: 0.7rem;">Admin</button>
                <button type="button" class="btn btn-outline btn-sm" onclick="quickFill('ahmad@literawaslu.com')" style="padding: 4px 8px; font-size: 0.7rem;">Pengguna (Ahmad)</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function quickFill(email) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = '123';
        showToast('Akun demo telah diisi. Silakan klik Masuk!', 'success');
    }

    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    }
</script>
@endsection
