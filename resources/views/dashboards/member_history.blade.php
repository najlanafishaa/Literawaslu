@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')
@section('header_title', 'Riwayat Peminjaman')

@section('content')
<style>
    .riwayat-wrap {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .riwayat-head {
        padding: 20px 24px;
        border-bottom: 1px solid #F3F4F6;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .riwayat-head-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .riwayat-head-left .icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: #FEF2F2;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #D62027;
        font-size: 15px;
        flex-shrink: 0;
    }

    .riwayat-head-title {
        font-size: 15px;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .riwayat-head-sub {
        font-size: 12px;
        color: #9CA3AF;
        margin: 2px 0 0;
    }

    .riwayat-count-pill {
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        color: #374151;
        font-size: 12px;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 99px;
        white-space: nowrap;
    }

    /* Table */
    .riwayat-table {
        width: 100%;
        border-collapse: collapse;
    }

    .riwayat-table thead th {
        background: #FAFAFA;
        color: #9CA3AF;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 12px 20px;
        border-bottom: 1px solid #F3F4F6;
        white-space: nowrap;
    }

    .riwayat-table tbody tr {
        border-bottom: 1px solid #F3F4F6;
        transition: background 180ms ease;
        cursor: default;
    }

    .riwayat-table tbody tr:last-child {
        border-bottom: none;
    }

    .riwayat-table tbody tr:hover {
        background: #FAFAFA;
    }

    .riwayat-table tbody td {
        padding: 16px 20px;
        vertical-align: middle;
    }

    /* Book cell */
    .book-flex {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .book-thumb {
        width: 48px;
        height: 68px;
        border-radius: 6px;
        object-fit: cover;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(0,0,0,0.12);
        background: #F3F4F6;
    }

    .book-thumb-placeholder {
        width: 48px;
        height: 68px;
        border-radius: 6px;
        flex-shrink: 0;
        background: linear-gradient(135deg, #D62027 0%, #F5B025 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
    }

    .book-texts {
        display: flex;
        flex-direction: column;
        gap: 3px;
        min-width: 0;
    }

    .book-name {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
        line-height: 1.35;
        white-space: normal;
    }

    .book-author {
        font-size: 12px;
        color: #9CA3AF;
    }

    .book-tags {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 4px;
        flex-wrap: wrap;
    }

    /* Badges */
    .chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
        line-height: 1;
    }

    .chip-online  { background: #DCFCE7; border: 1px solid #BBF7D0; color: #15803D; }
    .chip-offline { background: #FEE2E2; border: 1px solid #FECACA; color: #DC2626; }
    .chip-cat     { background: #F9FAFB; border: 1px solid #E5E7EB; color: #4B5563; }

    .chip-ok      { background: #DCFCE7; border: 1px solid #BBF7D0; color: #15803D; }
    .chip-pending { background: #FEF3C7; border: 1px solid #FDE68A; color: #92400E; }
    .chip-reject  { background: #FEE2E2; border: 1px solid #FECACA; color: #DC2626; }
    .chip-done    { background: #F3F4F6; border: 1px solid #D1D5DB; color: #6B7280; }
    .chip-late    { background: #FEE2E2; border: 1px solid #FCA5A5; color: #B91C1C; }

    /* Date */
    .date-val {
        font-size: 13px;
        color: #374151;
        white-space: nowrap;
    }

    .date-empty {
        font-size: 13px;
        color: #D1D5DB;
        font-style: italic;
    }

    /* Late info */
    .sanction-label {
        font-size: 12px;
        font-weight: 600;
        color: #B91C1C;
    }

    .sanction-sub {
        font-size: 11px;
        color: #6B7280;
        margin-top: 3px;
    }

    .on-time-label {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 13px;
        color: #6B7280;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 70px 20px;
        color: #9CA3AF;
    }

    .empty-state i {
        font-size: 2.5rem;
        color: #E5E7EB;
        margin-bottom: 12px;
        display: block;
    }

    .empty-state p:first-of-type {
        font-weight: 600;
        color: #374151;
        font-size: 15px;
        margin: 0 0 6px;
    }

    .empty-state p:last-of-type {
        font-size: 13px;
        margin: 0;
    }
</style>

<div class="riwayat-wrap">

    {{-- Header --}}
    <div class="riwayat-head">
        <div class="riwayat-head-left">
            <div class="icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <div>
                <p class="riwayat-head-title">Riwayat Peminjaman</p>
                <p class="riwayat-head-sub">Semua transaksi peminjaman buku Anda</p>
            </div>
        </div>
        <span class="riwayat-count-pill">{{ $totalLoans }} Transaksi</span>
    </div>

    {{-- Content --}}
    @if($borrows->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-book-open"></i>
            <p>Belum Ada Riwayat Transaksi</p>
            <p>Buku yang pernah Anda pinjam di Literawaslu akan tampil di sini.</p>
        </div>
    @else
        <div style="overflow-x: auto;">
            <table class="riwayat-table">
                <thead>
                    <tr>
                        <th style="min-width: 280px;">Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($borrows as $borrow)
                        @php
                            $due        = \Carbon\Carbon::parse($borrow->due_date);
                            $borrowDate = \Carbon\Carbon::parse($borrow->borrow_date);
                            $returnDate = $borrow->return_date ? \Carbon\Carbon::parse($borrow->return_date) : null;

                            $lateDays = 0;
                            if ($returnDate && $returnDate->greaterThan($due)) {
                                $lateDays = $returnDate->diffInDays($due);
                            } elseif (!$returnDate && \Carbon\Carbon::now()->startOfDay()->greaterThan($due)) {
                                $lateDays = \Carbon\Carbon::now()->startOfDay()->diffInDays($due);
                            }
                        @endphp
                        <tr>
                            {{-- Buku --}}
                            <td>
                                <div class="book-flex">
                                    @if($borrow->book->cover_image)
                                        <img src="{{ asset($borrow->book->cover_image) }}" alt="Cover" class="book-thumb">
                                    @else
                                        <div class="book-thumb-placeholder">
                                            <i class="fa-solid fa-book"></i>
                                        </div>
                                    @endif
                                    <div class="book-texts">
                                        <span class="book-name">{{ $borrow->book->title }}</span>
                                        <span class="book-author">{{ $borrow->book->author ?: '—' }}</span>
                                        <div class="book-tags">
                                            @if($borrow->book->is_online)
                                                <span class="chip chip-online"><i class="fa-solid fa-wifi" style="font-size:9px;"></i> Online</span>
                                            @else
                                                <span class="chip chip-offline"><i class="fa-solid fa-building-columns" style="font-size:9px;"></i> Offline</span>
                                            @endif
                                            @if($borrow->book->category)
                                                <span class="chip chip-cat">{{ $borrow->book->category }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Tanggal Pinjam --}}
                            <td><span class="date-val">{{ $borrowDate->format('d M Y') }}</span></td>

                            {{-- Jatuh Tempo --}}
                            <td><span class="date-val">{{ $due->format('d M Y') }}</span></td>

                            {{-- Tanggal Kembali --}}
                            <td>
                                @if($returnDate)
                                    <span class="date-val">{{ $returnDate->format('d M Y') }}</span>
                                @else
                                    <span class="date-empty">Belum kembali</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($borrow->status === 'returned')
                                    <span class="chip chip-done"><i class="fa-solid fa-check"></i> Selesai</span>
                                @elseif($borrow->status === 'borrowed')
                                    @if($lateDays > 0)
                                        <span class="chip chip-late"><i class="fa-solid fa-triangle-exclamation"></i> Terlambat</span>
                                    @else
                                        <span class="chip chip-ok"><i class="fa-solid fa-rotate"></i> Dipinjam</span>
                                    @endif
                                @elseif($borrow->status === 'pending')
                                    <span class="chip chip-pending"><i class="fa-regular fa-clock"></i> Menunggu</span>
                                @elseif($borrow->status === 'rejected')
                                    <span class="chip chip-reject"><i class="fa-solid fa-xmark"></i> Ditolak</span>
                                @else
                                    <span class="chip chip-done">{{ ucfirst($borrow->status) }}</span>
                                @endif
                            </td>

                            {{-- Keterangan --}}
                            <td>
                                @if(in_array($borrow->status, ['rejected', 'pending']))
                                    <span class="date-empty">—</span>
                                @elseif($lateDays > 0)
                                    <div class="sanction-label">{{ $lateDays }} hari terlambat</div>
                                    <div class="sanction-sub">
                                        @if($lateDays <= 3)
                                            Sanksi: −10 Poin
                                        @else
                                            Donasi 1 Buku Fisik
                                            @if($borrow->fine_status === 'paid')
                                                <span class="chip chip-ok" style="margin-top:4px; display:inline-flex;">Sudah dipenuhi</span>
                                            @else
                                                <span class="chip chip-late" style="margin-top:4px; display:inline-flex;">Belum dipenuhi</span>
                                            @endif
                                        @endif
                                    </div>
                                @else
                                    <div class="on-time-label">
                                        <i class="fa-solid fa-circle-check" style="color:#15803D; font-size:13px;"></i>
                                        Tepat waktu
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
