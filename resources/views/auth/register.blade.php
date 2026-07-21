@extends('layouts.app')

@section('title', 'Daftar Anggota')

@section('content')
<div class="auth-wrapper">
    <div class="auth-box" style="max-width: 500px;">
        <div class="auth-logo" style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
            <img src="{{ asset('images/logo-bawaslu.png') }}" alt="Logo Bawaslu" style="height: 60px; width: auto; object-fit: contain; margin-bottom: 5px;">
            <div style="font-size: 1.8rem; font-weight: 700; color: var(--dark); line-height: 1;">
                Litera<span style="color: var(--primary);">waslu</span>
            </div>
            <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-600); font-weight: 700; line-height: 1;">
                Bawaslu Prov. Lampung
            </div>
        </div>

        @if($errors->any())
            <div style="background-color: rgba(var(--primary-rgb), 0.1); border: 1px solid var(--primary); color: var(--primary); padding: 12px; border-radius: var(--border-radius); font-size: 0.85rem; margin-bottom: 20px; font-weight: 500;">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Ahmad Yani" value="{{ old('name') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="ahmad@literawaslu.com" value="{{ old('email') }}" required>
            </div>


            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required style="padding-right: 40px;" oninput="checkPasswordStrength(this.value)">
                        <button type="button" onclick="togglePassword('password', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--gray-500); cursor: pointer; padding: 0;">
                            <i class="fa-regular fa-eye-slash"></i>
                        </button>
                    </div>
                    {{-- Indikator kekuatan password --}}
                    <div id="password-strength-bar" style="height: 5px; border-radius: 3px; margin-top: 6px; transition: all 0.3s; width: 0%; background: #e74c3c;"></div>
                    <div id="password-strength-label" style="font-size: 0.75rem; margin-top: 4px; font-weight: 600;"></div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <div style="position: relative;">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="••••••••" required style="padding-right: 40px;">
                        <button type="button" onclick="togglePassword('password_confirmation', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--gray-500); cursor: pointer; padding: 0;">
                            <i class="fa-regular fa-eye-slash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="security_question">Pertanyaan Keamanan</label>
                <select name="security_question" id="security_question" class="form-control" required style="width: 100%;">
                    <option value="" disabled selected>-- Pilih Pertanyaan Keamanan --</option>
                    <option value="Siapa nama hewan peliharaan Anda?" {{ old('security_question') === 'Siapa nama hewan peliharaan Anda?' ? 'selected' : '' }}>Siapa nama hewan peliharaan Anda?</option>
                    <option value="Apa nama hewan favorit Anda?" {{ old('security_question') === 'Apa nama hewan favorit Anda?' ? 'selected' : '' }}>Apa nama hewan favorit Anda?</option>
                </select>
            </div>

            <div class="form-group">
                <label for="security_answer">Jawaban Keamanan</label>
                <input type="text" name="security_answer" id="security_answer" class="form-control" placeholder="Tulis jawaban Anda di sini..." value="{{ old('security_answer') }}" required>
            </div>

            <div class="form-group" style="background-color: rgba(var(--secondary-rgb), 0.05); border: 1px dashed var(--secondary); padding: 12px; border-radius: var(--border-radius); font-size: 0.8rem; color: var(--gray-700); margin-bottom: 25px;">
                <i class="fa-solid fa-circle-info" style="color: var(--secondary); margin-right: 5px;"></i>
                Setelah pendaftaran selesai, sistem secara otomatis akan menerbitkan kartu digital perpustakaan dan memberikan poin bonus pendaftaran sebesar <strong>10 Poin</strong>!
            </div>

            <button type="submit" id="registerBtn" class="btn btn-primary" style="width: 100%; opacity: 0.5; cursor: not-allowed;" disabled>
                <i class="fa-solid fa-user-plus"></i> Daftar & Terbitkan Kartu
            </button>
        </form>

        <div style="text-align: center; margin-top: 25px; font-size: 0.85rem; color: var(--gray-600);">
            Sudah terdaftar sebagai anggota? <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 600; text-decoration: none; border-bottom: 1px dashed var(--primary);">Masuk ke Akun</a>
        </div>
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
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    }

    let passwordIsStrong = false;

    function checkPasswordStrength(val) {
        const bar   = document.getElementById('password-strength-bar');
        const label = document.getElementById('password-strength-label');
        const btn   = document.getElementById('registerBtn');
        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[a-z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        if (val.length === 0) {
            bar.style.width = '0%'; bar.style.background = ''; label.textContent = '';
            passwordIsStrong = false;
            btn.disabled = true;
            btn.style.opacity = '0.5';
            btn.style.cursor = 'not-allowed';
            return;
        }
        if (score <= 2) {
            bar.style.width = '33%'; bar.style.background = '#e74c3c';
            label.style.color = '#e74c3c'; label.textContent = '🔴 Lemah — Password belum cukup kuat untuk mendaftar';
            passwordIsStrong = false;
        } else if (score === 3 || score === 4) {
            bar.style.width = '66%'; bar.style.background = '#f39c12';
            label.style.color = '#f39c12'; label.textContent = '🟡 Sedang — Tambahkan simbol/angka agar lebih kuat';
            passwordIsStrong = false;
        } else {
            bar.style.width = '100%'; bar.style.background = '#27ae60';
            label.style.color = '#27ae60'; label.textContent = 'Kuat - Kata sandi aman dan siap digunakan';
            passwordIsStrong = true;
        }

        btn.disabled = !passwordIsStrong;
        btn.style.opacity = passwordIsStrong ? '1' : '0.5';
        btn.style.cursor = passwordIsStrong ? 'pointer' : 'not-allowed';
    }

    // Block form submit if password is not strong
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!passwordIsStrong) {
            e.preventDefault();
            showToast('Password harus berstatus KUAT sebelum Anda dapat mendaftar.', 'danger');
        }
    });
</script>
@endsection
