@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="auth-wrapper">
        @if(session('success') === 'Anda telah berhasil keluar dari sistem.')
            <style>
                @keyframes fadeInSoft {
                    from { opacity: 0; transform: translateY(10px); }
                    to { opacity: 1; transform: translateY(0); }
                }
            </style>
            <div style="width: 100%; max-width: 480px; margin: 40px auto; background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); padding: 32px 24px; text-align: center; animation: fadeInSoft 200ms ease-out forwards; font-family: 'Plus Jakarta Sans', sans-serif;">
                <div style="width: 48px; height: 48px; background: #DCFCE7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                    <i class="ti ti-check" style="font-size: 24px; color: #15803D;"></i>
                </div>
                <h2 style="font-weight: 600; font-size: 18px; color: #111827; margin-bottom: 8px;">Berhasil Keluar</h2>
                <p style="font-size: 14px; color: #6B7280; margin-bottom: 24px; line-height: 1.5;">Anda telah berhasil keluar dari sistem. Terima kasih telah menggunakan Literawaslu.</p>
                
                <a href="{{ route('login') }}" style="display: block; width: 100%; background: #D62027; color: white; border-radius: 12px; padding: 12px; font-weight: 500; font-size: 14px; text-decoration: none; transition: background 0.2s ease; margin-bottom: 12px;">Kembali ke Login</a>
                
                <p style="font-size: 12px; color: #9CA3AF; margin: 0;">Anda akan diarahkan ke halaman login dalam 3 detik...</p>
                
                <script>
                    setTimeout(() => {
                        window.location.href = "{{ route('login') }}";
                    }, 3000);
                </script>
            </div>
        @else
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
                        <i class="ti ti-history" style="font-size: 1.4rem; margin-bottom: 8px; display: block; color: var(--secondary);"></i>
                        {{ session('warning') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger" style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: 16px;">
                        <i class="ti ti-circle-x" style="font-size: 1.4rem; margin-bottom: 8px; display: block; color: var(--primary);"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success') && session('success') !== 'Anda telah berhasil keluar dari sistem.')
                    @php
                        $successMsg = session('success');
                        $parts = explode('|||', $successMsg);
                    @endphp
                    <div class="alert alert-success" style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: 16px;">
                        <i class="ti ti-circle-check" style="font-size: 1.4rem; margin-bottom: 8px; display: block; color: #15803d;"></i>
                        <div>{{ $parts[0] }}</div>
                        @if(isset($parts[1]))
                            <div style="margin-top: 10px;">
                                <a href="https://mail.google.com/mail/?view=cm&fs=1&to={{ $parts[1] }}" target="_blank" rel="noopener noreferrer" style="color: var(--primary); font-weight: 600; text-decoration: underline;">{{ $parts[1] }}</a>
                            </div>
                        @endif
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="ti ti-alert-circle"></i> {{ $errors->first() }}
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
                        <i class="ti ti-eye-off"></i>
                    </button>
                </div>
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 8px; margin-bottom: 25px;">
                <input type="checkbox" name="remember" id="remember" style="accent-color: var(--primary); width: 16px; height: 16px; cursor: pointer;">
                <label for="remember" style="margin-bottom: 0; font-size: 0.85rem; cursor: pointer; user-select: none; color: var(--gray-700);">Ingat saya di perangkat ini</label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i class="ti ti-login"></i> Masuk Sekarang
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
        @endif
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
            icon.classList.remove('ti-eye-off');
            icon.classList.add('ti-eye');
        } else {
            input.type = 'password';
            icon.classList.remove('ti-eye');
            icon.classList.add('ti-eye-off');
        }
    }
</script>
@endsection
