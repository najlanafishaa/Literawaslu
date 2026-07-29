@extends('layouts.app')

@section('title', 'Notifikasi & Pesan Balasan')
@section('header_title', 'Notifikasi & Pesan Balasan')

@section('content')
<div class="welcome-banner" style="margin-bottom: 24px; padding: 25px 30px;">
    <div style="position: relative; z-index: 5; flex: 1; min-width: 220px;">
        <h1 style="font-size: 1.6rem; font-weight: 800; margin-bottom: 6px; display: flex; align-items: center; gap: 10px;">
            <i class="ti ti-bell" style="color: var(--secondary);"></i> Notifikasi & Pesan Balasan
        </h1>
        <p style="margin: 0; opacity: 0.9; font-size: 0.92rem;">
            Pusat informasi notifikasi pengingat jatuh tempo peminjaman dan balasan pertanyaan resmi dari Admin Bawaslu.
        </p>
    </div>
</div>

{{-- SECTION 1: PERINGATAN & PENGINGAT JATUH TEMPO PEMINJAMAN --}}
@php
    $dueReminders = $activeBorrows->filter(function($b) {
        $due = \Carbon\Carbon::parse($b->due_date);
        $now = \Carbon\Carbon::now()->startOfDay();
        $diff = $now->diffInDays($due, false);
        return $diff >= 0 && $diff <= 2;
    });
@endphp

<div class="card" style="margin-bottom: 24px; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #D62027 0%, #A01419 100%); color: #ffffff; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
        <h2 style="margin: 0; color: #ffffff !important; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
            <i class="ti ti-bell-ringing" style="color: #ffffff !important;"></i> Pengingat Jatuh Tempo Peminjaman
        </h2>
        <span class="badge" style="background: rgba(255,255,255,0.2); color: #ffffff !important; font-size: 0.78rem;">
            {{ $dueReminders->count() }} Perhatian
        </span>
    </div>
    <div class="card-body" style="padding: 20px;">
        @if($dueReminders->isEmpty())
            <div style="text-align: center; padding: 30px 20px; color: var(--gray-600);">
                <i class="ti ti-circle-check" style="font-size: 2.5rem; color: #16a34a; margin-bottom: 10px; display: block;"></i>
                <p style="font-weight: 700; color: #1F2937; font-size: 1rem; margin: 0 0 4px 0;">Tidak Ada Pengingat Keterlambatan</p>
                <p style="font-size: 0.85rem; color: #6B7280; margin: 0;">Semua peminjaman buku Anda saat ini dalam status aman & tepat waktu.</p>
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach($dueReminders as $rb)
                    @php
                        $rDue = \Carbon\Carbon::parse($rb->due_date);
                        $rNow = \Carbon\Carbon::now()->startOfDay();
                        $rDiff = $rNow->diffInDays($rDue, false);
                    @endphp
                    <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-left: 4px solid #D62027; border-radius: 10px; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <div style="width: 40px; height: 40px; border-radius: 10px; background: #FEF2F2; color: #D62027; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="ti ti-book" style="font-size: 20px;"></i>
                            </div>
                            <div>
                                <strong style="font-size: 0.95rem; color: #1F2937; display: block; font-weight: 700;">{{ $rb->book->title }}</strong>
                                <span style="font-size: 0.82rem; color: #6B7280;">Penulis: {{ $rb->book->author }} &bull; Tanggal Pinjam: {{ $rb->borrow_date->format('d M Y') }}</span>
                            </div>
                        </div>
                        <div>
                            @if($rDiff < 0)
                                <span class="badge badge-danger" style="font-size: 0.8rem; padding: 6px 14px; display: inline-flex; align-items: center; gap: 6px; background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; font-weight: 700;">
                                    <i class="ti ti-alert-triangle"></i> Terlambat {{ abs($rDiff) }} Hari
                                </span>
                            @elseif($rDiff == 0)
                                <span class="badge" style="font-size: 0.8rem; padding: 6px 14px; display: inline-flex; align-items: center; gap: 6px; background: #FFF7ED; color: #EA580C; border: 1px solid #FFEDD5; font-weight: 700;">
                                    <i class="ti ti-clock"></i> Jatuh Tempo Hari Ini!
                                </span>
                            @elseif($rDiff == 1)
                                <span class="badge" style="font-size: 0.8rem; padding: 6px 14px; display: inline-flex; align-items: center; gap: 6px; background: #FEFCE8; color: #CA8A04; border: 1px solid #FEF08A; font-weight: 700;">
                                    <i class="ti ti-hourglass"></i> {{ $rDiff }} Hari Lagi ({{ $rDue->format('d M Y') }})
                                </span>
                            @else
                                <span class="badge" style="font-size: 0.8rem; padding: 6px 14px; display: inline-flex; align-items: center; gap: 6px; background: #EFF6FF; color: #2563EB; border: 1px solid #BFDBFE; font-weight: 700;">
                                    <i class="ti ti-info-circle"></i> {{ $rDiff }} Hari Lagi ({{ $rDue->format('d M Y') }})
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- SECTION 2: PESAN PERTANYAAN & BALASAN DARI ADMIN --}}
<div class="card" style="overflow: hidden;">
    <div style="background: linear-gradient(135deg, var(--primary) 0%, #a01419 100%); color: #ffffff; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
        <h2 style="margin: 0; color: #ffffff !important; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
            <i class="ti ti-messages" style="color: #ffffff !important;"></i> Pesan & Balasan dari Admin
        </h2>
        <button onclick="toggleQuestionModal(true)" class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: #ffffff !important; border: 1px solid rgba(255,255,255,0.4); font-weight: 600;">
            <i class="ti ti-help-circle"></i> Ajukan Pertanyaan Baru
        </button>
    </div>
    <div class="card-body" style="padding: 20px;">
        @if(!isset($userQuestions) || $userQuestions->isEmpty())
            <div style="text-align: center; padding: 40px 20px; color: var(--gray-600);">
                <i class="ti ti-message-dots" style="font-size: 3rem; color: var(--gray-300); margin-bottom: 12px; display: block;"></i>
                <p style="font-weight: 700; color: #1e293b; font-size: 1rem; margin: 0 0 4px 0;">Belum Ada Pertanyaan yang Diajukan</p>
                <p style="font-size: 0.85rem; color: #64748b; margin: 0 0 16px 0;">Punya pertanyaan seputar layanan perpustakaan Bawaslu? Silakan tanyakan kepada Admin.</p>
                <button onclick="toggleQuestionModal(true)" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
                    <i class="ti ti-send"></i> Ajukan Pertanyaan Sekarang
                </button>
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 18px;">
                @foreach($userQuestions as $q)
                    <div style="border: 1px solid var(--gray-200); border-radius: 12px; overflow: hidden; background: #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
                        {{-- Question Header --}}
                        <div style="padding: 14px 18px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center;">
                                    <i class="ti ti-help-circle" style="font-size: 18px;"></i>
                                </div>
                                <div>
                                    <span style="font-weight: 700; font-size: 0.92rem; color: #1e293b; display: block;">Pertanyaan Anda</span>
                                    <span style="font-size: 0.76rem; color: #64748b;">Dikirim oleh: {{ $q->name }} ({{ $q->email }})</span>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="font-size: 0.78rem; color: #64748b;">{{ $q->created_at->format('d M Y H:i') }}</span>
                                @if($q->status === 'replied')
                                    <span class="badge badge-success" style="display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px;">
                                        <i class="ti ti-circle-check"></i> Sudah Dibalas
                                    </span>
                                @else
                                    <span class="badge badge-pending" style="display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px;">
                                        <i class="ti ti-clock"></i> Menunggu Balasan
                                    </span>
                                @endif
                                <form action="{{ route('questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus riwayat pertanyaan dan balasan ini?');" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm" style="padding: 4px 8px; color: #dc2626; border-color: #fecaca; background: #fef2f2;" title="Hapus Notifikasi / Pertanyaan Ini">
                                        <i class="ti ti-trash" style="font-size: 15px;"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Question Content --}}
                        <div style="padding: 16px 18px;">
                            <p style="margin: 0; font-size: 0.93rem; color: #334155; line-height: 1.6; white-space: pre-line;">{{ $q->message }}</p>
                        </div>

                        {{-- Admin Reply Content --}}
                        @if($q->status === 'replied' && $q->reply)
                            <div style="padding: 18px; background: #f0fdf4; border-top: 1px solid #bbf7d0;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; flex-wrap: wrap; gap: 6px;">
                                    <div style="display: flex; align-items: center; gap: 8px; color: #166534; font-weight: 700; font-size: 0.9rem;">
                                        <div style="width: 28px; height: 28px; border-radius: 6px; background: #dcfce7; color: #15803d; display: flex; align-items: center; justify-content: center;">
                                            <i class="ti ti-corner-down-right" style="font-size: 16px;"></i>
                                        </div>
                                        <span>Balasan Resmi dari Admin Bawaslu</span>
                                    </div>
                                    @if($q->replied_at)
                                        <span style="font-size: 0.78rem; color: #15803d; font-weight: 600;">{{ $q->replied_at->format('d M Y H:i') }}</span>
                                    @endif
                                </div>
                                <div style="background: #ffffff; padding: 14px 18px; border-radius: 10px; border: 1.5px solid #bbf7d0; box-shadow: 0 1px 4px rgba(22,101,52,0.05);">
                                    <p style="margin: 0; font-size: 0.93rem; color: #14532d; line-height: 1.6; white-space: pre-line;">{{ $q->reply }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
