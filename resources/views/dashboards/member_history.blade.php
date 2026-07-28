@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')
@section('header_title', 'Riwayat Peminjaman')

@section('content')
<div class="card">
    <div class="card-header">
        <h2><i class="fa-solid fa-clock-rotate-left" style="color: var(--primary); margin-right: 8px;"></i> Daftar Transaksi Anda</h2>
        <span class="badge badge-success" style="font-size: 0.85rem; padding: 6px 12px;">Total Peminjaman: {{ $totalLoans }} Kali</span>
    </div>
    
    <div class="card-body">
        @if($borrows->isEmpty())
            <div style="text-align: center; padding: 60px 20px; color: var(--gray-600);">
                <i class="fa-solid fa-book-bookmark" style="font-size: 3rem; color: var(--gray-300); margin-bottom: 15px;"></i>
                <p style="font-weight: 600; color: var(--gray-700);">Belum Ada Riwayat Transaksi</p>
                <p style="font-size: 0.85rem; margin-top: 5px;">Buku yang pernah Anda pinjam di perpustakaan Literawaslu akan muncul di halaman ini.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Judul Buku</th>
                            <th>Jenis Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Tanggal Kembali</th>
                            <th>Status Peminjaman</th>
                            <th>Keterangan Keterlambatan / Sanksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($borrows as $borrow)
                            @php
                                $due = \Carbon\Carbon::parse($borrow->due_date);
                                $borrowDate = \Carbon\Carbon::parse($borrow->borrow_date);
                                $returnDate = $borrow->return_date ? \Carbon\Carbon::parse($borrow->return_date) : null;
                                
                                // Calculate late days
                                $lateDays = 0;
                                if ($returnDate && $returnDate->greaterThan($due)) {
                                    $lateDays = $returnDate->diffInDays($due);
                                } elseif (!$returnDate && \Carbon\Carbon::now()->startOfDay()->greaterThan($due)) {
                                    $lateDays = \Carbon\Carbon::now()->startOfDay()->diffInDays($due);
                                }
                            @endphp
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: var(--dark);">{{ $borrow->book->title }}</div>
                                    <div style="font-size: 0.8rem; color: var(--gray-600); font-family: monospace;">Barcode: {{ $borrow->book->barcode }}</div>
                                </td>
                                <td>
                                    @if($borrow->book->is_online)
                                        <span class="badge badge-online">Online</span>
                                    @else
                                        <span class="badge badge-offline">Offline</span>
                                    @endif
                                </td>
                                <td>{{ $borrowDate->format('d M Y') }}</td>
                                <td>{{ $due->format('d M Y') }}</td>
                                <td>
                                    @if($returnDate)
                                        {{ $returnDate->format('d M Y') }}
                                    @else
                                        <span style="color: var(--gray-600); font-style: italic;">Belum Kembali</span>
                                    @endif
                                </td>
                                <td>
                                    @if($borrow->status === 'returned')
                                        @if($lateDays > 0)
                                            <span class="badge badge-warning"><i class="fa-solid fa-triangle-exclamation"></i> Selesai (Terlambat)</span>
                                        @else
                                            <span class="badge badge-success"><i class="fa-solid fa-bookmark"></i> Selesai</span>
                                        @endif
                                    @elseif($borrow->status === 'borrowed')
                                        @if($lateDays > 0)
                                            <span class="badge badge-danger"><i class="fa-solid fa-ban"></i> Terlambat</span>
                                        @else
                                            <span class="badge badge-warning"><i class="fa-solid fa-hourglass-half"></i> Dipinjam</span>
                                        @endif
                                    @elseif($borrow->status === 'pending')
                                        <span class="badge badge-pending"><i class="fa-solid fa-hourglass-half"></i> Menunggu Verifikasi</span>
                                    @else
                                        <span class="badge badge-danger">{{ ucfirst($borrow->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($lateDays > 0)
                                        <div style="font-size: 0.85rem; font-weight: 600; color: var(--primary);">
                                            Terlambat {{ $lateDays }} Hari
                                        </div>
                                        <div style="font-size: 0.78rem; color: var(--gray-700); margin-top: 2px;">
                                            @if($lateDays == 1)
                                                Sanksi: Pengurangan 10 Poin
                                            @elseif($lateDays == 2)
                                                Sanksi: Pengurangan 10 Poin
                                            @elseif($lateDays == 3)
                                                Sanksi: Pengurangan 10 Poin
                                            @else
                                                Sanksi: Wajib Donasi 1 Buku Fisik
                                                @if($borrow->fine_status === 'paid')
                                                    <span class="badge badge-success"><i class="fa-solid fa-bookmark"></i> Sudah Dipenuhi</span>
                                                @else
                                                    <span class="badge badge-danger"><i class="fa-solid fa-triangle-exclamation"></i> Belum Dipenuhi</span>
                                                @endif
                                            @endif
                                        </div>
                                    @else
                                    <span style="color: var(--dark); font-weight: 500; font-size: 0.85rem;"><i class="fa-solid fa-star" style="color: var(--secondary);"></i> Tepat Waktu</span>
                                    @endif
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
