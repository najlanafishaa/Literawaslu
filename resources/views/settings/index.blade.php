@extends('layouts.app')

@section('title', 'Pengaturan')
@section('header_title', 'Pengaturan Sistem')

@section('content')
<div class="welcome-banner" style="margin-bottom: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; width: 100%;">
        <div>
            <h1>Pengaturan & Integrasi Perpustakaan</h1>
            <p>Atur identitas perpustakaan, parameter operasional, durasi pinjam, denda, dan kelola integrasi data Google Sheets & Google Sites.</p>
        </div>
        <div>
            <i class="fa-solid fa-sliders" style="font-size: 3rem; color: var(--light); opacity: 0.9;"></i>
        </div>
    </div>
</div>

@if($errors->any())
    <div style="background-color: rgba(var(--primary-rgb), 0.1); border: 1px solid var(--primary); color: var(--primary); padding: 12px; border-radius: var(--border-radius); font-size: 0.85rem; margin-bottom: 20px; font-weight: 500;">
        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
    </div>
@endif

<form action="{{ route('settings.update') }}" method="POST">
    @csrf
    
    <div class="dashboard-grid" style="grid-template-columns: 1fr; gap: 25px; margin-bottom: 25px;">
        <!-- Column 1: Library Identity & Operational Parameters -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fa-solid fa-sliders" style="color: var(--primary); margin-right: 8px;"></i> Aturan & Parameter Operasional</h2>
            </div>
            <div class="card-body" style="padding: 25px; display: flex; flex-direction: column; gap: 20px;">
                <div class="form-group">
                    <label for="library_name" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; color: var(--dark);">
                        Nama Perpustakaan:
                    </label>
                    <input type="text" name="library_name" id="library_name" class="form-control" 
                           value="{{ $libraryName }}" placeholder="Contoh: Perpustakaan Literawaslu Bawaslu Lampung" required>
                </div>

                <div class="form-group">
                    <label for="loan_duration" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; color: var(--dark);">
                        Durasi Maksimal Peminjaman (Hari):
                    </label>
                    <input type="number" name="loan_duration" id="loan_duration" class="form-control" 
                           value="{{ $loanDuration }}" placeholder="7" required min="1">
                    <small style="color: var(--gray-600); display: block; margin-top: 5px; font-size: 0.8rem;">
                        Tenggat waktu pengembalian buku terhitung setelah checkout dilakukan.
                    </small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="reward_points" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; color: var(--dark);">
                            Poin Reward per Transaksi Sukses:
                        </label>
                        <input type="number" name="reward_points" id="reward_points" class="form-control" 
                               value="{{ $rewardPoints }}" placeholder="10" required min="0">
                    </div>
                </div>
            </div>
    </div>

    <!-- Sticky Bottom Form Action Card -->
    <div class="card" style="margin-bottom: 25px;">
        <div class="card-body" style="padding: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h3 style="font-size: 0.95rem; font-weight: 600; color: var(--dark);">Simpan Seluruh Pengaturan</h3>
                <p style="font-size: 0.8rem; color: var(--gray-600);">Tekan simpan untuk memperbarui aturan perpustakaan dan data integrasi eksternal.</p>
            </div>
            <button type="submit" class="btn btn-primary" style="padding: 10px 30px;">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Pengaturan
            </button>
        </div>
    </div>
</form>
@endsection
