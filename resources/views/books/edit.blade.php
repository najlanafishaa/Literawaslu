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
                <select name="category" id="category" class="form-control" required style="width: 100%;" onchange="toggleNewCategoryInput()">
                    <option value="" disabled {{ old('category', $book->category) ? '' : 'selected' }}>-- Pilih Kategori Buku --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category', $book->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                    <option value="new_category_option" {{ old('category') == 'new_category_option' || old('new_category') ? 'selected' : '' }}>[+] Input Kategori Manual / Baru</option>
                </select>
                <small style="color: var(--gray-600); margin-top: 5px; display: block;">
                    Kategori tidak terdaftar? <a href="{{ route('categories.index') }}" style="color: var(--primary); font-weight: 600; text-decoration: none; border-bottom: 1px dashed var(--primary);">Kelola Kategori Buku di sini</a>.
                </small>
            </div>

            <div class="form-group" id="newCategoryInputGroup" style="display: {{ old('category') == 'new_category_option' || old('new_category') ? 'block' : 'none' }};">
                <label for="new_category">Nama Kategori Baru</label>
                <input type="text" name="new_category" id="new_category" class="form-control" placeholder="Contoh: Novel, Biografi, Sejarah..." value="{{ old('new_category') }}">
                <small style="color: var(--gray-600); margin-top: 5px; display: block;">Kategori baru ini akan otomatis disimpan ke sistem saat Anda memperbarui buku.</small>
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

            <div class="form-group">
                <label for="drive_link"><i class="fa-brands fa-google-drive" style="color: #4285F4; margin-right: 4px;"></i> Link Baca Online (Google Drive) <span style="color: var(--gray-500); font-weight: 400;">(opsional)</span></label>
                <input type="url" name="drive_link" id="drive_link" class="form-control" placeholder="https://drive.google.com/file/d/FILE_ID/view" value="{{ old('drive_link', $book->drive_link) }}">
                <small style="color: var(--gray-600); margin-top: 5px; display: block;">Masukkan link Google Drive agar member bisa membaca buku secara online. Buku hanya bisa ditampilkan (preview), tidak bisa diunduh.</small>
                @if($book->drive_link)
                    <div style="margin-top: 8px; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-link" style="color: var(--success);"></i>
                        <small style="color: var(--success); font-weight: 600;">Link baca online sudah tersedia</small>
                    </div>
                @endif
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 15px;">
                <i class="fa-solid fa-save"></i> Perbarui Data
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleNewCategoryInput() {
        const select = document.getElementById('category');
        const inputGroup = document.getElementById('newCategoryInputGroup');
        const input = document.getElementById('new_category');
        if (select.value === 'new_category_option') {
            inputGroup.style.display = 'block';
            input.setAttribute('required', 'required');
            input.focus();
        } else {
            inputGroup.style.display = 'none';
            input.removeAttribute('required');
        }
    }
</script>
@endsection
