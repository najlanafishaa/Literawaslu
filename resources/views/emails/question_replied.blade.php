<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; color: #333; }
        .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border-top: 5px solid #16a34a; }
        .header { background: #16a34a; color: #ffffff; padding: 24px; text-align: center; }
        .header h2 { margin: 0; font-size: 22px; font-weight: 700; }
        .content { padding: 28px; }
        .quote-box { background: #f8fafc; border-left: 4px solid #94a3b8; padding: 14px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; color: #475569; }
        .reply-box { background: #f0fdf4; border-left: 4px solid #22c55e; padding: 18px; border-radius: 6px; margin-top: 10px; font-size: 15px; color: #14532d; line-height: 1.6; }
        .footer { background: #f8fafc; padding: 16px; text-align: center; font-size: 13px; color: #94a3b8; border-top: 1px solid #edf2f7; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h2>Jawaban Pertanyaan Anda</h2>
        </div>
        <div class="content">
            <p>Yth. <strong>{{ $question->name }}</strong>,</p>
            <p>Terima kasih telah menghubungi Bawaslu melalui layanan Literawaslu. Berikut adalah jawaban atas pertanyaan yang telah Anda ajukan:</p>
            
            <div style="font-weight: 600; font-size: 13px; color: #64748b; margin-top: 16px; margin-bottom: 6px;">PERTANYAAN ANDA:</div>
            <div class="quote-box">"{{ $question->message }}"</div>

            <div style="font-weight: 600; font-size: 13px; color: #16a34a; margin-top: 16px; margin-bottom: 6px;">TANGGAPAN / JAWABAN BAWASLU:</div>
            <div class="reply-box">{!! nl2br(e($question->reply)) !!}</div>

            <p style="margin-top: 24px; color: #475569; font-size: 14px;">Jika Anda memiliki pertanyaan lanjutan, Anda dapat mengajukannya kembali melalui tombol <strong>Ajukan Pertanyaan</strong> di portal Literawaslu.</p>
            <p style="color: #475569; font-size: 14px; margin-top: 16px;">Salam hangat,<br><strong>Tim Literawaslu Bawaslu</strong></p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Literawaslu Bawaslu. All rights reserved.
        </div>
    </div>
</body>
</html>
