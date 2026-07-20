@extends('layouts.app')

@section('title', 'Tambah Buku')
@section('header_title', 'Tambah Buku Baru')

@section('content')
<div class="card" style="max-width: 650px; margin: 0 auto;">
    <div class="card-header">
        <h2><i class="fa-solid fa-book-medical" style="color: var(--primary); margin-right: 8px;"></i> Input Data Buku Baru</h2>
        <a href="{{ route('books.index') }}" class="btn btn-outline btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    <div class="card-body">
        @if($errors->any())
            <div style="background-color: rgba(var(--primary-rgb), 0.1); border: 1px solid var(--primary); color: var(--primary); padding: 12px; border-radius: var(--border-radius); font-size: 0.85rem; margin-bottom: 20px; font-weight: 500;">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="barcode">Kode Buku / Barcode Unik</label>
                <div style="display: flex; gap: 8px;">
                    <input type="text" name="barcode" id="barcode" class="form-control" placeholder="Contoh: 9786020523315" value="{{ old('barcode') }}" required>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="generateRandomBarcode()">
                        <i class="fa-solid fa-arrows-rotate"></i> Auto
                    </button>
                </div>
                <small style="color: var(--gray-600); margin-top: 5px; display: block;">Gunakan barcode resmi buku atau buat kode unik tersendiri.</small>
            </div>

            <div class="form-group">
                <label for="title">Judul Buku</label>
                <input type="text" name="title" id="title" class="form-control" placeholder="Masukkan judul lengkap buku..." value="{{ old('title') }}" required>
            </div>

            <div class="form-group">
                <label for="author">Penulis / Pengarang</label>
                <input type="text" name="author" id="author" class="form-control" placeholder="Masukkan nama penulis..." value="{{ old('author') }}" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="publisher">Penerbit</label>
                    <input type="text" name="publisher" id="publisher" class="form-control" placeholder="Bentang Pustaka" value="{{ old('publisher') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="year">Tahun Terbit</label>
                    <input type="number" name="year" id="year" class="form-control" placeholder="2020" value="{{ old('year') }}" required min="1000" max="{{ date('Y') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="category">Kategori Buku</label>
                <select name="category" id="category" class="form-control" required style="width: 100%;">
                    <option value="" disabled {{ old('category') ? '' : 'selected' }}>-- Pilih Kategori Buku --</option>
                    <option value="Pemerintahan" {{ old('category') == 'Pemerintahan' ? 'selected' : '' }}>Pemerintahan</option>
                    <option value="Hukum dan Undang-Undang" {{ old('category') == 'Hukum dan Undang-Undang' ? 'selected' : '' }}>Hukum dan Undang-Undang</option>
                    <option value="Politik" {{ old('category') == 'Politik' ? 'selected' : '' }}>Politik</option>
                    <option value="Demokrasi" {{ old('category') == 'Demokrasi' ? 'selected' : '' }}>Demokrasi</option>
                    <option value="Sosial" {{ old('category') == 'Sosial' ? 'selected' : '' }}>Sosial</option>
                    <option value="Keagamaan" {{ old('category') == 'Keagamaan' ? 'selected' : '' }}>Keagamaan</option>
                    <option value="Sengketa Pemilu" {{ old('category') == 'Sengketa Pemilu' ? 'selected' : '' }}>Sengketa Pemilu</option>
                    <option value="Riset Pilkada" {{ old('category') == 'Riset Pilkada' ? 'selected' : '' }}>Riset Pilkada</option>
                    <option value="Akuntansi" {{ old('category') == 'Akuntansi' ? 'selected' : '' }}>Akuntansi</option>
                    <option value="Skripsi" {{ old('category') == 'Skripsi' ? 'selected' : '' }}>Skripsi</option>
                    <option value="Laporan Hasil Pengawasan" {{ old('category') == 'Laporan Hasil Pengawasan' ? 'selected' : '' }}>Laporan Hasil Pengawasan</option>
                    <option value="Motivasi" {{ old('category') == 'Motivasi' ? 'selected' : '' }}>Motivasi</option>
                    <option value="Novel" {{ old('category') == 'Novel' ? 'selected' : '' }}>Novel</option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi Buku <span style="color: var(--gray-500); font-weight: 400;">(opsional)</span></label>
                <textarea name="description" id="description" class="form-control" rows="4" placeholder="Tulis ringkasan atau deskripsi singkat buku ini...">{{ old('description') }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="stock">Jumlah Stok / Salinan Buku</label>
                    <input type="number" name="stock" id="stock" class="form-control" placeholder="1" value="{{ old('stock', 1) }}" required min="1">
                </div>
                
                <div class="form-group">
                    <label for="cover_image">Foto Sampul Buku (Opsional)</label>
                    <input type="file" name="cover_image" id="cover_image" class="form-control" accept="image/*" style="padding: 5px;">
                    <small style="color: var(--gray-600); margin-top: 5px; display: block;">Format: JPG, PNG, GIF. Maksimal 512 KB.</small>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 15px;">
                <i class="fa-solid fa-save"></i> Simpan Buku
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function generateRandomBarcode() {
        // Generate a 13-digit random number to simulate ISBN barcode
        let code = '';
        for (let i = 0; i < 13; i++) {
            code += Math.floor(Math.random() * 10);
        }
        document.getElementById('barcode').value = code;
        showToast('Barcode simulasi acak berhasil dibuat!', 'success');
    }
</script>
@endsection
