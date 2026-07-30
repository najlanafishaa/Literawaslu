@extends('layouts.app')

@section('title', 'Lupa Kata Sandi')

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

        <h3 style="text-align: center; margin-top: 15px; margin-bottom: 15px; font-weight: 600; color: var(--dark);">Lupa Kata Sandi</h3>

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="ti ti-circle-x"></i> {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success" style="margin-top: 16px;">
                <i class="ti ti-check-circle"></i>
                <div>
                    Permintaan reset password telah berhasil dikirim. Silakan menunggu proses verifikasi dari Admin. Apabila membutuhkan bantuan atau ingin menanyakan status pengajuan, silakan hubungi Admin melalui email di bawah ini.
                    <br><br>
                    <a href="https://mail.google.com/mail/?view=cm&fs=1&to=literawasluu@gmail.com" target="_blank" rel="noopener noreferrer" style="color: var(--primary); font-weight: 600; text-decoration: underline;">literawasluu@gmail.com</a>
                </div>
            </div>
        @endif

        @if(session('simulated_link'))
            <div class="alert alert-warning" style="display: block;">
                <p><strong>[DEMO SIMULASI]</strong> Link Atur Ulang Kata Sandi:</p>
                <a href="{{ session('simulated_link') }}" style="word-break: break-all; color: var(--primary); font-weight: bold;">{{ session('simulated_link') }}</a>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="email">Alamat Email Terdaftar</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus>
                <small style="color: var(--gray-600); margin-top: 5px; display: block;">Untuk pengguna, Anda perlu menjawab pertanyaan keamanan setelah ini.</small>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 15px;">
                <i class="ti ti-arrow-right"></i> Lanjutkan
            </button>
        </form>

        <div class="alert alert-info" style="margin-top: 16px;">
            <i class="ti ti-info-circle"></i>
            <div>
                <strong>Sudah mengajukan permintaan ke Admin?</strong> Masukkan email Anda kembali di atas dan klik <em>Lanjutkan</em> — jika Admin sudah menyetujui, Anda akan langsung diarahkan ke halaman buat kata sandi baru.
            </div>
        </div>



        <div style="text-align: center; margin-top: 25px; font-size: 0.85rem; color: var(--gray-600);">
            Kembali ke <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 600; text-decoration: none; border-bottom: 1px dashed var(--primary);">Halaman Masuk</a>
        </div>
    </div>
</div>
@endsection
