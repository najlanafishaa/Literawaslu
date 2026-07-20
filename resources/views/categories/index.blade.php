@extends('layouts.app')

@section('title', 'Kelola Kategori Buku')
@section('header_title', 'Kelola Kategori')

@section('content')
<div class="dashboard-grid" style="grid-template-columns: 1fr 2fr; gap: 25px; align-items: start;">
    <!-- Add Category Form -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-plus" style="color: var(--primary); margin-right: 8px;"></i> Tambah Kategori</h2>
        </div>
        <div class="card-body" style="padding: 20px;">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="name" style="font-weight: 600; display: block; margin-bottom: 8px; font-size: 0.88rem; color: var(--dark);">Nama Kategori:</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: Novel, Pemilu" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fa-solid fa-save"></i> Simpan Kategori
                </button>
            </form>
        </div>
    </div>

    <!-- Category List Table -->
    <div class="card">
        <div class="card-header" style="justify-content: space-between;">
            <h2><i class="fa-solid fa-tags" style="color: var(--dark); margin-right: 8px;"></i> Daftar Kategori Buku</h2>
            <span class="badge badge-success">{{ $categories->count() }} Kategori</span>
        </div>
        <div class="card-body">
            @if($categories->isEmpty())
                <p style="text-align: center; color: var(--gray-600); padding: 30px;">Belum ada kategori buku yang tersimpan di sistem.</p>
            @else
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th style="width: 60px; text-align: center;">No</th>
                                <th>Nama Kategori</th>
                                <th style="width: 150px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $index => $cat)
                                <tr>
                                    <td style="text-align: center;">{{ $index + 1 }}</td>
                                    <td>
                                        <span id="cat-text-{{ $cat->id }}" style="font-weight: 600; color: var(--dark);">{{ $cat->name }}</span>
                                        
                                        <!-- Inline Edit Form (Hidden by default) -->
                                        <form id="cat-form-{{ $cat->id }}" action="{{ route('categories.update', $cat->id) }}" method="POST" style="display: none; margin: 0; gap: 8px; width: 100%;">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="name" class="form-control" style="padding: 4px 8px; font-size: 0.85rem;" value="{{ $cat->name }}" required>
                                            <button type="submit" class="btn btn-primary btn-sm" style="padding: 4px 8px; font-size: 0.75rem;"><i class="fa-solid fa-check"></i></button>
                                            <button type="button" onclick="cancelEdit({{ $cat->id }})" class="btn btn-outline btn-sm" style="padding: 4px 8px; font-size: 0.75rem;"><i class="fa-solid fa-times"></i></button>
                                        </form>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 8px; justify-content: center;">
                                            <button id="cat-edit-btn-{{ $cat->id }}" onclick="enableEdit({{ $cat->id }})" class="btn btn-secondary btn-sm" style="padding: 5px 10px; font-size: 0.75rem; background-color: var(--gray-100); border-color: var(--gray-200); color: var(--dark);">
                                                <i class="fa-solid fa-pencil"></i> Ubah
                                            </button>
                                            
                                            <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori \'{{ $cat->name }}\'? Ini akan mempengaruhi penyaringan kategori buku.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline btn-sm" style="padding: 5px 10px; font-size: 0.75rem; color: var(--primary); border-color: var(--primary);">
                                                    <i class="fa-solid fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function enableEdit(id) {
        document.getElementById(`cat-text-${id}`).style.display = 'none';
        document.getElementById(`cat-edit-btn-${id}`).style.display = 'none';
        document.getElementById(`cat-form-${id}`).style.display = 'flex';
    }

    function cancelEdit(id) {
        document.getElementById(`cat-text-${id}`).style.display = 'block';
        document.getElementById(`cat-edit-btn-${id}`).style.display = 'inline-block';
        document.getElementById(`cat-form-${id}`).style.display = 'none';
    }
</script>
@endsection
