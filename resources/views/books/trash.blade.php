@extends('layouts.app')

@section('title', 'Tong Sampah Buku')
@section('header_title', 'Buku yang Dihapus')

@section('content')
<div class="card" style="margin-bottom: 25px;">
    <div class="card-body" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; padding:20px;">
        <a href="{{ route('books.index') }}" class="btn btn-outline">
            <i class="ti ti-arrow-left"></i> Kembali ke Koleksi
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2>Tong Sampah</h2>
            <span class="badge badge-success">{{ $books->count() }} Total Buku</span>
        </div>
        @if(auth()->user()->role === 'super_admin')
            <button type="button" class="btn btn-outline" style="color: var(--primary); border-color: rgba(var(--primary-rgb), 0.2);" onclick="submitBulkDelete()">
                <i class="ti ti-trash"></i> Hapus Terpilih
            </button>
        @endif
    </div>
    
    <div class="card-body">
        @if($books->isEmpty())
            <p style="text-align: center; color: var(--gray-600); padding: 20px;">Tong sampah kosong.</p>
        @else
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            @if(auth()->user()->role === 'super_admin')
                            <th style="width: 40px;"><input type="checkbox" id="selectAll"></th>
                            @endif
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
                                @if(auth()->user()->role === 'super_admin')
                                <td><input type="checkbox" name="book_ids[]" value="{{ $book->id }}" class="book-checkbox"></td>
                                @endif
                                <td style="font-family: monospace; font-weight: 600;">{{ $book->barcode }}</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width:35px; height:50px; border-radius:4px; overflow:hidden; background-color:var(--gray-100); border:1px solid var(--gray-200); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                            @if($book->cover_image)
                                                <img src="{{ asset($book->cover_image) }}" alt="Sampul" style="width: 100%; height: 100%; object-fit: cover;">
                                            @else
                                                <div style="width: 100%; height: 100%; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); display: flex; align-items: center; justify-content: center; color: var(--light);">
                                                    <i class="ti ti-book-2" style="font-size: 0.8rem;"></i>
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
                                    @if($book->drive_link && $book->stock > 0)
                                        <span class="badge badge-online">Online</span>
                                        <span class="badge badge-offline">Offline</span>
                                    @elseif($book->drive_link || $book->is_online)
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
                                        <form action="{{ route('books.restore', $book->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-outline btn-sm" title="Pulihkan Buku" style="padding: 6px 10px; color: var(--success); border-color: rgba(var(--success-rgb), 0.2);">
                                                <i class="ti ti-refresh"></i> Pulihkan
                                            </button>
                                        </form>
                                        <form action="{{ route('books.forceDelete', $book->id) }}" method="POST" onsubmit="return confirm('Peringatan: Tindakan ini akan menghapus buku secara permanen dan tidak dapat dikembalikan. Lanjutkan?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline btn-sm" title="Hapus Permanen" style="padding:6px 10px; color:var(--danger); border-color:rgba(var(--danger-rgb),0.2);">
                                                <i class="ti ti-trash"></i> Hapus Permanen
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

@if(auth()->user()->role === 'super_admin')


@endif
@endsection
