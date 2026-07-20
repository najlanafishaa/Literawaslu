@extends('layouts.app')

@section('title', 'Edit Buku')
@section('header_title', 'Edit Data Buku')

@section('content')
<div class="card" style="max-width: 650px; margin: 0 auto;">
    <div class="card-header">
        <h2><i class="fa-solid fa-edit" style="color: var(--primary); margin-right: 8px;"></i> Ubah Informasi Buku</h2>
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

        <form action="{{ route('books.update', $book->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="barcode">Kode Buku / Barcode Unik</label>
                <input type="text" name="barcode" id="barcode" class="form-control" placeholder="Contoh: 9786020523315" value="{{ old('barcode', $book->barcode) }}" required>
            </div>

            <div class="form-group">
                <label for="title">Judul Buku</label>
                <input type="text" name="title" id="title" class="form-control" placeholder="Masukkan judul lengkap buku..." value="{{ old('title', $book->title) }}" required>
            </div>

            <div class="form-group">
                <label for="author">Penulis / Pengarang</label>
                <input type="text" name="author" id="author" class="form-control" placeholder="Masukkan nama penulis..." value="{{ old('author', $book->author) }}" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="publisher">Penerbit</label>
                    <input type="text" name="publisher" id="publisher" class="form-control" placeholder="Bentang Pustaka" value="{{ old('publisher', $book->publisher) }}" required>
                </div>
                
                <div class="form-group">
                    <label for="year">Tahun Terbit</label>
                    <input type="number" name="year" id="year" class="form-control" placeholder="2020" value="{{ old('year', $book->year) }}" required min="1000" max="{{ date('Y') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="category">Kategori Buku</label>
                <select name="category" id="category" class="form-control" required style="width: 100%;">
                    <option value="" disabled {{ old('category', $book->category) ? '' : 'selected' }}>-- Pilih Kategori Buku --</option>
                    <option value="Pemerintahan" {{ old('category', $book->category) == 'Pemerintahan' ? 'selected' : '' }}>Pemerintahan</option>
                    <option value="Hukum dan Undang-Undang" {{ old('category', $book->category) == 'Hukum dan Undang-Undang' ? 'selected' : '' }}>Hukum dan Undang-Undang</option>
                    <option value="Politik" {{ old('category', $book->category) == 'Politik' ? 'selected' : '' }}>Politik</option>
                    <option value="Demokrasi" {{ old('category', $book->category) == 'Demokrasi' ? 'selected' : '' }}>Demokrasi</option>
                    <option value="Sosial" {{ old('category', $book->category) == 'Sosial' ? 'selected' : '' }}>Sosial</option>
                    <option value="Keagamaan" {{ old('category', $book->category) == 'Keagamaan' ? 'selected' : '' }}>Keagamaan</option>
                    <option value="Sengketa Pemilu" {{ old('category', $book->category) == 'Sengketa Pemilu' ? 'selected' : '' }}>Sengketa Pemilu</option>
                    <option value="Riset Pilkada" {{ old('category', $book->category) == 'Riset Pilkada' ? 'selected' : '' }}>Riset Pilkada</option>
                    <option value="Akuntansi" {{ old('category', $book->category) == 'Akuntansi' ? 'selected' : '' }}>Akuntansi</option>
                    <option value="Skripsi" {{ old('category', $book->category) == 'Skripsi' ? 'selected' : '' }}>Skripsi</option>
                    <option value="Laporan Hasil Pengawasan" {{ old('category', $book->category) == 'Laporan Hasil Pengawasan' ? 'selected' : '' }}>Laporan Hasil Pengawasan</option>
                    <option value="Motivasi" {{ old('category', $book->category) == 'Motivasi' ? 'selected' : '' }}>Motivasi</option>
                    <option value="Novel" {{ old('category', $book->category) == 'Novel' ? 'selected' : '' }}>Novel</option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi Buku <span style="color: var(--gray-500); font-weight: 400;">(opsional)</span></label>
                <textarea name="description" id="description" class="form-control" rows="4" placeholder="Tulis ringkasan atau deskripsi singkat buku ini...">{{ old('description', $book->description) }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="stock">Jumlah Stok / Salinan Buku</label>
                    <input type="number" name="stock" id="stock" class="form-control" placeholder="1" value="{{ old('stock', $book->stock) }}" required min="1">
                </div>
                
                <div class="form-group">
                    <label for="cover_image">Foto Sampul Buku (Opsional)</label>
                    <input type="file" name="cover_image" id="cover_image" class="form-control" accept="image/*" style="padding: 5px;">
                    <small style="color: var(--gray-600); margin-top: 5px; display: block;">Format: JPG, PNG, GIF. Maksimal 512 KB.</small>
                    @if($book->cover_image)
                        <div style="margin-top: 8px; display: flex; align-items: center; gap: 8px;">
                            <img src="{{ asset($book->cover_image) }}" alt="Sampul" style="height: 45px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <small style="color: var(--gray-600);">Sampul saat ini</small>
                        </div>
                    @endif
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 15px;">
                <i class="fa-solid fa-save"></i> Perbarui Data
            </button>
        </form>
    </div>
</div>
@endsection
