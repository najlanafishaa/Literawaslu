@extends('layouts.app')

@section('title', 'Transaksi Peminjaman')
@section('header_title', 'Peminjaman & Pengembalian')

@section('content')



<!-- Active Transactions List -->
<div class="card">
    <div class="card-header">
        <h2><i class="fa-solid fa-list-check" style="color: var(--dark); margin-right: 8px;"></i> Daftar Peminjaman Aktif (Sedang Dipinjam)</h2>
        <span class="badge badge-warning">{{ $activeBorrows->count() }} Peminjaman</span>
    </div>
    <div class="card-body">
        @if($activeBorrows->isEmpty())
            <p style="text-align: center; color: var(--gray-600); padding: 30px;">Tidak ada transaksi peminjaman aktif saat ini.</p>
        @else
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Buku</th>
                            <th>Anggota</th>
                            <th>Tgl Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Status Sisa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activeBorrows as $borrow)
                            @php
                                $due = \Carbon\Carbon::parse($borrow->due_date);
                                $now = \Carbon\Carbon::now()->startOfDay();
                                $diff = $now->diffInDays($due, false);
                            @endphp
                            <tr>
                                <td>
                                    <div style="font-weight: 600;">{{ $borrow->book->title }}</div>
                                    <div style="font-size: 0.8rem; color: var(--gray-600); font-family: monospace;">{{ $borrow->book->barcode }}</div>
                                </td>
                                <td>
                                    <div style="font-weight: 500;">{{ $borrow->member->user->name }}</div>
                                    <div style="font-size: 0.8rem; color: #b58b00; font-weight: 600;">{{ $borrow->member->member_code }}</div>
                                </td>
                                <td>{{ $borrow->borrow_date->format('d M Y') }}</td>
                                <td>{{ $borrow->due_date->format('d M Y') }}</td>
                                <td>
                                    @if($diff < 0)
                                        <span class="badge badge-danger">Terlambat {{ abs($diff) }} Hari</span>
                                    @elseif($diff == 0)
                                        <span class="badge badge-warning">Hari Ini!</span>
                                    @else
                                        <span class="badge badge-success">{{ $diff }} Hari</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('borrows.checkin') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="barcode" value="{{ $borrow->book->barcode }}">
                                        <button type="submit" class="btn btn-accent btn-sm" style="font-size: 0.75rem; padding: 6px 12px;">
                                            <i class="fa-solid fa-circle-left"></i> Kembalikan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Unpaid Fines List -->
@php
    $unpaidFines = \App\Models\Borrow::where('fine_status', 'unpaid')
        ->with(['member.user', 'book'])
        ->orderBy('created_at', 'desc')
        ->get();
@endphp

<div class="card" style="margin-top: 25px; margin-bottom: 25px;">
    <div class="card-header" style="background-color: rgba(var(--primary-rgb), 0.05);">
        <h2><i class="fa-solid fa-book-medical" style="color: var(--primary); margin-right: 8px;"></i> Daftar Sanksi Wajib Donasi Buku Fisik (> 3 Hari Terlambat)</h2>
        <span class="badge badge-danger">{{ $unpaidFines->count() }} Transaksi</span>
    </div>
    <div class="card-body">
        @if($unpaidFines->isEmpty())
            <p style="text-align: center; color: var(--gray-600); padding: 30px;">Tidak ada sanksi buku fisik yang belum dipenuhi saat ini.</p>
        @else
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Buku Dipinjam</th>
                            <th>Nama Member</th>
                            <th>Tanggal Jatuh Tempo</th>
                            <th>Tanggal Kembali</th>
                            <th>Sanksi Wajib</th>
                            <th>Aksi Konfirmasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unpaidFines as $fineBorrow)
                            <tr>
                                <td>
                                    <div style="font-weight: 600;">{{ $fineBorrow->book->title }}</div>
                                    <div style="font-size: 0.8rem; color: var(--gray-600); font-family: monospace;">{{ $fineBorrow->book->barcode }}</div>
                                </td>
                                <td>
                                    <div style="font-weight: 500;">{{ $fineBorrow->member->user->name }}</div>
                                    <div style="font-size: 0.8rem; color: #b58b00; font-weight: 600;">{{ $fineBorrow->member->member_code }}</div>
                                </td>
                                <td>{{ $fineBorrow->due_date->format('d M Y') }}</td>
                                <td>{{ $fineBorrow->return_date ? $fineBorrow->return_date->format('d M Y') : '-' }}</td>
                                <td>
                                    <strong style="color: var(--primary); font-size: 0.9rem;">1 Buku Fisik Baru</strong>
                                </td>
                                <td>
                                    <form action="{{ route('borrows.pay_fine', $fineBorrow->id) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm" style="font-size: 0.75rem; padding: 6px 12px; display: inline-flex; align-items: center; gap: 4px; height: auto;">
                                            <i class="fa-solid fa-check"></i> Konfirmasi Terima Buku
                                        </button>
                                    </form>
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
