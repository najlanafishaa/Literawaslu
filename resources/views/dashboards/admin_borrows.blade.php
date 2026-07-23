@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('header_title', 'Riwayat Transaksi')

@section('content')
<!-- Date Filter Panel -->
<div class="card" style="margin-bottom: 25px;">
    <div class="card-body" style="padding: 15px 20px;">
        <form action="{{ route('borrows.history') }}" method="GET" style="display: flex; justify-content: space-between; align-items: center; gap: 15px; flex-wrap: wrap; margin: 0;">
            <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                <span style="font-size: 0.85rem; font-weight: 700; color: var(--gray-700); margin-right: 5px;"><i class="fa-solid fa-calendar-days"></i> Filter Waktu:</span>
                <a href="{{ route('borrows.history', ['filter' => 'all']) }}" class="btn {{ request('filter', 'all') === 'all' ? 'btn-primary' : 'btn-outline' }} btn-sm" style="padding: 6px 12px; font-size: 0.8rem;">Semua</a>
                <a href="{{ route('borrows.history', ['filter' => 'today']) }}" class="btn {{ request('filter') === 'today' ? 'btn-primary' : 'btn-outline' }} btn-sm" style="padding: 6px 12px; font-size: 0.8rem;">Hari Ini</a>
                <a href="{{ route('borrows.history', ['filter' => 'week']) }}" class="btn {{ request('filter') === 'week' ? 'btn-primary' : 'btn-outline' }} btn-sm" style="padding: 6px 12px; font-size: 0.8rem;">Minggu Ini</a>
                <a href="{{ route('borrows.history', ['filter' => 'month']) }}" class="btn {{ request('filter') === 'month' ? 'btn-primary' : 'btn-outline' }} btn-sm" style="padding: 6px 12px; font-size: 0.8rem;">Bulan Ini</a>
                <a href="{{ route('borrows.history', ['filter' => 'year']) }}" class="btn {{ request('filter') === 'year' ? 'btn-primary' : 'btn-outline' }} btn-sm" style="padding: 6px 12px; font-size: 0.8rem;">Tahun Ini</a>
            </div>
            
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <input type="hidden" name="filter" value="custom">
                <input type="date" name="start_date" class="form-control" style="width: auto; padding: 5px 10px; font-size: 0.8rem; height: 32px;" value="{{ request('start_date') }}" required>
                <span style="color: var(--gray-600); font-size: 0.8rem;">s/d</span>
                <input type="date" name="end_date" class="form-control" style="width: auto; padding: 5px 10px; font-size: 0.8rem; height: 32px;" value="{{ request('end_date') }}" required>
                <button type="submit" class="btn btn-secondary btn-sm" style="padding: 6px 12px; font-size: 0.8rem; height: 32px; background-color: var(--dark); border-color: var(--dark); color: white;">Filter</button>
            </div>
        </form>
        @if(isset($filterLabel))
            <div style="margin-top: 10px; font-size: 0.8rem; color: var(--gray-600); font-weight: bold;">
                <i class="fa-solid fa-filter" style="color: var(--primary); margin-right: 4px;"></i> Periode Aktif: {{ $filterLabel }}
            </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fa-solid fa-clock-rotate-left" style="color: var(--primary); margin-right: 8px;"></i> Seluruh Transaksi Peminjaman & Pengembalian</h2>
        <span class="badge badge-success" style="font-size: 0.85rem; padding: 6px 12px;">Total Transaksi: {{ $borrows->count() }}</span>
    </div>
    
    <div class="card-body">
        @if($borrows->isEmpty())
            <div style="text-align: center; padding: 60px 20px; color: var(--gray-600);">
                <i class="fa-solid fa-book-bookmark" style="font-size: 3rem; color: var(--gray-300); margin-bottom: 15px;"></i>
                <p style="font-weight: 600; color: var(--gray-700);">Belum Ada Riwayat Transaksi</p>
                <p style="font-size: 0.85rem; margin-top: 5px;">Transaksi peminjaman buku akan tercatat secara otomatis di sini.</p>
            </div>
        @else
            <div class="table-responsive" style="-webkit-overflow-scrolling:touch;">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Buku</th>
                            <th>Member</th>
                            <th>Tanggal Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Tanggal Kembali</th>
                            <th>Keterangan Sanksi</th>
                            <th>Status Peminjaman</th>
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
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 30px; height: 42px; border-radius: 4px; overflow: hidden; background-color: #f0f0f0; border: 1px solid var(--gray-200); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            @if($borrow->book->cover_image)
                                                <img src="{{ asset($borrow->book->cover_image) }}" alt="Cover" style="width: 100%; height: 100%; object-fit: cover;">
                                            @else
                                                <div style="width: 100%; height: 100%; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); display: flex; align-items: center; justify-content: center; color: var(--light);">
                                                    <i class="fa-solid fa-book" style="font-size: 0.7rem;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: var(--dark);">{{ $borrow->book->title }}</div>
                                            <small style="color: var(--gray-600); font-family: monospace;">{{ $borrow->book->barcode }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--dark);">{{ $borrow->member->user->name }}</div>
                                    <div style="font-size: 0.8rem; color: #b58b00; font-weight: 600;">{{ $borrow->member->member_code }}</div>
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
                                        <div style="font-weight: bold; color: var(--primary);">{{ $borrow->fine_amount }} Buku Fisik</div>
                                        @if($borrow->fine_status === 'unpaid')
                                            <span class="badge badge-danger" style="font-size: 0.75rem; padding: 3px 6px; display: inline-block; margin-top: 4px;"><i class="fa-solid fa-circle-exclamation"></i> Belum Dipenuhi</span>
                                        @elseif($borrow->fine_status === 'paid')
                                            <span class="badge badge-success" style="font-size: 0.75rem; padding: 3px 6px; display: inline-block; margin-top: 4px;"><i class="fa-solid fa-circle-check"></i> Sudah Dipenuhi</span>
                                        @endif
                                    @else
                                        <span style="color: #16a34a; font-size: 0.85rem; font-weight: 500;">Tepat Waktu</span>
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
