@extends('layouts.app')

@section('title', 'Daftar Pengguna')

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
            <div class="alert alert-danger">
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
                    {{-- Persyaratan Password --}}
                    <div id="password-requirements" style="margin-top: 10px; padding: 12px 14px; background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 10px; display: none;">
                        <p style="font-size: 12px; font-weight: 600; color: #6B7280; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.04em;">Persyaratan Password</p>
                        <div style="display: flex; flex-direction: column; gap: 5px;">
                            <div class="req-item" id="req-length" style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #9CA3AF; transition: color 200ms ease;">
                                <i class="fa-regular fa-circle-xmark" style="font-size: 14px;"></i>
                                <span>Minimal 6 karakter</span>
                            </div>
                            <div class="req-item" id="req-upper" style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #9CA3AF; transition: color 200ms ease;">
                                <i class="fa-regular fa-circle-xmark" style="font-size: 14px;"></i>
                                <span>Mengandung huruf besar (A–Z)</span>
                            </div>
                            <div class="req-item" id="req-lower" style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #9CA3AF; transition: color 200ms ease;">
                                <i class="fa-regular fa-circle-xmark" style="font-size: 14px;"></i>
                                <span>Mengandung huruf kecil (a–z)</span>
                            </div>
                            <div class="req-item" id="req-number" style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #9CA3AF; transition: color 200ms ease;">
                                <i class="fa-regular fa-circle-xmark" style="font-size: 14px;"></i>
                                <span>Mengandung angka (0–9)</span>
                            </div>
                        </div>
                    </div>
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

            <div class="alert alert-warning" style="display: block; font-size: 0.8rem;">
                <i class="fa-solid fa-circle-info" style="color: var(--secondary); margin-right: 5px;"></i>
                Setelah pendaftaran selesai, sistem secara otomatis akan menerbitkan kartu digital perpustakaan dan memberikan poin bonus pendaftaran sebesar <strong>10 Poin</strong>!
            </div>

            <button type="submit" id="registerBtn" class="btn btn-primary" style="width: 100%; opacity: 0.5; cursor: not-allowed;" disabled>
                <i class="fa-solid fa-user-plus"></i> Daftar & Terbitkan Kartu
            </button>
        </form>

        <div style="text-align: center; margin-top: 25px; font-size: 0.85rem; color: var(--gray-600);">
            Sudah terdaftar sebagai pengguna? <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 600; text-decoration: none; border-bottom: 1px dashed var(--primary);">Masuk ke Akun</a>
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

    function setReq(id, passed) {
        const el = document.getElementById(id);
        const icon = el.querySelector('i');
        if (passed) {
            el.style.color = '#15803D';
            icon.className = 'fa-solid fa-circle-check';
            icon.style.color = '#15803D';
        } else {
            el.style.color = '#9CA3AF';
            icon.className = 'fa-regular fa-circle-xmark';
            icon.style.color = '#9CA3AF';
        }
    }

    function checkPasswordStrength(val) {
        const btn  = document.getElementById('registerBtn');
        const reqs = document.getElementById('password-requirements');

        const hasLength = val.length >= 6;
        const hasUpper  = /[A-Z]/.test(val);
        const hasLower  = /[a-z]/.test(val);
        const hasNumber = /[0-9]/.test(val);

        // Show/hide the checklist box
        reqs.style.display = val.length > 0 ? 'block' : 'none';

        setReq('req-length', hasLength);
        setReq('req-upper',  hasUpper);
        setReq('req-lower',  hasLower);
        setReq('req-number', hasNumber);

        passwordIsStrong = hasLength && hasUpper && hasLower && hasNumber;

        btn.disabled = !passwordIsStrong;
        btn.style.opacity = passwordIsStrong ? '1' : '0.5';
        btn.style.cursor  = passwordIsStrong ? 'pointer' : 'not-allowed';
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
