<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Belum Diverifikasi | Literawaslu</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #E31E24;
            --primary-dark: #b51217;
            --dark: #0F172A;
            --light: #FFFFFF;
            --gray-50: #F8FAFC;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-600: #475569;
            --border-radius: 16px;
            --font-family: 'Plus Jakarta Sans', sans-serif;
            --font-title: 'Outfit', sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--gray-50);
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 550px;
            text-align: center;
        }

        .brand-logo {
            font-family: var(--font-title);
            font-weight: 800;
            font-size: 2.2rem;
            margin-bottom: 30px;
            color: var(--dark);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .brand-logo span {
            color: var(--primary);
        }

        .card {
            background: var(--light);
            border-radius: var(--border-radius);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            border: 1px solid var(--gray-200);
            padding: 40px 30px;
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--primary-dark));
        }

        .icon-wrapper {
            width: 90px;
            height: 90px;
            background-color: rgba(227, 30, 36, 0.1);
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 25px auto;
            animation: pulse 2s infinite ease-in-out;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(227, 30, 36, 0.2);
            }
            70% {
                transform: scale(1.05);
                box-shadow: 0 0 0 15px rgba(227, 30, 36, 0);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(227, 30, 36, 0);
            }
        }

        h1 {
            font-family: var(--font-title);
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 15px;
        }

        p {
            font-size: 0.95rem;
            color: var(--gray-600);
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .user-details {
            background-color: var(--gray-100);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: left;
            border: 1px dashed var(--gray-200);
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.85rem;
        }

        .detail-row:last-child {
            margin-bottom: 0;
        }

        .detail-label {
            font-weight: 600;
            color: var(--gray-600);
        }

        .detail-value {
            font-weight: 700;
            color: var(--dark);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 14px 20px;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            text-decoration: none;
            border: none;
        }

        .btn-logout {
            background-color: var(--gray-100);
            color: var(--dark);
            border: 1px solid var(--gray-200);
        }

        .btn-logout:hover {
            background-color: var(--gray-200);
            transform: translateY(-1px);
        }

        .footer-text {
            font-size: 0.8rem;
            color: var(--gray-600);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="brand-logo">
            Literawaslu
        </div>
        
        <div class="card">
            <div class="icon-wrapper">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            
            <h1>Menunggu Verifikasi Akun</h1>
            
            <p>
                Pendaftaran akun Anda berhasil dilakukan. Namun, untuk menjaga keamanan dan validitas data perpustakaan, akun Anda harus <strong>diverifikasi terlebih dahulu</strong> oleh petugas sebelum Anda dapat mengakses dashboard perpustakaan.
            </p>
            
            <div class="user-details">
                <div class="detail-row">
                    <span class="detail-label">Status Verifikasi:</span>
                    <span class="detail-value" style="color: var(--primary);"><i class="fa-solid fa-hourglass-half"></i> Belum Aktif</span>
                </div>
            </div>
            
            <a href="{{ route('login') }}" class="btn btn-logout">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Halaman Login
            </a>
        </div>
        
        <p class="footer-text">
            Silakan hubungi petugas perpustakaan di area pelayanan untuk mengaktifkan kartu anggota Anda.
        </p>
    </div>
</body>
</html>
