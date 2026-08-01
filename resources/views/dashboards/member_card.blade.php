@extends('layouts.app')

@section('title', 'Kartu Pengguna Digital')
@section('header_title', 'Kartu Pengguna')

@section('content')
<div class="card" style="max-width: 550px; margin: 0 auto;">
    <div class="card-header" style="flex-wrap: wrap; gap: 10px;">
        <h2><i class="ti ti-id-badge" style="color: var(--primary); margin-right: 8px;"></i> Kartu Pengguna Perpustakaan</h2>
        <div style="display: flex; gap: 8px;">
            <button onclick="downloadCard()" class="btn btn-primary btn-sm" style="background-color: var(--secondary); border-color: var(--secondary); color: var(--dark);">
                <i class="ti ti-download"></i> Unduh Kartu (PDF)
            </button>
            <button onclick="window.print()" class="btn btn-outline btn-sm">
                <i class="ti ti-printer"></i> Cetak Kartu
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
                            <div style="font-size: 1.45rem; font-weight: 800; color: #1A1A1A; line-height: 1; letter-spacing: 0.5px;">
                                Literawaslu
                            </div>
                        </div>
                    </div>
                    
                    <div class="digital-card-body" style="margin-top: 10px; z-index: 5; display: flex; align-items: center; gap: 15px;">
                        <div>
                            <div class="member-name" style="font-size: 1.6rem; font-weight: 700; color: #1A1A1A; letter-spacing: 0.5px;">
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
                            <span style="font-size: 1rem; font-weight: 700; color: #1A1A1A;">{{ strtoupper($member->created_at->addYear(1)->locale('id')->translatedFormat('d F Y')) }}</span>
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
                            <div style="font-size: 1.3rem; font-weight: 800; color: #1A1A1A; letter-spacing: 0.5px;">BAWASLU</div>
                            <div style="font-size: 0.52rem; font-weight: 700; color: rgba(0,0,0,0.65); letter-spacing: 0.2px;">BADAN PENGAWAS PEMILIHAN UMUM</div>
                            <div style="font-size: 0.48rem; font-weight: 700; color: #1A1A1A; letter-spacing: 0.5px;">PROVINSI LAMPUNG</div>
                        </div>
                    </div>

                    <!-- Middle Bawaslu Quote -->
                    <div style="margin: auto 0; z-index: 5; max-width: 380px;">
                        <p style="font-size: 0.85rem; font-weight: 800; color: #1A1A1A; line-height: 1.5; letter-spacing: 0.2px; margin: 0; font-style: italic;">
                            "Bersama Rakyat Awasi Pemilu,<br>Bersama Bawaslu Tegakkan Keadilan Pemilu"
                        </p>
                    </div>

                    <!-- Bottom White Pill Badge (Social & Web Info) -->
                    <div style="background-color: #FFFFFF; color: #1A1A1A; border-radius: 20px; padding: 5px 15px; display: flex; align-items: center; justify-content: space-between; width: 100%; max-width: 395px; box-shadow: 0 4px 8px rgba(0,0,0,0.15); margin-top: 10px; z-index: 5;">
                        <!-- Web link with circular globe icon -->
                        <div style="display: flex; align-items: center; gap: 6px; font-weight: 700; font-size: 0.58rem;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 16px; height: 16px; border: 1.2px solid #1A1A1A; border-radius: 50%; font-size: 9px;">
                                <i class="ti ti-world"></i>
                            </span>
                            lampung.bawaslu.go.id
                        </div>
                        
                        <!-- Social links with circular icons -->
                        <div style="display: flex; align-items: center; gap: 6px; font-weight: 700; font-size: 0.58rem;">
                            <div style="display: flex; align-items: center; gap: 3px;">
                                <span style="display: inline-flex; align-items: center; justify-content: center; width: 14px; height: 14px; border: 1.2px solid #1A1A1A; border-radius: 50%; font-size: 7.5px;">
                                    <i class="ti ti-brand-facebook"></i>
                                </span>
                                <span style="display: inline-flex; align-items: center; justify-content: center; width: 14px; height: 14px; border: 1.2px solid #1A1A1A; border-radius: 50%; font-size: 7.5px;">
                                    <i class="ti ti-brand-x"></i>
                                </span>
                                <span style="display: inline-flex; align-items: center; justify-content: center; width: 14px; height: 14px; border: 1.2px solid #1A1A1A; border-radius: 50%; font-size: 7.5px;">
                                    <i class="ti ti-brand-instagram"></i>
                                </span>
                                <span style="display: inline-flex; align-items: center; justify-content: center; width: 14px; height: 14px; border: 1.2px solid #1A1A1A; border-radius: 50%; font-size: 7px;">
                                    <i class="ti ti-brand-youtube"></i>
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
                <i class="ti ti-info-circle" style="color: var(--primary);"></i> Panduan Penggunaan Kartu Digital:
            </h4>
            <ul style="padding-left: 20px; font-size: 0.85rem; color: var(--gray-700); display: flex; flex-direction: column; gap: 8px;">
                <li>Tunjukkan kartu digital ini kepada Admin Perpustakaan saat ingin melakukan transaksi peminjaman maupun pengembalian.</li>
                <li>Admin akan mencari data pengguna Anda menggunakan Kode Pengguna yang tertera pada sisi depan kartu.</li>
                <li>Kartu ini bersifat permanen dan tidak dapat dipindahtangankan.</li>
            </ul>
        </div>
    </div>
</div>

<!-- Print-only container. Left empty and hidden on screen; populated with
     clean clones of the front/back faces right before printing (see the
     'beforeprint' handler below), instead of trying to coax the on-screen
     3D-flip markup (position:absolute, rotateY transforms, backface-visibility)
     into printing correctly. -->
<div id="printCardArea" style="display: none;"></div>

<style>
    /* 3D Card Flipping Styles */
    .card-flip-inner.flipped {
        transform: rotateY(180deg) !important;
    }
    
    @media print {
    @media print {
        /* Make the printed page itself the size of the card (450x260px @ 96dpi
           converted to mm), instead of the browser's default A4/Letter. Note:
           this is only honored by Chrome's own "Save as PDF" destination -
           OS/driver-based printers like "Microsoft Print to PDF" ignore custom
           @page sizes and fall back to their own standard paper list. */
        @page {
            size: 119.06mm 68.79mm;
            margin: 0;
        }

        /* Hide literally everything else on the page, and show only the
           print-only container (populated by JS right before printing - see
           the 'beforeprint' handler). This avoids fighting the on-screen
           card's 3D-flip markup (position:absolute, rotateY transform,
           backface-visibility), which kept causing the back face to render
           wrong, overlap, or disappear entirely. */
        body > *:not(#printCardArea) {
            display: none !important;
        }
        #printCardArea {
            display: block !important;
        }
        #printCardArea > div {
            margin: 0 auto !important;
            page-break-inside: avoid;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        #printCardArea > div:first-child {
            page-break-after: always;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    // Builds two clean, plain copies of the card faces into #printCardArea
    // right before the browser prints - whether triggered by the "Cetak
    // Kartu" button (window.print()) or the user's own Ctrl+P. We strip out
    // all the 3D-flip-only styling (absolute positioning, rotateY transform,
    // backface-visibility) on the clones directly via JS, rather than trying
    // to fight it with @media print CSS overrides, which kept leaving the
    // back face broken, overlapping, or missing entirely.
    function preparePrintCard() {
        const front = document.querySelector('.card-front');
        const back = document.querySelector('.card-back');
        const area = document.getElementById('printCardArea');
        if (!front || !back || !area) return;

        // Must be a direct child of <body> - the print CSS only hides
        // "body > *:not(#printCardArea)", so if this container stayed nested
        // inside the normal page layout, its hidden ancestor would hide it too.
        if (area.parentElement !== document.body) {
            document.body.appendChild(area);
        }

        area.innerHTML = '';

        [front, back].forEach(original => {
            const clone = original.cloneNode(true);
            clone.removeAttribute('id');
            clone.style.position = 'static';
            clone.style.left = 'auto';
            clone.style.top = 'auto';
            clone.style.transform = 'none';
            clone.style.webkitTransform = 'none';
            clone.style.backfaceVisibility = 'visible';
            clone.style.webkitBackfaceVisibility = 'visible';
            clone.style.boxShadow = 'none';
            clone.style.margin = '0 auto';
            // Explicit px sizing set directly on the clone (inline style),
            // not left to the external #printCardArea CSS rule - the clone
            // still carries the original's inline "width:100%; height:100%"
            // copied over from cloneNode(), and inline style always beats an
            // external stylesheet rule unless that rule uses !important.
            // This was why the card kept coming out the wrong size again.
            clone.style.width = '450px';
            clone.style.height = '260px';
            clone.style.maxWidth = 'none';
            clone.style.minWidth = '0';
            clone.style.minHeight = '0';
            clone.style.boxSizing = 'border-box';
            clone.style.overflow = 'hidden';
            area.appendChild(clone);
        });
    }

    window.addEventListener('beforeprint', preparePrintCard);

    function toggleCardFlip() {
        const cardInner = document.getElementById('membershipCard');
        cardInner.classList.toggle('flipped');
    }

    function downloadCard() {
        const front = document.querySelector('.card-front');
        const back = document.querySelector('.card-back');
        showToast('Memproses unduhan kartu anggota...', 'warning');

        // The back face is normally rotated 180deg (rotateY) and hidden via
        // backface-visibility so it looks right in the 3D flip UI. html2canvas
        // can't render that 3D transform correctly, so we temporarily neutralize
        // it just for the capture, then restore it right after.
        const originalTransform = back.style.transform;
        const originalBackfaceVisibility = back.style.backfaceVisibility;
        back.style.transform = 'none';
        back.style.backfaceVisibility = 'visible';

        const restoreBack = () => {
            back.style.transform = originalTransform;
            back.style.backfaceVisibility = originalBackfaceVisibility;
        };

        // Wait briefly for rendering to settle
        setTimeout(() => {
            Promise.all([
                html2canvas(front, { scale: 3, backgroundColor: '#b1b5b9', useCORS: true, logging: false }),
                html2canvas(back, { scale: 3, backgroundColor: '#b1b5b9', useCORS: true, logging: false })
            ]).then(([frontCanvas, backCanvas]) => {
                restoreBack();

                const { jsPDF } = window.jspdf;

                // Use the card's fixed design size (matches the 450x260px
                // .card-front/.card-back size and the @page print size below)
                // instead of measuring the live element - offsetWidth/Height
                // can drift with layout/viewport and was making the PDF page
                // come out the wrong size (browser then defaulted to A4 and
                // shrank the card down to fit).
                const pxToMm = 0.2645833333;
                const CARD_WIDTH_PX = 450;
                const CARD_HEIGHT_PX = 260;
                const pageWidth = CARD_WIDTH_PX * pxToMm;   // ~119.06mm
                const pageHeight = CARD_HEIGHT_PX * pxToMm; // ~68.79mm
                const orientation = 'landscape';

                const pdf = new jsPDF({
                    orientation,
                    unit: 'mm',
                    format: [pageWidth, pageHeight]
                });

                // Page 1: front
                pdf.addImage(frontCanvas.toDataURL('image/png'), 'PNG', 0, 0, pageWidth, pageHeight);
                // Page 2: back
                pdf.addPage([pageWidth, pageHeight], orientation);
                pdf.addImage(backCanvas.toDataURL('image/png'), 'PNG', 0, 0, pageWidth, pageHeight);

                pdf.save('Kartu-Pengguna-{{ $member->member_code }}.pdf');
                showToast('Kartu pengguna berhasil diunduh (2 halaman)!', 'success');
            }).catch(err => {
                restoreBack();
                showToast('Gagal mengunduh kartu: ' + err.message, 'danger');
            });
        }, 100);
    }
</script>
@endsection