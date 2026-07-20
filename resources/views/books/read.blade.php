@extends('layouts.app')

@section('title', 'Baca Online - ' . $book->title)
@section('header_title', 'Baca Online')

@section('content')
<div class="card" style="margin-bottom: 20px;">
    <div class="card-header">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('catalog') }}" class="btn btn-outline btn-sm" style="flex-shrink: 0;">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <div>
                <h2 style="margin: 0; font-size: 1rem; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-brands fa-google-drive" style="color: #4285F4;"></i>
                    {{ $book->title }}
                </h2>
                <span style="font-size: 0.78rem; color: var(--gray-600);">Oleh: {{ $book->author }} &bull; {{ $book->publisher }} ({{ $book->year }})</span>
            </div>
        </div>
    </div>
</div>

<div class="card" style="overflow: hidden; border-radius: var(--border-radius);">
    <div style="position: relative; width: 100%; height: 80vh; background-color: var(--gray-100);">
        <iframe 
            src="{{ $embedUrl }}" 
            style="width: 100%; height: 100%; border: none;"
            sandbox="allow-scripts allow-same-origin allow-popups"
            allow="autoplay"
            loading="lazy"
        ></iframe>
        
        {{-- Overlay to prevent right-click download attempts --}}
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 40px; background: transparent; z-index: 10;" oncontextmenu="return false;"></div>
    </div>
</div>

<div style="margin-top: 15px; padding: 12px 20px; background-color: rgba(66,133,244,0.05); border: 1px solid rgba(66,133,244,0.2); border-radius: var(--border-radius); display: flex; align-items: center; gap: 10px;">
    <i class="fa-solid fa-circle-info" style="color: #4285F4; font-size: 1.1rem;"></i>
    <span style="font-size: 0.82rem; color: var(--gray-700);">
        Buku ini ditampilkan dalam mode <strong>preview saja</strong>. Anda hanya bisa membaca secara online tanpa mengunduh file. Silakan pinjam buku fisik melalui katalog untuk koleksi pribadi.
    </span>
</div>
@endsection

@section('scripts')
<script>
    // Disable right-click on entire page to discourage download
    document.addEventListener('contextmenu', function(e) {
        if (e.target.tagName === 'IFRAME' || e.target.closest('.card')) {
            e.preventDefault();
        }
    });
</script>
@endsection
