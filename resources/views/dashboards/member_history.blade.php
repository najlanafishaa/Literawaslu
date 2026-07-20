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
                            <th>Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Tanggal Kembali</th>
                            <th>Sanksi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($borrows as $borrow)
                            @php
                                $due = \Carbon\Carbon::parse($borrow->due_date);
                                $now = \Carbon\Carbon::now()->startOfDay();
                                $diff = $now->diffInDays($due, false);
                            @endphp
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: var(--dark);">{{ $borrow->book->title }}</div>
                                    <div style="font-size: 0.8rem; color: var(--gray-600); font-family: monospace;">{{ $borrow->book->barcode }}</div>
                                </td>
                                <td>{{ $borrow->borrow_date->format('d M Y') }}</td>
                                <td>{{ $borrow->due_date->format('d M Y') }}</td>
                                <td>
                                    @if($borrow->return_date)
                                        {{ $borrow->return_date->format('d M Y') }}
                                    @else
                                        <span style="color: var(--gray-600); font-style: italic;">Belum kembali</span>
                                    @endif
                                </td>
                                <td>
                                    @if($borrow->fine_amount > 0)
                                        <div style="font-weight: bold; color: var(--primary);">{{ $borrow->fine_amount }} Buku</div>
                                        @if($borrow->fine_status === 'unpaid')
                                            <span class="badge badge-danger" style="font-size: 0.75rem; padding: 3px 6px; display: inline-block; margin-top: 4px;"><i class="fa-solid fa-circle-exclamation"></i> Wajib Ganti Buku</span>
                                        @elseif($borrow->fine_status === 'paid')
                                            <span class="badge badge-success" style="font-size: 0.75rem; padding: 3px 6px; display: inline-block; margin-top: 4px;"><i class="fa-solid fa-circle-check"></i> Buku Sudah Diganti</span>
                                        @endif
                                    @else
                                        <span style="color: var(--gray-500); font-style: italic; font-size: 0.85rem;">Aman</span>
                                    @endif
                                </td>
                                <td>
                                    @if($borrow->status === 'returned')
                                        @if($borrow->return_date->greaterThan($borrow->due_date))
                                            <span class="badge badge-warning" title="Dikembalikan terlambat"><i class="fa-solid fa-circle-exclamation"></i> Kembali (Terlambat)</span>
                                        @else
                                            <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Selesai</span>
                                        @endif
                                    @elseif($borrow->status === 'borrowed')
                                        @if($diff < 0)
                                            <span class="badge badge-danger"><i class="fa-solid fa-circle-xmark"></i> Terlambat {{ abs($diff) }} Hari</span>
                                        @else
                                            <span class="badge badge-warning"><i class="fa-solid fa-clock"></i> Dipinjam</span>
                                        @endif
                                    @elseif($borrow->status === 'terlambat')
                                        <span class="badge badge-danger"><i class="fa-solid fa-circle-exclamation"></i> Terlambat {{ abs($diff) }} Hari</span>
                                    @else
                                        <span class="badge badge-danger"><i class="fa-solid fa-circle-exclamation"></i> {{ ucfirst($borrow->status) }}</span>
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
