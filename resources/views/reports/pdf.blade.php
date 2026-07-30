<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman Buku - Literawaslu</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 12mm 12mm 12mm;
        }
        
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            color: #0f172a;
            background: #ffffff;
            margin: 0;
            padding: 0;
            font-size: 11.5px;
            line-height: 1.45;
        }

        /* Kop Surat Header */
        .kop-surat {
            border-bottom: 3px solid #D62027;
            padding-bottom: 12px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .kop-title {
            text-align: center;
            flex: 1;
        }

        .kop-logo {
            width: 65px;
            height: 65px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .kop-title h1 {
            margin: 0;
            font-size: 19px;
            font-weight: 800;
            color: #D62027;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .kop-title h2 {
            margin: 3px 0 0 0;
            font-size: 13.5px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-title p {
            margin: 4px 0 0 0;
            font-size: 10.5px;
            color: #64748b;
        }

        /* Metadata Bar */
        .meta-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 16px;
            margin-bottom: 18px;
            font-size: 11px;
        }

        .meta-item {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .meta-label {
            font-weight: 600;
            color: #64748b;
        }

        .meta-value {
            font-weight: 700;
            color: #0f172a;
        }

        /* Section Title */
        .section-title {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Summary Stats Cards Grid Table */
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin-bottom: 20px;
            margin-left: -8px;
            margin-right: -8px;
        }

        .summary-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px;
            text-align: center;
        }

        .summary-card .label {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
            letter-spacing: 0.4px;
        }

        .summary-card .val {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
        }

        /* Main Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #D62027;
        }

        .data-table th {
            background-color: #D62027 !important;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 10px;
            padding: 10px 8px;
            text-align: left;
            border: 1px solid #B91C1C;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .data-table td {
            padding: 8px 8px;
            border: 1px solid #e2e8f0;
            font-size: 10.5px;
            vertical-align: middle;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .text-center {
            text-align: center !important;
        }

        /* Status Badges */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            font-size: 9.5px;
            font-weight: 700;
            border-radius: 6px;
            text-align: center;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .badge-success { background-color: #dcfce7 !important; color: #15803d !important; border: 1px solid #bbf7d0 !important; }
        .badge-warning { background-color: #fff7ed !important; color: #ea580c !important; border: 1px solid #ffedd5 !important; }
        .badge-danger  { background-color: #fef2f2 !important; color: #dc2626 !important; border: 1px solid #fecaca !important; }
        .badge-secondary { background-color: #f1f5f9 !important; color: #475569 !important; border: 1px solid #e2e8f0 !important; }

        /* Signature Block */
        .signature-wrapper {
            margin-top: 25px;
            display: flex;
            justify-content: flex-end;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 260px;
            text-align: center;
        }

        .signature-date {
            font-size: 11px;
            margin-bottom: 4px;
            color: #475569;
        }

        .signature-role {
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 55px;
        }

        .signature-name {
            font-size: 12px;
            font-weight: 800;
            color: #0f172a;
            text-decoration: underline;
        }

        .no-print-bar {
            background-color: #1e293b;
            color: #ffffff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .btn-print {
            background-color: #D62027;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            font-weight: 700;
            font-size: 12px;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(214,32,39,0.3);
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div>
            <strong>Mode Cetak Dokumen Laporan Resmi</strong> &bull; Silakan klik tombol atau tekan <code>Ctrl + P</code> untuk menyimpan sebagai PDF.
        </div>
        <div>
            <button onclick="window.print()" class="btn-print">Cetak / Simpan PDF</button>
            <button onclick="window.close()" class="btn-print" style="background-color: #475569; margin-left: 6px;">Tutup</button>
        </div>
    </div>

    <!-- Kop Surat -->
    <div class="kop-surat">
        <img src="{{ asset('images/logo-bawaslu.png') }}" alt="Logo Bawaslu" class="kop-logo">
        <div class="kop-title">
            <h1>BAWASLU PROVINSI LAMPUNG</h1>
            <h2>PERPUSTAKAAN DIGITAL LITERAWASLU</h2>
            <p>Jl. Arif Rahman Hakim No.5, Jagabaya III, Kec. Way Halim, Kota Bandar Lampung, Lampung 35132</p>
        </div>
        <img src="{{ asset('images/logo-bawaslu.png') }}" alt="Logo Bawaslu" class="kop-logo" style="visibility: hidden;">
    </div>

    <!-- Meta Bar -->
    <div class="meta-bar">
        <div class="meta-item">
            <span class="meta-label">Periode Laporan:</span>
            <span class="meta-value">{{ $filterLabel ?? 'Semua Waktu' }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Tanggal Dicetak:</span>
            <span class="meta-value">{{ now()->format('d M Y, H:i') }} WIB</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Dicetak Oleh:</span>
            <span class="meta-value">{{ auth()->user()->name ?? 'Admin Perpustakaan' }} ({{ auth()->user()->role === 'super_admin' ? 'Super Admin' : 'Admin' }})</span>
        </div>
    </div>

    <!-- Summary Cards Grid -->
    <div class="section-title">I. RINGKASAN STATISTIK LAPORAN</div>
    <table class="summary-table">
        <tr>
            <td style="width: 20%; padding: 0;">
                <div class="summary-card">
                    <div class="label">Total Peminjaman</div>
                    <div class="val">{{ $totalBorrowCount }} Transaksi</div>
                </div>
            </td>
            <td style="width: 20%; padding: 0;">
                <div class="summary-card">
                    <div class="label">Jumlah Keterlambatan</div>
                    <div class="val" style="color: {{ $lateCount > 0 ? '#dc2626' : '#0f172a' }};">{{ $lateCount }} Kali</div>
                </div>
            </td>
            <td style="width: 20%; padding: 0;">
                <div class="summary-card">
                    <div class="label">Total Sanksi Donasi</div>
                    <div class="val">{{ $totalFineAmount }} Buku</div>
                </div>
            </td>
            <td style="width: 20%; padding: 0;">
                <div class="summary-card">
                    <div class="label">Donasi Dipenuhi</div>
                    <div class="val" style="color: #16a34a;">{{ $paidFineAmount }} Buku</div>
                </div>
            </td>
            <td style="width: 20%; padding: 0;">
                <div class="summary-card">
                    <div class="label">Donasi Belum Dipenuhi</div>
                    <div class="val" style="color: #dc2626;">{{ $unpaidFineAmount }} Buku</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Main Table Section -->
    <div class="section-title" style="margin-top: 15px;">II. RINCIAN DATA TRANSAKSI PEMINJAMAN</div>
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 28px;">No</th>
                <th style="width: 90px;">Kode Member</th>
                <th style="width: 130px;">Nama Pengguna</th>
                <th>Judul Buku</th>
                <th style="width: 85px;">Barcode</th>
                <th class="text-center" style="width: 75px;">Tgl Pinjam</th>
                <th class="text-center" style="width: 75px;">Jatuh Tempo</th>
                <th class="text-center" style="width: 75px;">Tgl Kembali</th>
                <th class="text-center" style="width: 95px;">Status</th>
                <th>Keterangan / Sanksi Keterlambatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($borrows as $index => $borrow)
                @php
                    $due = \Carbon\Carbon::parse($borrow->due_date);
                    $returnDate = $borrow->return_date ? \Carbon\Carbon::parse($borrow->return_date) : null;
                    
                    $lateDays = 0;
                    if ($returnDate && $returnDate->greaterThan($due)) {
                        $lateDays = (int) $due->copy()->diffInDays($returnDate->copy(), false);
                    } elseif (!$returnDate && \Carbon\Carbon::now()->startOfDay()->greaterThan($due)) {
                        $lateDays = (int) $due->copy()->diffInDays(\Carbon\Carbon::now()->startOfDay(), false);
                    }

                    $keterangan = 'Tepat Waktu';
                    if ($lateDays > 0) {
                        if ($lateDays == 1) {
                            $keterangan = "Terlambat 1 hari (-10 Poin)";
                        } elseif ($lateDays == 2) {
                            $keterangan = "Terlambat 2 hari (-20 Poin)";
                        } elseif ($lateDays == 3) {
                            $keterangan = "Terlambat 3 hari (-30 Poin)";
                        } else {
                            $keterangan = "Terlambat {$lateDays} hari (-10 Poin/hari + Wajib Donasi 1 Buku Fisik)";
                        }
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td style="font-family: monospace; font-weight: 600; color: #475569;">{{ $borrow->member ? $borrow->member->member_code : '-' }}</td>
                    <td><strong>{{ $borrow->member && $borrow->member->user ? $borrow->member->user->name : '-' }}</strong></td>
                    <td><strong style="color: #0f172a;">{{ $borrow->book ? $borrow->book->title : '-' }}</strong></td>
                    <td style="font-family: monospace; font-weight: 500; color: #475569;">{{ $borrow->book ? $borrow->book->barcode : '-' }}</td>
                    <td class="text-center">{{ $borrow->borrow_date ? \Carbon\Carbon::parse($borrow->borrow_date)->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">{{ $borrow->due_date ? \Carbon\Carbon::parse($borrow->due_date)->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">
                        {{ $borrow->return_date ? \Carbon\Carbon::parse($borrow->return_date)->format('d/m/Y') : 'Belum Kembali' }}
                    </td>
                    <td class="text-center">
                        @if($borrow->status === 'returned')
                            <span class="badge badge-success">Dikembalikan</span>
                        @elseif($borrow->status === 'borrowed')
                            <span class="badge badge-warning">Sedang Dipinjam</span>
                        @elseif($borrow->status === 'pending')
                            <span class="badge badge-secondary">Menunggu</span>
                        @elseif($borrow->status === 'terlambat')
                            <span class="badge badge-danger">Terlambat</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($borrow->status) }}</span>
                        @endif
                    </td>
                    <td style="{{ $lateDays > 0 ? 'color: #dc2626; font-weight: 700;' : 'color: #0f172a;' }}">
                        {{ $keterangan }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 24px; color: #64748b;">
                        Tidak ada data transaksi peminjaman pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Block -->
    <div class="signature-wrapper">
        <div class="signature-box">
            <div class="signature-date">Bandar Lampung, {{ now()->locale('id')->translatedFormat('d F Y') }}</div>
            <div class="signature-role">Mengetahui,<br>Admin Perpustakaan Literawaslu</div>
            <div class="signature-name">{{ auth()->user()->name ?? 'Admin Perpustakaan' }}</div>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
