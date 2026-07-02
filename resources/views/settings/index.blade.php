@extends('layouts.app')

@section('title', 'Pengaturan')
@section('header_title', 'Pengaturan Sistem')

@section('content')
<div class="welcome-banner" style="margin-bottom: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; width: 100%;">
        <div>
            <h1>Pengaturan & Integrasi Perpustakaan</h1>
            <p>Atur identitas perpustakaan, parameter operasional, durasi pinjam, denda, dan kelola integrasi data Google Sheets & Google Sites.</p>
        </div>
        <div>
            <i class="fa-solid fa-sliders" style="font-size: 3rem; color: var(--light); opacity: 0.9;"></i>
        </div>
    </div>
</div>

@if($errors->any())
    <div style="background-color: rgba(var(--primary-rgb), 0.1); border: 1px solid var(--primary); color: var(--primary); padding: 12px; border-radius: var(--border-radius); font-size: 0.85rem; margin-bottom: 20px; font-weight: 500;">
        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
    </div>
@endif

<form action="{{ route('settings.update') }}" method="POST">
    @csrf
    
    <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px;">
        <!-- Column 1: Library Identity & Operational Parameters -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fa-solid fa-sliders" style="color: var(--primary); margin-right: 8px;"></i> Aturan & Parameter Operasional</h2>
            </div>
            <div class="card-body" style="padding: 25px; display: flex; flex-direction: column; gap: 20px;">
                <div class="form-group">
                    <label for="library_name" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; color: var(--dark);">
                        Nama Perpustakaan:
                    </label>
                    <input type="text" name="library_name" id="library_name" class="form-control" 
                           value="{{ $libraryName }}" placeholder="Contoh: Perpustakaan Literawas Bawaslu Lampung" required>
                </div>

                <div class="form-group">
                    <label for="loan_duration" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; color: var(--dark);">
                        Durasi Maksimal Peminjaman (Hari):
                    </label>
                    <input type="number" name="loan_duration" id="loan_duration" class="form-control" 
                           value="{{ $loanDuration }}" placeholder="7" required min="1">
                    <small style="color: var(--gray-600); display: block; margin-top: 5px; font-size: 0.8rem;">
                        Tenggat waktu pengembalian buku terhitung setelah checkout dilakukan.
                    </small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="late_fee" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; color: var(--dark);">
                            Denda Keterlambatan per Hari (Rp):
                        </label>
                        <input type="number" name="late_fee" id="late_fee" class="form-control" 
                               value="{{ $lateFee }}" placeholder="2000" required min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="reward_points" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; color: var(--dark);">
                            Poin Reward per Transaksi Sukses:
                        </label>
                        <input type="number" name="reward_points" id="reward_points" class="form-control" 
                               value="{{ $rewardPoints }}" placeholder="10" required min="0">
                    </div>
                </div>
            </div>
        </div>

        <!-- Column 2: External Integrations (Google Sheets & Google Sites) -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fa-solid fa-link" style="color: var(--secondary); margin-right: 8px;"></i> Integrasi Google Sheets & Google Sites</h2>
            </div>
            <div class="card-body" style="padding: 25px; display: flex; flex-direction: column; gap: 20px;">
                <div class="form-group">
                    <label for="google_sheets_url" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; color: var(--dark);">
                        Google Sheets Web App URL (Apps Script):
                    </label>
                    <input type="url" name="google_sheets_url" id="google_sheets_url" class="form-control" 
                           placeholder="https://script.google.com/macros/s/.../exec" 
                           value="{{ $googleSheetsUrl }}">
                    <small style="color: var(--gray-600); display: block; margin-top: 5px; font-size: 0.8rem;">
                        Masukkan URL Deployment Web App dari Google Apps Script untuk sinkronisasi database.
                    </small>
                </div>

                <div class="form-group">
                    <label for="google_sites_url" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; color: var(--dark);">
                        Google Sites Publish URL:
                    </label>
                    <input type="url" name="google_sites_url" id="google_sites_url" class="form-control" 
                           placeholder="https://sites.google.com/view/..." 
                           value="{{ $googleSitesUrl }}">
                    <small style="color: var(--gray-600); display: block; margin-top: 5px; font-size: 0.8rem;">
                        Tautkan URL portal Google Sites Anda yang menampilkan visual catalog atau profil perpustakaan.
                    </small>
                </div>
                
                @if(!empty($googleSitesUrl))
                    <div style="margin-top: 5px;">
                        <a href="{{ $googleSitesUrl }}" target="_blank" class="btn btn-outline btn-sm" style="width: 100%; text-align: center; display: inline-block; color: var(--secondary); border-color: var(--secondary);">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Portal Google Sites
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sticky Bottom Form Action Card -->
    <div class="card" style="margin-bottom: 25px;">
        <div class="card-body" style="padding: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h3 style="font-size: 0.95rem; font-weight: 600; color: var(--dark);">Simpan Seluruh Pengaturan</h3>
                <p style="font-size: 0.8rem; color: var(--gray-600);">Tekan simpan untuk memperbarui aturan perpustakaan dan data integrasi eksternal.</p>
            </div>
            <button type="submit" class="btn btn-primary" style="padding: 10px 30px;">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Pengaturan
            </button>
        </div>
    </div>
</form>

<div class="dashboard-grid" style="grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px;">
    <!-- Sync Actions Card -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-arrows-rotate" style="color: var(--primary); margin-right: 8px;"></i> Aksi Sinkronisasi Data</h2>
        </div>
        <div class="card-body" style="padding: 25px;">
            <p style="font-size: 0.85rem; color: var(--gray-700); margin-bottom: 20px; line-height: 1.5;">
                Gunakan tombol di bawah untuk langsung memicu proses pengiriman dan sinkronisasi seluruh tabel database lokal (Buku, Anggota, Transaksi) ke dalam Google Spreadsheet Anda.
            </p>

            @if(!empty($googleSheetsUrl))
                <form action="{{ route('settings.sync_sheets') }}" method="POST" id="syncForm">
                    @csrf
                    <button type="submit" class="btn btn-secondary" id="syncBtn" style="background-color: var(--secondary); border-color: var(--secondary); color: var(--dark); width: 100%; padding: 12px; font-weight: 600;">
                        <i class="fa-solid fa-rotate" id="syncIcon"></i> Mulai Sinkronisasi Sekarang
                    </button>
                </form>
            @else
                <div style="background-color: rgba(var(--primary-rgb), 0.05); color: var(--primary); padding: 15px; border-radius: var(--border-radius); font-size: 0.85rem; border: 1px dashed rgba(var(--primary-rgb), 0.2); text-align: center;">
                    <i class="fa-solid fa-circle-info"></i> Silakan isi dan simpan <strong>Google Sheets Web App URL</strong> di atas terlebih dahulu untuk mengaktifkan sinkronisasi.
                </div>
            @endif
        </div>
    </div>

    <!-- Help Setup Card -->
    <div class="card" style="border-color: var(--gray-200);">
        <div class="card-header" style="cursor: pointer; display: flex; justify-content: space-between; align-items: center;" onclick="toggleGuide()">
            <h2><i class="fa-solid fa-circle-question" style="color: var(--secondary); margin-right: 8px;"></i> Panduan Setup Google Apps Script</h2>
            <i class="fa-solid fa-chevron-down" id="guideChevron" style="color: var(--gray-600); transition: var(--transition);"></i>
        </div>
        <div class="card-body" id="guideBody" style="font-size: 0.85rem; line-height: 1.6; color: var(--gray-700); display: none; padding: 25px; border-top: 1px solid var(--gray-100);">
            <ol style="padding-left: 20px; display: flex; flex-direction: column; gap: 10px; margin-bottom: 15px;">
                <li>Buat sebuah <strong>Google Spreadsheet</strong> baru di Google Drive Anda.</li>
                <li>Buka menu <strong>Ekstensi (Extensions)</strong> &gt; <strong>Apps Script</strong>.</li>
                <li>Hapus kode bawaan, lalu salin dan tempelkan kode skrip di bawah ini:</li>
            </ol>
            
            <div style="margin-bottom: 15px; position: relative;">
                <textarea readonly style="width: 100%; height: 160px; font-family: monospace; font-size: 0.75rem; padding: 10px; border-radius: 8px; border: 1px solid var(--gray-300); background-color: var(--gray-50); resize: none;" id="scriptCode">function doPost(e) {
  try {
    var data = JSON.parse(e.postData.contents);
    var ss = SpreadsheetApp.getActiveSpreadsheet();
    
    // Sinkronisasi Sheet Buku
    var sheetBooks = ss.getSheetByName("Buku") || ss.insertSheet("Buku");
    sheetBooks.clear();
    sheetBooks.appendRow(["Barcode", "Judul Buku", "Penulis", "Penerbit", "Tahun Terbit", "Kategori", "Tersedia"]);
    data.books.forEach(function(b) {
      sheetBooks.appendRow([b.barcode, b.title, b.author, b.publisher, b.year, b.category, b.is_available ? "Ya" : "Tidak"]);
    });
    
    // Sinkronisasi Sheet Member
    var sheetMembers = ss.getSheetByName("Member") || ss.insertSheet("Member");
    sheetMembers.clear();
    sheetMembers.appendRow(["Kode Member", "Nama", "Email", "Total Peminjaman", "Poin Reward", "Batas Peminjaman", "Tanggal Gabung"]);
    data.members.forEach(function(m) {
      sheetMembers.appendRow([m.member_code, m.name, m.email, m.total_loans, m.points, m.borrow_limit, m.joined_at]);
    });
    
    // Sinkronisasi Sheet Peminjaman
    var sheetBorrows = ss.getSheetByName("Peminjaman") || ss.insertSheet("Peminjaman");
    sheetBorrows.clear();
    sheetBorrows.appendRow(["Nama Member", "Judul Buku", "Barcode", "Tanggal Pinjam", "Jatuh Tempo", "Tanggal Kembali", "Status"]);
    data.borrows.forEach(function(tr) {
      sheetBorrows.appendRow([tr.member_name, tr.book_title, tr.barcode, tr.borrow_date, tr.due_date, tr.return_date || "-", tr.status === 'borrowed' ? 'Sedang Dipinjam' : 'Dikembalikan']);
    });
    
    return ContentService.createTextOutput(JSON.stringify({status: "success", message: "Data synced successfully"}))
      .setMimeType(ContentService.MimeType.JSON);
  } catch(err) {
    return ContentService.createTextOutput(JSON.stringify({status: "error", message: err.toString()}))
      .setMimeType(ContentService.MimeType.JSON);
  }
}</textarea>
                <button onclick="copyScriptCode()" type="button" class="btn btn-outline btn-sm" style="margin-top: 5px; width: 100%; font-size: 0.75rem; padding: 6px;">
                    <i class="fa-solid fa-copy"></i> Salin Skrip Apps Script
                </button>
            </div>

            <ol start="4" style="padding-left: 20px; display: flex; flex-direction: column; gap: 10px;">
                <li>Klik ikon <strong>Simpan (Save)</strong> proyek.</li>
                <li>Klik tombol <strong>Terapkan (Deploy)</strong> &gt; <strong>Penerapan baru (New deployment)</strong>.</li>
                <li>Pilih tipe <strong>Aplikasi web (Web app)</strong>.</li>
                <li>Konfigurasikan:
                    <ul style="padding-left: 20px; margin-top: 5px; list-style-type: circle;">
                        <li><strong>Execute as:</strong> Me (Email Anda)</li>
                        <li><strong>Who has access:</strong> Anyone (Siapa saja)</li>
                    </ul>
                </li>
                <li>Klik <strong>Deploy</strong>, lalu berikan izin otorisasi keamanan yang diminta.</li>
                <li>Salin <strong>Web app URL</strong> yang terbit, lalu tempelkan ke kolom isian Google Sheets URL di atas.</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleGuide() {
        const guide = document.getElementById('guideBody');
        const chevron = document.getElementById('guideChevron');
        if (guide.style.display === 'none' || guide.style.display === '') {
            guide.style.display = 'block';
            chevron.style.transform = 'rotate(180deg)';
        } else {
            guide.style.display = 'none';
            chevron.style.transform = 'rotate(0deg)';
        }
    }

    function copyScriptCode() {
        const copyText = document.getElementById("scriptCode");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        showToast("Kode skrip berhasil disalin!", "success");
    }

    const syncForm = document.getElementById('syncForm');
    if (syncForm) {
        syncForm.addEventListener('submit', () => {
            const btn = document.getElementById('syncBtn');
            const icon = document.getElementById('syncIcon');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Menyingkronkan Data...';
            showToast("Memulai proses sinkronisasi ke Google Sheets. Harap tunggu...", "warning");
        });
    }
</script>
@endsection
