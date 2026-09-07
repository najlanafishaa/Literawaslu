@extends('layouts.app')

@section('title', 'Kelola Buku')
@section('header_title', 'Kelola Koleksi Buku')

@section('content')
<div class="card" style="margin-bottom: 25px;">
    <div class="card-body" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; padding:20px;">
        <form action="{{ route('books.index') }}" method="GET" style="display: flex; gap: 10px; flex: 1; max-width: 500px;">
            <input type="text" name="search" class="form-control" placeholder="Cari judul, penulis, atau barcode..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i> Cari</button>
            @if(request('search'))
                <a href="{{ route('books.index') }}" class="btn btn-outline"><i class="ti ti-rotate"></i> Atur Ulang</a>
            @endif
        </form>
        
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('books.trash') }}" class="btn btn-outline" style="color: var(--danger); border-color: rgba(var(--danger-rgb), 0.2);">
                <i class="ti ti-trash"></i> Tong Sampah
            </a>
            <a href="{{ route('books.create') }}" class="btn btn-secondary">
                <i class="ti ti-plus"></i> Tambah Buku Baru
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2>Daftar Koleksi Buku</h2>
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
            <p style="text-align: center; color: var(--gray-600); padding: 20px;">Tidak ada koleksi buku yang ditemukan.</p>
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
                                        <a href="{{ route('books.edit', $book->id) }}" class="btn btn-outline btn-sm" title="Edit Buku" style="padding: 6px 10px;">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                        <form action="{{ route('books.destroy', $book->id) }}" method="POST" onsubmit="return confirm('{{ in_array(auth()->user()->role, ['admin', 'petugas']) ? 'Ajukan penghapusan buku ini ke Super Admin?' : 'Apakah Anda yakin ingin menghapus buku ini dari sistem?' }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline btn-sm" title="{{ in_array(auth()->user()->role, ['admin', 'petugas']) ? 'Ajukan Hapus Buku' : 'Hapus Buku' }}" style="padding:6px 10px; color:var(--primary); border-color:rgba(var(--primary-rgb),0.2);">
                                                <i class="ti ti-trash"></i> {{ in_array(auth()->user()->role, ['admin', 'petugas']) ? 'Ajukan Hapus' : '' }}
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

// Hidden form for bulk delete (kept for CSRF token reference)
<form id="bulkDeleteForm" style="display:none;">
    @csrf
</form>

<script>
    document.getElementById('selectAll')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.book-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    async function submitBulkDelete() {
        const selected = document.querySelectorAll('.book-checkbox:checked');
        if (selected.length === 0) {
            alert('Pilih setidaknya satu buku untuk dihapus.');
            return;
        }

        if (!confirm('Yakin ingin menghapus ' + selected.length + ' buku yang dipilih?')) {
            return;
        }
        
        const btn = document.querySelector('button[onclick="submitBulkDelete()"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="ti ti-loader" style="animation: spin 1s linear infinite;"></i> Menghapus...';
        btn.disabled = true;

        const bookIds = Array.from(selected).map(cb => cb.value);
        const csrfToken = document.querySelector('#bulkDeleteForm input[name="_token"]').value;
        
        // Force HTTPS URL
        let url = '{{ route('books.bulkDelete') }}';
        if (url.startsWith('http://') && window.location.protocol === 'https:') {
            url = url.replace('http://', 'https://');
        }

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    book_ids: bookIds
                })
            });

            if (response.ok || response.redirected) {
                window.location.reload();
            } else {
                const errData = await response.json();
                let errMsg = 'Gagal menghapus buku. Silakan coba lagi.';
                if (errData.errors) {
                    errMsg = Object.values(errData.errors).flat().join('\n');
                } else if (errData.message) {
                    errMsg = errData.message;
                }
                alert('Gagal:\n' + errMsg);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan jaringan.');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }
</script>
@endif
@endsection
