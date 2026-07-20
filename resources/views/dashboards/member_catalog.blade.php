@extends('layouts.app')

@section('title', 'Katalog Buku')
@section('header_title', 'Katalog Buku')

@section('content')
@if(auth()->user()->member->status === 'pending')
    <div class="alert alert-warning" style="background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #ffeeba;">
        <strong>Perhatian!</strong> Akun Anda sedang menunggu verifikasi dari Admin. Anda belum bisa meminjam buku secara online sampai akun Anda disetujui.
    </div>
@endif
@if(auth()->user()->member->status === 'rejected')
    <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
        <strong>Pendaftaran Ditolak!</strong> Akun Anda tidak disetujui oleh Admin. Silakan hubungi petugas perpustakaan untuk informasi lebih lanjut.
    </div>
@endif

<div class="card" style="margin-bottom: 25px;">
    <div class="card-body">
        <form id="catalogFilter" action="{{ route('catalog') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 250px;">
                <input type="text" name="search" class="form-control" placeholder="Cari judul buku, penulis, atau barcode..." value="{{ request('search') }}">
            </div>
            
            <div style="width: 200px; min-width: 150px;">
                <select name="category" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-magnifying-glass"></i> Cari
                </button>
                @if(request()->anyFilled(['search', 'category']))
                    <a href="{{ route('catalog') }}" class="btn btn-outline">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="catalog-grid">
    @forelse($books as $book)
        <div class="book-card" style="position: relative; overflow: hidden; display: flex; flex-direction: column; min-height: 450px; padding: 0; border: 1px solid var(--gray-200); border-radius: var(--border-radius); background: var(--light);">
            <!-- Blur Cover Background -->
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 160px; overflow: hidden; z-index: 1;">
                <div style="width: 100%; height: 100%; background-image: url('{{ $book->cover_image ? asset($book->cover_image) : asset('images/logo-bawaslu.png') }}'); background-size: cover; background-position: center; filter: blur(12px) brightness(0.55); transform: scale(1.15);"></div>
            </div>
            
            <!-- Foreground Elements -->
            <div style="position: relative; z-index: 2; padding: 25px 20px 0; display: flex; flex-direction: column; align-items: center; text-align: center; margin-top: 15px;">
                <!-- Foreground Cover Image -->
                <div style="width: 110px; height: 155px; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.35); background-color: #f0f0f0; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255,255,255,0.2);">
                    @if($book->cover_image)
                        <img src="{{ asset($book->cover_image) }}" alt="Sampul {{ $book->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <!-- Elegant Book Icon Placeholder with gradient -->
                        <div style="width: 100%; height: 100%; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--light); padding: 10px;">
                            <i class="fa-solid fa-book" style="font-size: 2.2rem; margin-bottom: 5px;"></i>
                            <span style="font-size: 0.55rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">No Cover</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Card Body Content -->
            <div style="flex-grow: 1; padding: 15px 20px 20px; display: flex; flex-direction: column; justify-content: space-between; z-index: 2; background-color: var(--light);">
                <div style="text-align: center;">
                    <span class="book-category" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: var(--primary); margin-bottom: 5px; display: block;">{{ $book->category }}</span>
                    <h3 class="book-title" style="font-size: 0.95rem; font-weight: 700; color: var(--dark); line-height: 1.3; margin-bottom: 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 38px;">{{ $book->title }}</h3>
                    <p class="book-author" style="font-size: 0.8rem; color: var(--gray-600); margin-bottom: 5px;">Oleh: {{ $book->author }}</p>
                    
                    <div style="font-size: 0.75rem; color: var(--gray-600); display: flex; justify-content: center; gap: 10px; margin-top: 5px;">
                        <span><i class="fa-solid fa-print"></i> {{ $book->publisher }}</span>
                        <span>•</span>
                        <span>{{ $book->year }}</span>
                    </div>
                </div>
                
                <div style="border-top: 1px solid var(--gray-100); padding-top: 12px; margin-top: 12px;">
                    <!-- Stock Ratio Info -->
                    <div style="display: flex; justify-content: space-between; font-size: 0.78rem; margin-bottom: 8px; align-items: center;">
                        <span style="color: var(--gray-600);"><i class="fa-solid fa-layer-group"></i> Stok Tersedia:</span>
                        <span style="font-weight: 700; color: {{ $book->available_stock > 0 ? 'var(--success)' : 'var(--primary)' }}">
                            {{ $book->available_stock }} / {{ $book->stock }} Buku
                        </span>
                    </div>
                    
                    <div class="book-footer" style="border: none; padding: 0; display: flex; justify-content: space-between; align-items: center;">
                        <span class="book-status">
                            @if($book->available_stock > 0)
                                <span class="badge badge-success" style="background-color: rgba(40,167,69,0.1); color: var(--success);"><i class="fa-solid fa-circle-check"></i> Tersedia</span>
                            @else
                                <span class="badge badge-danger" style="background-color: rgba(214,32,39,0.1); color: var(--primary);"><i class="fa-solid fa-circle-xmark"></i> Kosong</span>
                            @endif
                        </span>
                        <span style="font-size: 0.75rem; color: var(--gray-600); font-family: monospace;">{{ $book->barcode }}</span>
                    </div>
                    
                    <div style="margin-top: 15px; display: flex; flex-direction: column; gap: 8px;">
                        @if($book->available_stock > 0 && auth()->user()->member->status === 'active')
                            <form action="{{ route('member.request_borrow') }}" method="POST" style="margin: 0;">
                                @csrf
                                <input type="hidden" name="book_id" value="{{ $book->id }}">
                                <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; display: flex; justify-content: center; align-items: center; gap: 8px;">
                                    <i class="fa-solid fa-book-open"></i> Pinjam Buku Ini
                                </button>
                            </form>
                        @endif
                        <button onclick="showBookDetail({{ $book->id }})" class="btn btn-outline btn-sm" style="width: 100%; margin: 0; display: flex; justify-content: center; align-items: center; gap: 8px; color: var(--dark); border-color: var(--gray-300);">
                            <i class="fa-solid fa-circle-info"></i> Lihat Detail
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background-color: var(--light); border-radius: var(--border-radius); border: 1px solid var(--gray-200);">
            @if(request('category') && !request('search'))
                <i class="fa-solid fa-book-open" style="font-size: 3rem; color: var(--gray-300); margin-bottom: 15px;"></i>
                <p style="font-weight: 600; color: var(--gray-700);">Belum ada buku dalam kategori "{{ request('category') }}"</p>
                <p style="font-size: 0.85rem; color: var(--gray-600); margin-top: 5px;">Kategori ini belum memiliki buku. Silakan cek kategori lainnya.</p>
            @elseif(request('search'))
                <i class="fa-solid fa-magnifying-glass" style="font-size: 3rem; color: var(--gray-300); margin-bottom: 15px;"></i>
                <p style="font-weight: 600; color: var(--gray-700);">Buku tidak ditemukan</p>
                <p style="font-size: 0.85rem; color: var(--gray-600); margin-top: 5px;">Tidak ada hasil untuk "{{ request('search') }}"{{ request('category') ? ' di kategori "'.request('category').'"' : '' }}. Coba kata kunci lain.</p>
            @else
                <i class="fa-solid fa-folder-open" style="font-size: 3rem; color: var(--gray-300); margin-bottom: 15px;"></i>
                <p style="font-weight: 600; color: var(--gray-700);">Belum ada buku tersedia</p>
                <p style="font-size: 0.85rem; color: var(--gray-600); margin-top: 5px;">Perpustakaan belum memiliki buku. Silakan cek kembali nanti.</p>
            @endif
        </div>
    @endforelse
</div>

{{-- Book Detail Modal --}}
<div id="bookDetailModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:9999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:var(--light); border-radius:16px; max-width:560px; width:100%; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,0.3);">
        <div style="display:flex; justify-content:space-between; align-items:center; padding:20px 25px; border-bottom:1px solid var(--gray-100);">
            <h3 id="modalBookTitle" style="font-size:1.1rem; font-weight:700; color:var(--dark); margin:0; line-height:1.4;"></h3>
            <button onclick="closeBookDetail()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--gray-600); line-height:1;">&times;</button>
        </div>
        <div style="display:flex; gap:20px; padding:20px 25px;">
            <div id="modalCoverWrap" style="flex-shrink:0; width:110px; height:155px; border-radius:10px; overflow:hidden; background:var(--gray-100); display:flex; align-items:center; justify-content:center;">
                <img id="modalCover" src="" alt="" style="width:100%; height:100%; object-fit:cover; display:none;">
                <div id="modalCoverIcon" style="width:100%; height:100%; background:linear-gradient(135deg,var(--primary),var(--secondary)); display:flex; flex-direction:column; align-items:center; justify-content:center; color:white;">
                    <i class="fa-solid fa-book" style="font-size:2rem;"></i>
                </div>
            </div>
            <div style="flex:1; display:flex; flex-direction:column; gap:6px;">
                <span id="modalCategory" style="font-size:0.72rem; font-weight:700; text-transform:uppercase; color:var(--primary);"></span>
                <p id="modalAuthor" style="font-size:0.88rem; color:var(--gray-700); margin:0;"></p>
                <p id="modalPublisher" style="font-size:0.82rem; color:var(--gray-600); margin:0;"></p>
                <p id="modalYear" style="font-size:0.82rem; color:var(--gray-600); margin:0;"></p>
                <p id="modalBarcode" style="font-size:0.78rem; color:var(--gray-500); font-family:monospace; margin:0;"></p>
                <div id="modalStock" style="font-size:0.82rem; font-weight:600; margin-top:4px;"></div>
            </div>
        </div>
        <div id="modalDescWrap" style="padding:0 25px 25px;">
            <div style="border-top:1px solid var(--gray-100); padding-top:15px;">
                <p style="font-size:0.8rem; font-weight:700; text-transform:uppercase; color:var(--gray-500); margin-bottom:8px;">Deskripsi</p>
                <p id="modalDesc" style="font-size:0.88rem; color:var(--gray-700); line-height:1.7; margin:0;"></p>
            </div>
        </div>
        <div id="modalBorrowWrap" style="padding:0 25px 25px; display:none;">
            <form id="modalBorrowForm" action="{{ route('member.request_borrow') }}" method="POST">
                @csrf
                <input type="hidden" id="modalBookId" name="book_id" value="">
                <button type="submit" class="btn btn-primary" style="width:100%; gap:8px; display:flex; align-items:center; justify-content:center;">
                    <i class="fa-solid fa-book-open"></i> Pinjam Buku Ini
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@php
    $memberStatus = auth()->user()->member->status;
    $bookDataArray = [];
    foreach($books as $b) {
        $bookDataArray[$b->id] = [
            'id'          => $b->id,
            'title'       => $b->title,
            'author'      => $b->author,
            'publisher'   => $b->publisher,
            'year'        => $b->year,
            'category'    => $b->category,
            'barcode'     => $b->barcode,
            'description' => $b->description,
            'cover'       => $b->cover_image ? asset($b->cover_image) : null,
            'stock'       => $b->available_stock,
            'totalStock'  => $b->stock,
            'canBorrow'   => $b->available_stock > 0 && $memberStatus === 'active',
        ];
    }
@endphp
<script>
const bookData = @json($bookDataArray);

function showBookDetail(id) {
    const b = bookData[id];
    if (!b) return;

    document.getElementById('modalBookTitle').textContent   = b.title;
    document.getElementById('modalCategory').textContent    = b.category;
    document.getElementById('modalAuthor').textContent      = 'Penulis: ' + b.author;
    document.getElementById('modalPublisher').textContent   = 'Penerbit: ' + b.publisher;
    document.getElementById('modalYear').textContent        = 'Tahun Terbit: ' + b.year;
    document.getElementById('modalBarcode').textContent     = 'Barcode: ' + b.barcode;

    const stockEl = document.getElementById('modalStock');
    if (b.stock > 0) {
        stockEl.innerHTML = `<span style="color:var(--success)"><i class="fa-solid fa-circle-check"></i> Tersedia: ${b.stock} / ${b.totalStock} Buku</span>`;
    } else {
        stockEl.innerHTML = `<span style="color:var(--primary)"><i class="fa-solid fa-circle-xmark"></i> Stok Habis</span>`;
    }

    const descEl = document.getElementById('modalDesc');
    const descWrap = document.getElementById('modalDescWrap');
    if (b.description && b.description.trim() !== '') {
        descEl.textContent = b.description;
        descWrap.style.display = 'block';
    } else {
        descEl.textContent = 'Belum ada deskripsi untuk buku ini.';
        descWrap.style.display = 'block';
    }

    const coverImg  = document.getElementById('modalCover');
    const coverIcon = document.getElementById('modalCoverIcon');
    if (b.cover) {
        coverImg.src          = b.cover;
        coverImg.style.display = 'block';
        coverIcon.style.display = 'none';
    } else {
        coverImg.style.display  = 'none';
        coverIcon.style.display = 'flex';
    }

    const borrowWrap = document.getElementById('modalBorrowWrap');
    if (b.canBorrow) {
        document.getElementById('modalBookId').value = b.id;
        borrowWrap.style.display = 'block';
    } else {
        borrowWrap.style.display = 'none';
    }

    const modal = document.getElementById('bookDetailModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeBookDetail() {
    document.getElementById('bookDetailModal').style.display = 'none';
    document.body.style.overflow = '';
}

document.getElementById('bookDetailModal').addEventListener('click', function(e) {
    if (e.target === this) closeBookDetail();
});
</script>
@endsection

