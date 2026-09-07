@extends('layouts.app')

@section('title', 'Persetujuan Hapus Buku')
@section('header_title', 'Persetujuan Hapus Buku')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Daftar Pengajuan Penghapusan Buku</h2>
    </div>
    
    <div class="card-body">
        @if($requests->isEmpty())
            <p style="text-align: center; color: var(--gray-600); padding: 20px;">Belum ada pengajuan penghapusan buku.</p>
        @else
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Tanggal Pengajuan</th>
                            <th>Buku</th>
                            <th>Diajukan Oleh</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $req)
                            <tr>
                                <td>{{ $req->created_at->format('d M Y, H:i') }}</td>
                                <td>{{ $req->book ? $req->book->title : 'Buku sudah terhapus' }}</td>
                                <td>{{ $req->requestedBy ? $req->requestedBy->name : 'Admin' }}</td>
                                <td>
                                    @if($req->status === 'pending')
                                        <span class="badge badge-warning">Menunggu</span>
                                    @elseif($req->status === 'approved')
                                        <span class="badge badge-success">Disetujui</span>
                                    @else
                                        <span class="badge badge-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    @if($req->status === 'pending' && $req->book)
                                        <div style="display: flex; gap: 8px;">
                                            <form action="{{ route('superadmin.deletion_requests.approve', $req->id) }}" method="POST" onsubmit="return confirm('Setujui penghapusan buku ini? Buku akan permanen dihapus.');">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-sm" style="padding: 6px 10px;">Setujui</button>
                                            </form>
                                            <form action="{{ route('superadmin.deletion_requests.reject', $req->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-outline btn-sm" style="padding: 6px 10px; color: var(--danger); border-color: rgba(var(--danger-rgb), 0.2);">Tolak</button>
                                            </form>
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
</div>
@endsection
