@extends('layouts.app')

@section('title', 'Kartu Anggota Digital')
@section('header_title', 'Kartu Anggota')

@section('content')
<div class="card" style="max-width: 550px; margin: 0 auto;">
    <div class="card-header" style="flex-wrap: wrap; gap: 10px;">
        <h2><i class="fa-solid fa-id-card" style="color: var(--primary); margin-right: 8px;"></i> Kartu Anggota Perpustakaan</h2>
        <div style="display: flex; gap: 8px;">
            <button onclick="downloadCard()" class="btn btn-primary btn-sm" style="background-color: var(--secondary); border-color: var(--secondary); color: var(--dark);">
                <i class="fa-solid fa-download"></i> Unduh Kartu (PNG)
            </button>
            <button onclick="window.print()" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-print"></i> Cetak Kartu
            </button>
        </div>
    </div>
    
    <div class="card-body" style="padding: 30px; display: flex; flex-direction: column; align-items: center; gap: 30px;">
        <!-- Premium 3D Flipping Card Container -->
        <div class="card-flip-container" style="perspective: 1000px; width: 100%; max-width: 450px; height: 260px; cursor: pointer;">
            <div class="card-flip-inner" id="membershipCard" onclick="toggleCardFlip()" style="position: relative; width: 100%; height: 100%; text-align: left; transition: transform 0.8s; transform-style: preserve-3d; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4); border-radius: 16px;">
                
                <!-- CARD FRONT -->
                <div class="card-front" style="position: absolute; width: 100%; height: 100%; -webkit-backface-visibility: hidden; backface-visibility: hidden; background: #b1b5b9 !important; color: #1A1A1A !important; border: 1px solid rgba(0,0,0,0.1); border-radius: 16px; padding: 25px; display: flex; flex-direction: column; justify-content: space-between; overflow: hidden;">
                    <!-- Elegant Inner Dashed Border Frame -->
                    <div style="position: absolute; top: 10px; left: 10px; right: 10px; bottom: 10px; border: 1px dashed rgba(26,26,26,0.15); border-radius: 12px; pointer-events: none; z-index: 2;"></div>
                    
                    <!-- Shiny Reflection Effect -->
                    <div style="position: absolute; top: -50%; right: -20%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 60%); border-radius: 50%; pointer-events: none;"></div>
                    
                    <!-- Center Watermark Logo Bawaslu -->
                    <img src="{{ asset('images/logo-bawaslu.png') }}" alt="Watermark Bawaslu" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); height: 150px; width: auto; opacity: 0.06; pointer-events: none; z-index: 1; filter: brightness(0);">
                    
                    <div class="digital-card-header" style="display: flex; justify-content: space-between; align-items: flex-start; z-index: 5;">
                        <div class="card-logo" style="display: flex; align-items: center; gap: 10px;">
                            <img src="{{ asset('images/logo-bawaslu.png') }}" alt="Logo Bawaslu" style="height: 52px; width: auto; object-fit: contain;">
                            <div style="font-size: 1.45rem; font-weight: 800; color: #1A1A1A; line-height: 1; font-family: 'Montserrat', sans-serif; letter-spacing: 0.5px;">
                                Literawaslu
                            </div>
                        </div>
                    </div>
                    
                    <div class="digital-card-body" style="margin-top: 10px; z-index: 5; display: flex; align-items: center; gap: 15px;">
                        <div>
                            <div class="member-name" style="font-size: 1.6rem; font-weight: 700; color: #1A1A1A; font-family: 'Montserrat', sans-serif; letter-spacing: 0.5px;">
                                {{ auth()->user()->name }}
                            </div>
                            <div class="member-id" style="font-size: 1.35rem; color: #1A1A1A; margin-top: 5px; font-family: monospace; letter-spacing: 2px; font-weight: bold;">
                                {{ $member->member_code }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="digital-card-footer" style="margin-top: 5px; display: flex; justify-content: space-between; align-items: flex-end; z-index: 5;">
                        <div class="card-info-item">
                            <label style="font-size: 0.68rem; text-transform: uppercase; color: rgba(0,0,0,0.55); display: block; letter-spacing: 1px; font-weight: 700; margin-bottom: 2px;">Berlaku Sampai</label>
                            <span style="font-size: 1rem; font-weight: 700; color: #1A1A1A; font-family: 'Montserrat', sans-serif;">{{ strtoupper($member->created_at->addYear(1)->locale('id')->translatedFormat('d F Y')) }}</span>
                        </div>
                    </div>

                </div>

                <!-- CARD BACK -->
                <div class="card-back" style="position: absolute; width: 100%; height: 100%; -webkit-backface-visibility: hidden; backface-visibility: hidden; transform: rotateY(180deg); background: #b1b5b9 !important; color: #1A1A1A !important; border: 1px solid rgba(0,0,0,0.1); border-radius: 16px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between; align-items: center; text-align: center; overflow: hidden;">
                    <!-- Elegant Inner Dashed Border Frame -->
                    <div style="position: absolute; top: 10px; left: 10px; right: 10px; bottom: 10px; border: 1px dashed rgba(26,26,26,0.15); border-radius: 12px; pointer-events: none; z-index: 2;"></div>
                    
                    <!-- Center Watermark Logo Bawaslu -->
                    <img src="{{ asset('images/logo-bawaslu.png') }}" alt="Watermark Bawaslu" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); height: 150px; width: auto; opacity: 0.06; pointer-events: none; z-index: 1; filter: brightness(0);">
                    
                    <!-- Top Logo Bawaslu Lampung -->
                    <div style="display: flex; align-items: center; gap: 10px; z-index: 5;">
                        <img src="{{ asset('images/logo-bawaslu.png') }}" alt="Logo Bawaslu" style="height: 56px; width: auto; object-fit: contain;">
                        <div style="text-align: left; line-height: 1.1;">
                            <div style="font-size: 1.3rem; font-weight: 800; color: #1A1A1A; font-family: 'Montserrat', sans-serif; letter-spacing: 0.5px;">BAWASLU</div>
                            <div style="font-size: 0.52rem; font-weight: 700; color: rgba(0,0,0,0.65); font-family: 'Montserrat', sans-serif; letter-spacing: 0.2px;">BADAN PENGAWAS PEMILIHAN UMUM</div>
                            <div style="font-size: 0.48rem; font-weight: 700; color: #1A1A1A; font-family: 'Montserrat', sans-serif; letter-spacing: 0.5px;">PROVINSI LAMPUNG</div>
                        </div>
                    </div>

                    <!-- Middle Bawaslu Quote -->
                    <div style="margin: auto 0; z-index: 5; max-width: 380px;">
                        <p style="font-size: 0.85rem; font-weight: 800; color: #1A1A1A; line-height: 1.5; font-family: 'Montserrat', sans-serif; letter-spacing: 0.2px; margin: 0; font-style: italic;">
                            "Bersama Rakyat Awasi Pemilu,<br>Bersama Bawaslu Tegakkan Keadilan Pemilu"
                        </p>
                    </div>

                    <!-- Bottom White Pill Badge (Social & Web Info) -->
                    <div style="background-color: #FFFFFF; color: #1A1A1A; border-radius: 20px; padding: 5px 15px; display: flex; align-items: center; justify-content: space-between; width: 100%; max-width: 395px; box-shadow: 0 4px 8px rgba(0,0,0,0.15); font-family: 'Montserrat', sans-serif; margin-top: 10px; z-index: 5;">
                        <!-- Web link with circular globe icon -->
                        <div style="display: flex; align-items: center; gap: 6px; font-weight: 700; font-size: 0.58rem;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 16px; height: 16px; border: 1.2px solid #1A1A1A; border-radius: 50%; font-size: 9px;">
                                <i class="fa-solid fa-globe"></i>
                            </span>
                            lampung.bawaslu.go.id
                        </div>
                        
                        <!-- Social links with circular icons -->
                        <div style="display: flex; align-items: center; gap: 6px; font-weight: 700; font-size: 0.58rem;">
                            <div style="display: flex; align-items: center; gap: 3px;">
                                <span style="display: inline-flex; align-items: center; justify-content: center; width: 14px; height: 14px; border: 1.2px solid #1A1A1A; border-radius: 50%; font-size: 7.5px;">
                                    <i class="fa-brands fa-facebook-f"></i>
                                </span>
                                <span style="display: inline-flex; align-items: center; justify-content: center; width: 14px; height: 14px; border: 1.2px solid #1A1A1A; border-radius: 50%; font-size: 7.5px;">
                                    <i class="fa-brands fa-x-twitter"></i>
                                </span>
                                <span style="display: inline-flex; align-items: center; justify-content: center; width: 14px; height: 14px; border: 1.2px solid #1A1A1A; border-radius: 50%; font-size: 7.5px;">
                                    <i class="fa-brands fa-instagram"></i>
                                </span>
                                <span style="display: inline-flex; align-items: center; justify-content: center; width: 14px; height: 14px; border: 1.2px solid #1A1A1A; border-radius: 50%; font-size: 7px;">
                                    <i class="fa-brands fa-youtube"></i>
                                </span>
                                <span style="display: inline-flex; align-items: center; justify-content: center; width: 14px; height: 14px; border: 1.2px solid #1A1A1A; border-radius: 50%; font-size: 8px; font-weight: bold; font-family: sans-serif; line-height: 1;">
                                    @
                                </span>
                            </div>
                            <span style="font-weight: 800; font-size: 0.58rem; margin-left: 2px;">Bawaslu Lampung</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <p style="font-size: 0.8rem; color: var(--gray-600); margin-top: -15px; font-style: italic;">
            *Klik kartu untuk membalik dan melihat bagian belakang.
        </p>

        <div style="background-color: var(--gray-50); border: 1px solid var(--gray-200); border-radius: var(--border-radius); padding: 20px; width: 100%;">
            <h4 style="font-size: 0.95rem; font-weight: 600; color: var(--dark); margin-bottom: 10px;">
                <i class="fa-solid fa-circle-info" style="color: var(--primary);"></i> Panduan Penggunaan Kartu Digital:
            </h4>
            <ul style="padding-left: 20px; font-size: 0.85rem; color: var(--gray-700); display: flex; flex-direction: column; gap: 8px;">
                <li>Tunjukkan kartu digital ini kepada Petugas Perpustakaan saat ingin melakukan transaksi peminjaman maupun pengembalian.</li>
                <li>Petugas akan mencari data keanggotaan Anda menggunakan Kode Anggota yang tertera pada sisi depan kartu.</li>
                <li>Kartu ini bersifat permanen dan tidak dapat dipindahtangankan.</li>
            </ul>
        </div>
    </div>
</div>

<style>
    /* 3D Card Flipping Styles */
    .card-flip-inner.flipped {
        transform: rotateY(180deg) !important;
    }
    
    @media print {
        body * {
            visibility: hidden;
        }
        .card-front, .card-front * {
            visibility: visible;
        }
        .card-front {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            box-shadow: none !important;
            background: #b1b5b9 !important;
            color: #1A1A1A !important;
            border: 1px solid rgba(0,0,0,0.1) !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    function toggleCardFlip() {
        const cardInner = document.getElementById('membershipCard');
        cardInner.classList.toggle('flipped');
    }

    function downloadCard() {
        const card = document.querySelector('.card-front');
        showToast('Memproses unduhan kartu anggota...', 'warning');
        
        // Wait briefly for rendering to settle
        setTimeout(() => {
            html2canvas(card, {
                scale: 3, // Very high definition
                backgroundColor: null, // transparent corners
                useCORS: true,
                logging: false
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'Kartu-Member-{{ $member->member_code }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                showToast('Kartu anggota berhasil diunduh!', 'success');
            }).catch(err => {
                showToast('Gagal mengunduh kartu: ' + err.message, 'danger');
            });
        }, 100);
    }
</script>
@endsection
