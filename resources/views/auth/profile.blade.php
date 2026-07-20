@extends('layouts.app')

@section('title', 'Ubah Profil')
@section('header_title', 'Profil Saya')

@section('content')
<div class="welcome-banner" style="margin-bottom: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; width: 100%;">
        <div>
            <h1>Pengaturan Profil Pengguna</h1>
            <p>Kelola data nama, email, dan kata sandi masuk untuk akun Anda.</p>
        </div>
        <div>
            <i class="fa-solid fa-user-gear" style="font-size: 3rem; color: var(--light); opacity: 0.9;"></i>
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
            <h2><i class="fa-solid fa-address-card" style="color: var(--primary); margin-right: 8px;"></i> Data Akun</h2>
        </div>
        <div class="card-body" style="padding: 25px;">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 20px;">
                @csrf
                

                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="name" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; color: var(--dark);">
                            Nama Lengkap:
                        </label>
                        <input type="text" name="name" id="name" class="form-control" 
                               value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="email" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; color: var(--dark);">
                            Alamat Email:
                        </label>
                        <input type="email" name="email" id="email" class="form-control" 
                               value="{{ old('email', $user->email) }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; color: var(--dark);">
                        Hak Akses / Role:
                    </label>
                    <input type="text" class="form-control" style="background-color: var(--gray-100); cursor: not-allowed;" 
                           value="{{ auth()->user()->role === 'super_admin' ? 'Super Admin' : (in_array(auth()->user()->role, ['admin', 'petugas']) ? 'Admin' : 'Pengguna') }}" disabled>
                    <small style="color: var(--gray-600); display: block; margin-top: 5px; font-size: 0.8rem;">
                        Peran akun diatur oleh administrator sistem dan tidak dapat diubah dari profil ini.
                    </small>
                </div>

                <hr style="border: 0; border-top: 1px solid var(--gray-200); margin: 10px 0;">

                <h3 style="font-size: 1.1rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;">
                    <i class="fa-solid fa-key" style="color: var(--secondary); margin-right: 6px;"></i> Ubah Kata Sandi
                </h3>
                <p style="font-size: 0.8rem; color: var(--gray-600); margin-bottom: 10px;">
                    Kosongkan kolom di bawah ini jika Anda tidak ingin mengubah kata sandi masuk Anda.
                </p>

                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="password" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; color: var(--dark);">
                            Kata Sandi Baru:
                        </label>
                        <input type="password" name="password" id="password" class="form-control"
                               placeholder="Minimal 6 karakter" oninput="checkPasswordStrength(this.value)">
                        <div id="password-strength-bar" style="height: 5px; border-radius: 3px; margin-top: 6px; transition: all 0.3s; width: 0%; background: #e74c3c;"></div>
                        <div id="password-strength-label" style="font-size: 0.75rem; margin-top: 4px; font-weight: 600;"></div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; color: var(--dark);">
                            Konfirmasi Kata Sandi Baru:
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                               placeholder="Ulangi kata sandi baru">
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="padding: 12px 30px;">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function checkPasswordStrength(val) {
        const bar   = document.getElementById('password-strength-bar');
        const label = document.getElementById('password-strength-label');
        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[a-z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        if (val.length === 0) {
            bar.style.width = '0%'; label.textContent = ''; return;
        }
        if (score <= 2) {
            bar.style.width = '33%'; bar.style.background = '#e74c3c';
            label.style.color = '#e74c3c'; label.textContent = 'Kurang Aman';
        } else if (score <= 4) {
            bar.style.width = '66%'; bar.style.background = '#f39c12';
            label.style.color = '#f39c12'; label.textContent = 'Cukup Aman';
        } else {
            bar.style.width = '100%'; bar.style.background = '#27ae60';
            label.style.color = '#27ae60'; label.textContent = 'Sangat Aman';
        }
    }
</script>
@endsection
