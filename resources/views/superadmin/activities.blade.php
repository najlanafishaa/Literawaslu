@extends('layouts.app')

@section('title', 'Log Aktivitas Admin')
@section('header_title', 'Log Aktivitas Admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Riwayat Aktivitas Admin</h2>
    </div>
    
    <div class="card-body">
        @if($logs->isEmpty())
            <p style="text-align: center; color: var(--gray-600); padding: 20px;">Belum ada log aktivitas yang tercatat.</p>
        @else
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Admin</th>
                            <th>Aksi</th>
                            <th>Detail Deskripsi</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>{{ $log->created_at->format('d M Y, H:i') }}</td>
                                <td>{{ $log->user ? $log->user->name : 'Sistem' }}</td>
                                <td><span class="badge badge-primary" style="background-color: var(--primary); color: white;">{{ $log->action }}</span></td>
                                <td>{{ $log->description }}</td>
                                <td>{{ $log->ip_address }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top: 20px;">
                {{ $logs->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>
@endsection
