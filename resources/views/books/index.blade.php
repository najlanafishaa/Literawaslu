@extends('layouts.app')

@section('title', 'Kelola Buku')
@section('header_title', 'Kelola Koleksi Buku')

@section('content')
<div class="card" style="margin-bottom: 25px;">
    <div class="card-body" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; padding:20px;">
        <form action="{{ route('books.index') }}" method="GET" style="display: flex; gap: 10px; flex: 1; max-width: 500px;">
            <input type="text" name="search" class="form-control" placeholder="Cari judul, penulis, atau barcode..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
            @if(request('search'))
                <a href="{{ route('books.index') }}" class="btn btn-outline"><i class="fa-solid fa-rotate-left"></i> Atur Ulang</a>
            @endif
        </form>
        
        <a href="{{ route('books.create') }}" class="btn btn-secondary">
            <i class="fa-solid fa-plus"></i> Tambah Buku Baru
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Daftar Koleksi Buku</h2>
        <span class="badge badge-success">{{ $books->count() }} Total Buku</span>
    </div>
    
    <div class="card-body">
        @if($books->isEmpty())
            <p style="text-align: center; color: var(--gray-600); padding: 20px;">Tidak ada koleksi buku yang ditemukan.</p>
        @else
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Barcode</th>
                            <th>Buku</th>
                            <th>Penulis</th>
                            <th>Jenis Buku</th>
                            <th>Stok</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($books as $book)
                            <tr>
                                <td style="font-family: monospace; font-weight: 600;">{{ $book->barcode }}</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width:35px; height:50px; border-radius:4px; overflow:hidden; background-color:var(--gray-100); border:1px solid var(--gray-200); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                            @if($book->cover_image)
                                                <img src="{{ asset($book->cover_image) }}" alt="Sampul" style="width: 100%; height: 100%; object-fit: cover;">
                                            @else
                                                <div style="width: 100%; height: 100%; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); display: flex; align-items: center; justify-content: center; color: var(--light);">
                                                    <i class="fa-solid fa-book" style="font-size: 0.8rem;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: var(--dark);">{{ $book->title }}</div>
                                            <small style="color: var(--gray-600);">{{ $book->publisher }} ({{ $book->year }})</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $book->author }}</td>
                                <td>
                                    @if($book->drive_link)
                                        <span class="badge badge-online">Online</span>
                                    @else
                                        <span class="badge badge-offline">Offline</span>
                                    @endif
                                </td>
                                <td>
                                    <span style="font-weight: 600; color: var(--dark);">{{ $book->available_stock }}</span>
                                    <span style="color: var(--gray-500);">/ {{ $book->stock }}</span>
                                </td>
                                <td>{{ $book->category }}</td>
                                <td>
                                    @if($book->available_stock > 0)
                                        <span class="badge badge-success">Tersedia</span>
                                    @else
                                        <span class="badge badge-danger">Habis</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="{{ route('books.edit', $book->id) }}" class="btn btn-outline btn-sm" title="Edit Buku" style="padding: 6px 10px;">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('books.destroy', $book->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini dari sistem?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline btn-sm" title="Hapus Buku" style="padding:6px 10px; color:var(--primary); border-color:rgba(var(--primary-rgb),0.2);">
                                                <i class="fa-solid fa-trash"></i>
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
@endsection
