<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; color: #333; }
        .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border-top: 5px solid #1e3a8a; }
        .header { background: #1e3a8a; color: #ffffff; padding: 24px; text-align: center; }
        .header h2 { margin: 0; font-size: 22px; font-weight: 700; }
        .content { padding: 28px; }
        .info-group { margin-bottom: 16px; border-bottom: 1px solid #edf2f7; padding-bottom: 12px; }
        .info-label { font-weight: 600; color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .info-value { font-size: 16px; color: #1e293b; }
        .message-box { background: #f8fafc; border-left: 4px solid #3b82f6; padding: 16px; border-radius: 6px; font-style: italic; margin-top: 10px; }
        .footer { background: #f8fafc; padding: 16px; text-align: center; font-size: 13px; color: #94a3b8; border-top: 1px solid #edf2f7; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h2>Pertanyaan Baru Masuk</h2>
        </div>
        <div class="content">
            <p>Halo Tim Bawaslu,</p>
            <p>Terdapat pertanyaan baru yang diajukan oleh pengguna melalui portal Literawaslu:</p>
            
            <div class="info-group">
                <div class="info-label">Nama Pengirim</div>
                <div class="info-value">{{ $question->name }}</div>
            </div>

            <div class="info-group">
                <div class="info-label">Email Pengirim</div>
                <div class="info-value">{{ $question->email }}</div>
            </div>

            <div class="info-group">
                <div class="info-label">Waktu Pengiriman</div>
                <div class="info-value">{{ $question->created_at->format('d M Y, H:i') }} WIB</div>
            </div>

            <div class="info-group">
                <div class="info-label">Pesan Pertanyaan</div>
                <div class="message-box">"{{ $question->message }}"</div>
            </div>

            <p style="margin-top: 24px;">Silakan login ke <strong>Dashboard Super Admin</strong> Literawaslu untuk membalas pertanyaan ini secara langsung.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Literawaslu Bawaslu. All rights reserved.
        </div>
    </div>
</body>
</html>
