@extends('layouts.app')

@section('title', 'Baca Online - ' . $book->title)
@section('header_title', 'Baca Online')

@section('content')
<div class="card" style="margin-bottom: 20px;">
    <div class="card-header">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; width: 100%;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <a href="{{ route('catalog') }}" class="btn btn-outline btn-sm" style="flex-shrink: 0;">
                    <i class="ti ti-arrow-left"></i> Kembali
                </a>
                <div>
                    <h2 style="margin: 0; font-size: 1rem; display: flex; align-items: center; gap: 8px;">
                        <i class="ti ti-brand-google-drive" style="color: #4285F4;"></i>
                        {{ $book->title }}
                    </h2>
                    <span style="font-size: 0.78rem; color: var(--gray-600);">Oleh: {{ $book->author }} &bull; {{ $book->publisher }} ({{ $book->year }})</span>
                </div>
            </div>
            @php
                $viewUrl = $book->drive_link;
                if (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $viewUrl, $matches)) {
                    $viewUrl = "https://drive.google.com/file/d/" . $matches[1] . "/preview";
                } elseif (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $viewUrl, $matches)) {
                    $viewUrl = "https://drive.google.com/file/d/" . $matches[1] . "/preview";
                } else {
                    $viewUrl = $embedUrl;
                }
            @endphp
            <a href="{{ $viewUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                <i class="ti ti-external-link"></i> Buka di Google Drive
            </a>
        </div>
    </div>
</div>

<div class="card" style="overflow: hidden; border-radius: var(--border-radius);">
    <div style="position: relative; width: 100%; height: 80vh; background-color: var(--gray-100); overflow: hidden;">
        <iframe 
            src="{{ $embedUrl }}" 
            style="position: absolute; top: -56px; left: 0; width: 100%; height: calc(100% + 56px); border: none;"
            sandbox="allow-scripts allow-same-origin"
            loading="lazy"
        ></iframe>
        
        {{-- Overlay to prevent popout and right-click download --}}
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 50px; background: transparent; z-index: 10;" oncontextmenu="return false;"></div>
    </div>
</div>

<div style="margin-top:15px; padding:12px 20px; background-color:var(--gray-100); border:1px solid var(--gray-200); border-radius:var(--border-radius); display:flex; align-items:center; gap:10px;">
    <i class="ti ti-eye" style="color:var(--primary); font-size:1.1rem;"></i>
    <span style="font-size:0.82rem; color:var(--gray-700);">
        Mode <strong>baca online saja</strong>. Unduh file tidak diizinkan.
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
