@extends('layouts.app')

@section('title', 'Kelola Pertanyaan Pengguna')
@section('header_title', 'Kelola Pertanyaan Pengguna')

@section('content')
<div class="content-header" style="margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 1.6rem; font-weight: 700; color: var(--dark); margin: 0 0 4px 0;">
            <i class="ti ti-messages" style="color: var(--primary); margin-right: 8px;"></i>
            Kelola Pertanyaan Pengguna
        </h1>
        <p style="color: var(--gray-600); margin: 0; font-size: 0.9rem;">
            Daftar pertanyaan yang diajukan oleh pengguna melalui tombol FAB. Anda dapat membaca dan membalas pesan secara manual.
        </p>
    </div>
</div>

<!-- Cards Filter & Stats -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <a href="{{ route('questions.index') }}" style="text-decoration: none;">
        <div style="background: #ffffff; border-radius: 12px; padding: 20px; border: 1.5px solid {{ request('status') ? '#e2e8f0' : '#3b82f6' }}; box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: all 0.2s;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 13px; font-weight: 600; color: #64748b;">TOTAL PERTANYAAN</span>
                <div style="width: 34px; height: 34px; border-radius: 8px; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center;">
                    <i class="ti ti-folder-open"></i>
                </div>
            </div>
            <div style="font-size: 24px; font-weight: 800; color: #0f172a;">{{ $pendingCount + $repliedCount }}</div>
        </div>
    </a>

    <a href="{{ route('questions.index', ['status' => 'pending']) }}" style="text-decoration: none;">
        <div style="background: #ffffff; border-radius: 12px; padding: 20px; border: 1.5px solid {{ request('status') === 'pending' ? '#ef4444' : '#e2e8f0' }}; box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: all 0.2s;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 13px; font-weight: 600; color: #b91c1c;">BELUM DIBALAS</span>
                <div style="width: 34px; height: 34px; border-radius: 8px; background: #fef2f2; color: #ef4444; display: flex; align-items: center; justify-content: center;">
                    <i class="ti ti-clock" style="font-size: 16px"></i>
                </div>
            </div>
            <div style="font-size: 24px; font-weight: 800; color: #ef4444;">{{ $pendingCount }}</div>
        </div>
    </a>

    <a href="{{ route('questions.index', ['status' => 'replied']) }}" style="text-decoration: none;">
        <div style="background: #ffffff; border-radius: 12px; padding: 20px; border: 1.5px solid {{ request('status') === 'replied' ? '#22c55e' : '#e2e8f0' }}; box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: all 0.2s;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 13px; font-weight: 600; color: #15803d;">SUDAH DIBALAS</span>
                <div style="width: 34px; height: 34px; border-radius: 8px; background: #f0fdf4; color: #22c55e; display: flex; align-items: center; justify-content: center;">
                    <i class="ti ti-circle-check"></i>
                </div>
            </div>
            <div style="font-size: 24px; font-weight: 800; color: #16a34a;">{{ $repliedCount }}</div>
        </div>
    </a>
</div>

<!-- Table Card -->
<div style="background: #ffffff; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 4px 16px rgba(0,0,0,0.04); overflow: hidden;">
    <!-- Filter and Search Bar -->
    <div style="padding: 16px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 12px;">
        <form action="{{ route('questions.index') }}" method="GET" style="display: flex; gap: 10px; flex: 1; max-width: 480px;">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div style="position: relative; flex: 1;">
                <i class="ti ti-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px;"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau pesan..." 
                    style="width: 100%; padding: 8px 12px 8px 34px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; outline: none;">
            </div>
            <button type="submit" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
                <i class="ti ti-filter"></i> Cari
            </button>
        </form>
        
        @if(request('search') || request('status'))
            <a href="{{ route('questions.index') }}" style="font-size: 13px; color: #ef4444; text-decoration: none; font-weight: 600;">
                <i class="ti ti-rotate"></i> Reset Filter
            </a>
        @endif
    </div>

    <!-- Table content -->
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 14px; text-align: left;">
            <thead>
                <tr style="background: #f1f5f9; color: #475569; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                    <th style="padding: 14px 20px;">Pengirim</th>
                    <th style="padding: 14px 20px;">Pesan Pertanyaan</th>
                    <th style="padding: 14px 20px;">Tanggal</th>
                    <th style="padding: 14px 20px;">Status</th>
                    <th style="padding: 14px 20px; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $q)
                    <tr style="border-bottom: 1px solid #edf2f7; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 16px 20px; vertical-align: top;">
                            <div style="font-weight: 700; color: #1e293b; font-size: 14px;">{{ $q->name }}</div>
                            <div style="font-size: 12.5px; color: #64748b; margin-top: 2px;">
                                <i class="ti ti-mail" style="margin-right: 4px;"></i>{{ $q->email }}
                            </div>
                        </td>
                        <td style="padding: 16px 20px; vertical-align: top; max-width: 320px;">
                            <div style="color: #334155; line-height: 1.5; font-size: 13.5px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $q->message }}">
                                "{{ $q->message }}"
                            </div>
                            @if($q->status === 'replied' && $q->reply)
                                <div style="margin-top: 8px; padding: 8px 12px; background: #f0fdf4; border-left: 3px solid #22c55e; border-radius: 4px; font-size: 12.5px; color: #166534;">
                                    <strong>Balasan:</strong> {{ Str::limit($q->reply, 80) }}
                                </div>
                            @endif
                        </td>
                        <td style="padding: 16px 20px; vertical-align: top; font-size: 13px; color: #64748b; whitespace: nowrap;">
                            {{ $q->created_at->format('d M Y') }}<br>
                            <span style="font-size: 11.5px; color: #94a3b8;">{{ $q->created_at->format('H:i') }} WIB</span>
                        </td>
                        <td style="padding: 16px 20px; vertical-align: top;">
                            @if($q->status === 'pending')
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; background: #fef2f2; color: #dc2626; font-size: 12px; font-weight: 700; border: 1px solid #fecaca;">
                                    <i class="ti ti-clock" style="font-size: 10px;"></i> Belum Dibalas
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; background: #f0fdf4; color: #16a34a; font-size: 12px; font-weight: 700; border: 1px solid #bbf7d0;">
                                    <i class="ti ti-circle-check" style="font-size: 10px;"></i> Sudah Dibalas
                                </span>
                            @endif
                        </td>
                        <td style="padding: 16px 20px; vertical-align: top; text-align: right;">
                            <button type="button" onclick="openReplyModal({{ json_encode($q) }})" 
                                class="btn btn-sm {{ $q->status === 'pending' ? 'btn-primary' : 'btn-outline' }}" 
                                style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; padding: 6px 14px;">
                                <i class="{{ $q->status === 'pending' ? 'ti ti-corner-up-left' : 'ti ti-eye' }}"></i>
                                <span>{{ $q->status === 'pending' ? 'Balas' : 'Detail / Edit' }}</span>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 40px; text-align: center; color: #94a3b8;">
                            <i class="ti ti-inbox" style="font-size: 36px; margin-bottom: 12px; display: block; color: #cbd5e1;"></i>
                            Tidak ada pertanyaan yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($questions->hasPages())
        <div style="padding: 16px 20px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
            {{ $questions->links() }}
        </div>
    @endif
</div>

<!-- Modal Balas Pertanyaan -->
<div id="adminReplyModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 100000; align-items: center; justify-content: center; padding: 16px; opacity: 0; transition: opacity 0.3s ease;">
    <div id="adminReplyCard" style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 560px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; transform: scale(0.95); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);">
        
        <!-- Header Modal -->
        <div style="background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%); padding: 20px 24px; color: #ffffff; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 38px; height: 38px; background: rgba(255,255,255,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <i class="ti ti-corner-up-left-double" style="font-size: 18px; color: #ffffff;"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 17px; font-weight: 700; color: #ffffff;">Balas Pertanyaan Pengguna</h3>
                    <p style="margin: 2px 0 0 0; font-size: 12px; color: rgba(255,255,255,0.8);">Jawaban akan langsung dikirim ke email pengguna</p>
                </div>
            </div>
            <button type="button" onclick="closeReplyModal()" style="background: transparent; border: none; color: rgba(255,255,255,0.8); font-size: 20px; cursor: pointer; padding: 4px 8px; border-radius: 6px;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.8)'">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <!-- Body Modal -->
        <div style="padding: 24px; max-height: 80vh; overflow-y: auto;">
            <!-- Informational detail of question -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                    <div>
                        <span id="modalSenderName" style="font-weight: 700; color: #0f172a; font-size: 15px;"></span>
                        <div id="modalSenderEmail" style="font-size: 12.5px; color: #64748b; margin-top: 1px;"></div>
                    </div>
                    <span id="modalQuestionDate" style="font-size: 12px; color: #94a3b8; font-weight: 500;"></span>
                </div>
                <div style="margin-top: 12px; font-size: 13.5px; color: #334155; line-height: 1.6; background: #ffffff; padding: 12px; border-radius: 6px; border-left: 4px solid #3b82f6;">
                    <strong style="display: block; font-size: 11px; text-transform: uppercase; color: #64748b; margin-bottom: 4px;">Pesan Pertanyaan:</strong>
                    <div id="modalQuestionMessage" style="white-space: pre-line;"></div>
                </div>
            </div>

            <!-- Form Balasan -->
            <form id="replyForm" method="POST">
                @csrf
                <div style="margin-bottom: 20px;">
                    <label for="replyInput" style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                        Tuliskan Balasan Admin <span style="color: #ef4444;">*</span>
                    </label>
                    <textarea id="replyInput" name="reply" rows="5" required placeholder="Tuliskan jawaban yang detail dan jelas..." 
                        style="width: 100%; padding: 12px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 14px; color: #1e293b; outline: none; transition: border-color 0.2s; resize: vertical; min-height: 120px;"
                        onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#cbd5e1'"></textarea>
                </div>

                <div style="display: flex; gap: 12px; justify-content: flex-end; align-items: center;">
                    <button type="button" onclick="closeReplyModal()" 
                        style="padding: 10px 18px; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer;">
                        Batal
                    </button>
                    <button type="submit" id="adminReplySubmitBtn" class="btn btn-primary" style="display: flex; align-items: center; gap: 8px; padding: 10px 22px;">
                        <i class="ti ti-send"></i>
                        <span>Kirim Balasan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openReplyModal(question) {
        const modal = document.getElementById('adminReplyModal');
        const card = document.getElementById('adminReplyCard');
        const form = document.getElementById('replyForm');

        document.getElementById('modalSenderName').innerText = question.name;
        document.getElementById('modalSenderEmail').innerText = question.email;
        document.getElementById('modalQuestionMessage').innerText = question.message;
        
        const dateObj = new Date(question.created_at);
        document.getElementById('modalQuestionDate').innerText = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

        document.getElementById('replyInput').value = question.reply || '';

        // Form action url
        form.action = `/admin/questions/${question.id}/reply`;

        modal.style.display = 'flex';
        setTimeout(() => {
            modal.style.opacity = '1';
            card.style.transform = 'scale(1)';
        }, 10);
    }

    function closeReplyModal() {
        const modal = document.getElementById('adminReplyModal');
        const card = document.getElementById('adminReplyCard');

        modal.style.opacity = '0';
        card.style.transform = 'scale(0.95)';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }
</script>
@endsection
