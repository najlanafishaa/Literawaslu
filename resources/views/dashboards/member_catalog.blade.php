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
                    
                    <!-- Overall Rating -->
                    <div style="margin-top: 5px; display: flex; justify-content: center; align-items: center; gap: 5px; font-size: 0.82rem; color: #f1c40f;">
                        @if($book->reviews->count() > 0)
                            <div style="display: flex; gap: 2px;">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= round($book->average_rating) ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                                @endfor
                            </div>
                            <span style="color: var(--gray-700); font-weight: bold; margin-left: 3px;">{{ $book->average_rating }}</span>
                            <span style="color: var(--gray-500); font-size: 0.72rem;">({{ $book->reviews->count() }} Ulasan)</span>
                        @else
                            <span style="color: var(--gray-400); font-size: 0.72rem; font-style: italic;">Belum ada ulasan</span>
                        @endif
                    </div>

                    <div style="font-size: 0.75rem; color: var(--gray-600); display: flex; justify-content: center; gap: 10px; margin-top: 6px;">
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
                        @if($book->drive_link)
                            <a href="{{ route('book.read', $book->id) }}" class="btn btn-sm" style="width: 100%; display: flex; justify-content: center; align-items: center; gap: 8px; background-color: #4285F4; color: white; border: none; text-decoration: none;">
                                <i class="fa-brands fa-google-drive"></i> Baca Online
                            </a>
                        @endif
                        <button onclick="showBookDetail({{ $book->id }})" class="btn btn-outline btn-sm" style="width: 100%; margin: 0; display: flex; justify-content: center; align-items: center; gap: 8px; color: var(--dark); border-color: var(--gray-300);">
                            <i class="fa-solid fa-circle-info"></i> Lihat Detail & Ulasan
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
    <div style="background:var(--light); border-radius:16px; max-width:600px; width:100%; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,0.3);">
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
                <div id="modalRatingHeader" style="margin-top: 5px; font-size:0.85rem;"></div>
            </div>
        </div>
        
        <div id="modalDescWrap" style="padding:0 25px 15px;">
            <div style="border-top:1px solid var(--gray-100); padding-top:15px;">
                <p style="font-size:0.8rem; font-weight:700; text-transform:uppercase; color:var(--gray-500); margin-bottom:8px;">Deskripsi</p>
                <p id="modalDesc" style="font-size:0.88rem; color:var(--gray-700); line-height:1.7; margin:0;"></p>
            </div>
        </div>

        {{-- Review Section --}}
        <div style="padding:0 25px 25px;">
            <div style="border-top:1px solid var(--gray-100); padding-top:15px;">
                <p style="font-size:0.8rem; font-weight:700; text-transform:uppercase; color:var(--gray-500); margin-bottom:12px;">Ulasan Anggota</p>
                
                {{-- Review List --}}
                <div id="modalReviewList" style="display:flex; flex-direction:column; gap:12px; max-height:200px; overflow-y:auto; margin-bottom:15px; padding-right:5px;"></div>
                
                {{-- Add Review Form --}}
                <div id="modalReviewFormWrap" style="display:none; background:var(--gray-50); border:1px solid var(--gray-200); border-radius:10px; padding:15px; margin-top:10px;">
                    <form id="modalReviewForm" action="" method="POST">
                        @csrf
                        <p style="font-size:0.85rem; font-weight:700; color:var(--dark); margin-bottom:10px; display:flex; align-items:center; gap:6px;">
                            <i class="fa-solid fa-pen-to-square" style="color:var(--primary);"></i> <span id="modalReviewFormTitle">Tulis Ulasan Anda</span>
                        </p>
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
                            <label style="font-size:0.8rem; color:var(--gray-700); font-weight:600;">Rating:</label>
                            <select name="rating" required style="padding:4px 8px; border-radius:4px; border:1px solid var(--gray-300); font-size:0.85rem; color:#f1c40f; font-weight:bold;">
                                <option value="5">★★★★★ (5)</option>
                                <option value="4">★★★★☆ (4)</option>
                                <option value="3">★★★☆☆ (3)</option>
                                <option value="2">★★☆☆☆ (2)</option>
                                <option value="1">★☆☆☆☆ (1)</option>
                            </select>
                        </div>
                        <div style="margin-bottom:10px;">
                            <textarea name="review" placeholder="Tulis komentar atau ulasan Anda mengenai buku ini..." style="width:100%; height:60px; border-radius:6px; border:1px solid var(--gray-300); padding:8px; font-size:0.82rem; resize:none;" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm" style="width:100%;">Kirim Ulasan</button>
                    </form>
                </div>
                
                <div id="modalReviewNotEligible" style="display:none; font-size:0.75rem; color:var(--gray-500); background:var(--gray-50); border-radius:8px; padding:10px; border:1px solid var(--gray-100); text-align:center;">
                    <i class="fa-solid fa-lock" style="margin-right:4px;"></i> Anda harus pernah mengembalikan buku ini atau meminjamnya selama minimal 7 hari untuk dapat memberikan ulasan.
                </div>
            </div>
        </div>

        <div id="modalBorrowWrap" style="padding:0 25px 25px; display:none;">
            <div style="display:flex; flex-direction:column; gap:8px;">
                <form id="modalBorrowForm" action="{{ route('member.request_borrow') }}" method="POST" style="margin:0;">
                    @csrf
                    <input type="hidden" id="modalBookId" name="book_id" value="">
                    <button type="submit" class="btn btn-primary" style="width:100%; gap:8px; display:flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-book-open"></i> Pinjam Buku Ini
                    </button>
                </form>
                <a id="modalReadOnlineBtn" href="#" style="display:none; width:100%; gap:8px; align-items:center; justify-content:center; background-color:#4285F4; color:white; border:none; text-decoration:none; padding:8px 16px; border-radius:var(--border-radius); font-size:0.85rem; font-weight:600; text-align:center;">
                    <i class="fa-brands fa-google-drive"></i> Baca Online
                </a>
            </div>
        </div>
        <div id="modalReadOnlyWrap" style="padding:0 25px 25px; display:none;">
            <a id="modalReadOnlyBtn" href="#" style="width:100%; gap:8px; display:flex; align-items:center; justify-content:center; background-color:#4285F4; color:white; border:none; text-decoration:none; padding:10px 16px; border-radius:var(--border-radius); font-size:0.85rem; font-weight:600;">
                <i class="fa-brands fa-google-drive"></i> Baca Online
            </a>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@php
    $memberStatus = auth()->user()->member->status;
    $bookDataArray = [];
    foreach($books as $b) {
        $reviews = [];
        foreach($b->reviews as $r) {
            $reviews[] = [
                'id'          => $r->id,
                'rating'      => $r->rating,
                'review'      => $r->review,
                'member_name' => $r->member->user->name,
                'date'        => $r->created_at->format('d M Y'),
            ];
        }

        $bookDataArray[$b->id] = [
            'id'             => $b->id,
            'title'          => $b->title,
            'author'         => $b->author,
            'publisher'      => $b->publisher,
            'year'           => $b->year,
            'category'       => $b->category,
            'barcode'        => $b->barcode,
            'description'    => $b->description,
            'cover'          => $b->cover_image ? asset($b->cover_image) : null,
            'stock'          => $b->available_stock,
            'totalStock'     => $b->stock,
            'canBorrow'      => $b->available_stock > 0 && $memberStatus === 'active',
            'averageRating'  => $b->average_rating,
            'reviewsCount'   => $b->reviews->count(),
            'reviews'        => $reviews,
            'eligibleReview' => in_array($b->id, $returnedBookIds),
            'hasReviewed'    => in_array($b->id, $reviewedBookIds),
            'driveLink'      => $b->drive_link,
            'readUrl'        => $b->drive_link ? route('book.read', $b->id) : null,
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

    // Average rating display
    const ratingHeader = document.getElementById('modalRatingHeader');
    if (b.reviewsCount > 0) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += `<i class="${i <= Math.round(b.averageRating) ? 'fa-solid' : 'fa-regular'} fa-star" style="color:#f1c40f;"></i> `;
        }
        ratingHeader.innerHTML = `${stars} <strong style="color:var(--dark); margin-left:4px;">${b.averageRating}</strong> <span style="color:var(--gray-500); font-size:0.75rem;">(${b.reviewsCount} Ulasan)</span>`;
    } else {
        ratingHeader.innerHTML = `<span style="color:var(--gray-500); font-style:italic; font-size:0.8rem;">Belum ada rating</span>`;
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
    const readOnlineBtn = document.getElementById('modalReadOnlineBtn');
    const readOnlyWrap = document.getElementById('modalReadOnlyWrap');
    const readOnlyBtn = document.getElementById('modalReadOnlyBtn');

    if (b.canBorrow) {
        document.getElementById('modalBookId').value = b.id;
        borrowWrap.style.display = 'block';
        if (b.driveLink) {
            readOnlineBtn.href = b.readUrl;
            readOnlineBtn.style.display = 'flex';
        } else {
            readOnlineBtn.style.display = 'none';
        }
        readOnlyWrap.style.display = 'none';
    } else {
        borrowWrap.style.display = 'none';
        if (b.driveLink) {
            readOnlyBtn.href = b.readUrl;
            readOnlyWrap.style.display = 'block';
        } else {
            readOnlyWrap.style.display = 'none';
        }
    }

    // Render Reviews
    const reviewList = document.getElementById('modalReviewList');
    reviewList.innerHTML = '';
    if (b.reviews.length > 0) {
        b.reviews.forEach(r => {
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                stars += `<i class="${i <= r.rating ? 'fa-solid' : 'fa-regular'} fa-star" style="color:#f1c40f; font-size:0.75rem;"></i>`;
            }
            const item = document.createElement('div');
            item.style.borderBottom = '1px solid var(--gray-100)';
            item.style.paddingBottom = '8px';
            item.innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                    <strong style="font-size:0.82rem; color:var(--dark);">${r.member_name}</strong>
                    <span style="font-size:0.7rem; color:var(--gray-500);">${r.date}</span>
                </div>
                <div style="margin-bottom:4px;">${stars}</div>
                <p style="font-size:0.8rem; color:var(--gray-700); margin:0; line-height:1.4;">${r.review || '<em style="color:var(--gray-400)">Tidak ada komentar</em>'}</p>
            `;
            reviewList.appendChild(item);
        });
    } else {
        reviewList.innerHTML = `<div style="text-align:center; padding:15px; color:var(--gray-500); font-style:italic; font-size:0.82rem;">Belum ada ulasan untuk buku ini.</div>`;
    }

    // Handle Review Form Eligibility
    const formWrap = document.getElementById('modalReviewFormWrap');
    const notEligible = document.getElementById('modalReviewNotEligible');
    const reviewForm = document.getElementById('modalReviewForm');
    const formTitle = document.getElementById('modalReviewFormTitle');

    if (b.eligibleReview) {
        notEligible.style.display = 'none';
        formWrap.style.display = 'block';
        reviewForm.action = `/catalog/${b.id}/review`;
        
        if (b.hasReviewed) {
            formTitle.textContent = 'Perbarui Ulasan Anda';
        } else {
            formTitle.textContent = 'Tulis Ulasan Anda';
        }
    } else {
        formWrap.style.display = 'none';
        notEligible.style.display = 'block';
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

