<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman Buku - Literawaslu</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 15mm 15mm 15mm;
        }
        
        body {
            font-family: 'Outfit', Arial, sans-serif;
            color: #1e293b;
            background: #ffffff;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.4;
        }

        /* Kop Surat Header */
        .kop-surat {
            border-bottom: 3px double #99131a;
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .kop-title {
            text-align: center;
            width: 100%;
        }

        .kop-title h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            color: #99131a;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .kop-title h2 {
            margin: 4px 0 0 0;
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
        }

        .kop-title p {
            margin: 3px 0 0 0;
            font-size: 11px;
            color: #64748b;
        }

        /* Metadata & Filter Bar */
        .meta-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 15px;
            margin-bottom: 20px;
            font-size: 11px;
        }

        .meta-item {
            display: flex;
            gap: 5px;
        }

        .meta-label {
            font-weight: 600;
            color: #475569;
        }

        .meta-value {
            font-weight: 700;
            color: #0f172a;
        }

        /* Summary Stats Cards Grid */
        .section-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #0f172a;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .summary-table th {
            background-color: #99131a;
            color: #ffffff;
            font-weight: 600;
            font-size: 11px;
            padding: 8px 10px;
            text-align: center;
            border: 1px solid #99131a;
        }

        .summary-table td {
            background-color: #ffffff;
            color: #0f172a;
            font-weight: 700;
            font-size: 12px;
            padding: 8px 10px;
            text-align: center;
            border: 1px solid #cbd5e1;
        }

        /* Main Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .data-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: 600;
            font-size: 10.5px;
            padding: 8px 6px;
            text-align: left;
            border: 1px solid #1e293b;
            text-transform: uppercase;
        }

        .data-table td {
            padding: 7px 6px;
            font-size: 10.5px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }

        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 9.5px;
            font-weight: 700;
            border-radius: 4px;
            text-align: center;
        }
        .badge-success { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-warning { background-color: #fef9c3; color: #a16207; border: 1px solid #fef08a; }
        .badge-danger { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .badge-secondary { background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

        /* Signature Block */
        .signature-wrapper {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 250px;
            text-align: center;
        }

        .signature-date {
            font-size: 11px;
            margin-bottom: 5px;
            color: #475569;
        }

        .signature-role {
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 60px;
        }

        .signature-name {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            text-decoration: underline;
        }

        .signature-nip {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }

        .no-print-bar {
            background-color: #0f172a;
            color: #ffffff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .btn-print {
            background-color: #99131a;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            font-weight: 600;
            font-size: 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        @media print {
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
            <button onclick="window.print()" class="btn-print">Cetak / Cetak PDF</button>
            <button onclick="window.close()" class="btn-print" style="background-color: #475569; margin-left: 5px;">Tutup</button>
        </div>
    </div>

    <!-- Kop Surat -->
    <div class="kop-surat">
        <div class="kop-title">
            <h1>PERPUSTAKAAN DIGITAL LITERAWASLU</h1>
            <h2>LAPORAN REKAPITULASI HASIL PEMINJAMAN & PENGEMBALIAN BUKU</h2>
            <p>Sistem Informasi Pengelolaan Perpustakaan Digital &bull; Literawaslu Official Document</p>
        </div>
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
            <span class="meta-value">{{ auth()->user()->name ?? 'Petugas Perpustakaan' }} ({{ ucfirst(auth()->user()->role ?? 'Petugas') }})</span>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="section-title">I. Ringkasan Statistik Laporan</div>
    <table class="summary-table">
        <thead>
            <tr>
                <th>Total Peminjaman</th>
                <th>Jumlah Keterlambatan</th>
                <th>Total Sanksi Donasi Buku</th>
                <th>Donasi Buku Dipenuhi</th>
                <th>Donasi Buku Belum Dipenuhi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $totalBorrowCount }} Transaksi</td>
                <td>{{ $lateCount }} Kali</td>
                <td>{{ $totalFineAmount }} Buku</td>
                <td style="color: #15803d;">{{ $paidFineAmount }} Buku</td>
                <td style="color: #b91c1c;">{{ $unpaidFineAmount }} Buku</td>
            </tr>
        </tbody>
    </table>

    <!-- Main Table Section -->
    <div class="section-title">II. Rincian Data Transaksi Peminjaman</div>
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">No</th>
                <th style="width: 85px;">Kode Member</th>
                <th>Nama Anggota</th>
                <th>Judul Buku</th>
                <th style="width: 80px;">Barcode</th>
                <th class="text-center" style="width: 75px;">Tgl Pinjam</th>
                <th class="text-center" style="width: 75px;">Jatuh Tempo</th>
                <th class="text-center" style="width: 75px;">Tgl Kembali</th>
                <th class="text-center" style="width: 85px;">Status</th>
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
                        $lateDays = (int) $returnDate->diffInDays($due);
                    } elseif (!$returnDate && \Carbon\Carbon::now()->startOfDay()->greaterThan($due)) {
                        $lateDays = (int) \Carbon\Carbon::now()->startOfDay()->diffInDays($due);
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
                            $keterangan = "Terlambat {$lateDays} hari (Wajib Donasi 1 Buku Fisik)";
                        }
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td style="font-family: monospace; font-weight: 600;">{{ $borrow->member ? $borrow->member->member_code : '-' }}</td>
                    <td><strong>{{ $borrow->member && $borrow->member->user ? $borrow->member->user->name : '-' }}</strong></td>
                    <td>{{ $borrow->book ? $borrow->book->title : '-' }}</td>
                    <td style="font-family: monospace;">{{ $borrow->book ? $borrow->book->barcode : '-' }}</td>
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
                    <td style="{{ $lateDays > 0 ? 'color: #b91c1c; font-weight: 600;' : 'color: #15803d;' }}">
                        {{ $keterangan }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada data transaksi peminjaman pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Block -->
    <div class="signature-wrapper">
        <div class="signature-box">
            <div class="signature-date">Yogyakarta, {{ now()->format('d F Y') }}</div>
            <div class="signature-role">Mengetahui,<br>Petugas Perpustakaan Literawaslu</div>
            <div class="signature-name">{{ auth()->user()->name ?? 'Petugas Perpustakaan' }}</div>
            <div class="signature-nip">NIP/ID: {{ auth()->user()->id ? 'PLW-' . str_pad(auth()->user()->id, 4, '0', STR_PAD_LEFT) : '-' }}</div>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // Auto open print dialog when opened
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
