<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panduan & Interaktif: Responsive Web Design</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: var(--gray-900);
            --bg-card: var(--gray-800);
            --accent-primary: var(--primary);
            --accent-secondary: var(--secondary);
            --accent-gradient: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            --text-main: var(--gray-50);
            --text-muted: var(--gray-300);
            --border-color: var(--gray-700);
            --code-bg: var(--gray-900);
            --radius-lg: 16px;
            --radius-md: 10px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            line-height: 1.6;
            padding-bottom: 60px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 24px;
        }

        header {
            padding: 60px 0 40px;
            text-align: center;
            position: relative;
        }

        .badge {
            display: inline-block;
            padding: 6px 16px;
            background: rgba(99, 102, 241, 0.15);
            color: #818cf8;
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 99px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 16px;
        }

        h1 {
            font-size: 2.75rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 16px;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: 1.125rem;
            color: var(--text-muted);
            max-width: 650px;
            margin: 0 auto 32px;
        }

        section {
            margin-bottom: 48px;
        }

        .section-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 32px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title span.icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            background: rgba(99, 102, 241, 0.2);
            color: #818cf8;
            border-radius: 8px;
        }

        .section-text {
            color: var(--text-muted);
            margin-bottom: 24px;
            font-size: 1.025rem;
        }

        .code-container {
            background-color: var(--code-bg);
            border: 1px solid #1e293b;
            border-radius: var(--radius-md);
            padding: 16px 20px;
            font-family: 'Fira Code', monospace;
            font-size: 0.925rem;
            color: #e2e8f0;
            overflow-x: auto;
            margin-bottom: 24px;
        }

        .code-container .keyword { color: #f472b6; }
        .code-container .selector { color: #38bdf8; }
        .code-container .property { color: #a7f3d0; }
        .code-container .number { color: #fbbf24; }

        .demo-box {
            background: var(--bg-dark);
            border: 1px dashed var(--border-color);
            border-radius: var(--radius-md);
            padding: 24px;
        }

        .demo-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-color);
        }

        .demo-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--accent-secondary);
            text-transform: uppercase;
        }

        .width-indicator {
            font-family: 'Fira Code', monospace;
            font-size: 0.875rem;
            background: #1e293b;
            padding: 4px 12px;
            border-radius: 6px;
            color: #38bdf8;
        }

        .slider-control {
            margin-bottom: 20px;
        }
        .slider-control label {
            display: block;
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-bottom: 8px;
        }
        .slider-control input[type="range"] {
            width: 100%;
            height: 6px;
            background: #334155;
            border-radius: 3px;
            accent-color: var(--accent-primary);
        }

        .product-container {
            display: flex;
            gap: 16px;
            transition: all 0.4s ease;
            padding: 16px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
        }

        .product-card {
            flex: 1;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 10px;
            padding: 16px;
            text-align: center;
        }

        .techniques-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .technique-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 24px;
        }

        .technique-card h3 {
            font-size: 1.2rem;
            margin-bottom: 8px;
            color: #818cf8;
        }

        .technique-card p {
            font-size: 0.95rem;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        .unit-badge {
            font-family: 'Fira Code', monospace;
            padding: 2px 8px;
            background: rgba(6, 182, 212, 0.15);
            color: #22d3ee;
            border-radius: 4px;
            font-size: 0.85rem;
        }

        .mobile-first-steps {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 16px;
        }
        .step-box {
            flex: 1;
            min-width: 140px;
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            padding: 16px;
            border-radius: 8px;
            text-align: center;
        }

        @media (max-width: 768px) {
            h1 { font-size: 2rem; }
            .section-card { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <span class="badge">Edukasi Web Development</span>
            <h1>Responsive Web Design</h1>
            <p class="hero-desc">
                Teknik dan pendekatan modern untuk memastikan tampilan web dapat menyesuaikan berbagai ukuran layar secara optimal.
            </p>
        </header>

        <section>
            <div class="section-card">
                <h2 class="section-title"><span class="icon">⚡</span> 1. Media Query</h2>
                <p class="section-text">
                    Media Query memungkinkan developer menerapkan style berbeda berdasarkan ukuran layar.
                </p>

                <div class="code-container">
                    <span class="keyword">@media</span> (<span class="property">max-width</span>: <span class="number">768px</span>) {<br>
                    &nbsp;&nbsp;<span class="selector">.product</span> {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="property">flex-direction</span>: column;<br>
                    &nbsp;&nbsp;}<br>
                    }
                </div>

                <p class="section-text">
                    Saat lebar layar berada di bawah <strong>768 piksel</strong>, susunan produk yang sebelumnya menyamping akan berubah menjadi vertikal.
                </p>

                <div class="demo-box">
                    <div class="demo-header">
                        <span class="demo-label">Simulasi Perubahan Layout</span>
                        <span class="width-indicator" id="widthDisplay">Simulasi Lebar: 850px</span>
                    </div>

                    <div class="slider-control">
                        <label for="widthSlider">Geser slider lebar layar:</label>
                        <input type="range" id="widthSlider" min="360" max="900" value="850">
                    </div>

                    <div class="product-container" id="productDemo" style="flex-direction: row;">
                        <div class="product-card">
                            <div style="background: linear-gradient(135deg, #6366f1, #a855f7); height: 80px; border-radius: 6px; margin-bottom: 8px;"></div>
                            <h4>Produk 1</h4>
                            <p>Tampilan Menyamping</p>
                        </div>
                        <div class="product-card">
                            <div style="background: linear-gradient(135deg, #06b6d4, #3b82f6); height: 80px; border-radius: 6px; margin-bottom: 8px;"></div>
                            <h4>Produk 2</h4>
                            <p>Tampilan Menyamping</p>
                        </div>
                        <div class="product-card">
                            <div style="background: linear-gradient(135deg, #10b981, #059669); height: 80px; border-radius: 6px; margin-bottom: 8px;"></div>
                            <h4>Produk 3</h4>
                            <p>Tampilan Menyamping</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <h2 style="font-size: 1.75rem; margin-bottom: 20px;">Teknik Pendukung Responsive Web Design</h2>
            <p class="section-text">
                Responsive Web Design bukan cuma soal Media Query. Developer biasanya menggabungkan beberapa teknik berikut:
            </p>

            <div class="techniques-grid">
                <div class="technique-card">
                    <h3>📐 Flexible Layout</h3>
                    <p>Menggunakan Flexbox atau CSS Grid agar susunan elemen mudah menyesuaikan.</p>
                </div>

                <div class="technique-card">
                    <h3>📏 Relative Units</h3>
                    <p>Menggunakan satuan seperti <span class="unit-badge">%</span>, <span class="unit-badge">rem</span>, atau <span class="unit-badge">vw</span>, bukan selalu ukuran yang tetap (px).</p>
                </div>

                <div class="technique-card">
                    <h3>🖼️ Responsive Images</h3>
                    <p>Memastikan gambar tidak melewati ukuran layar (<span class="unit-badge">max-width: 100%</span>) atau memuat ukuran gambar yang sesuai perangkat.</p>
                </div>

                <div class="technique-card" style="grid-column: 1 / -1;">
                    <h3>📱 Mobile-first Design</h3>
                    <p>Merancang tampilan untuk layar kecil terlebih dahulu, lalu mengembangkannya untuk layar yang lebih besar.</p>

                    <div class="mobile-first-steps">
                        <div class="step-box">
                            <div style="font-size: 1.5rem;">📱</div>
                            <strong>Mobile</strong>
                            <p style="font-size: 0.8rem; color: var(--text-muted);">1 Column</p>
                        </div>
                        <div class="step-box">
                            <div style="font-size: 1.5rem;">💻</div>
                            <strong>Tablet</strong>
                            <p style="font-size: 0.8rem; color: var(--text-muted);">2 Columns</p>
                        </div>
                        <div class="step-box">
                            <div style="font-size: 1.5rem;">🖥️</div>
                            <strong>Desktop</strong>
                            <p style="font-size: 0.8rem; color: var(--text-muted);">Multi Column Grid</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        const slider = document.getElementById('widthSlider');
        const widthDisplay = document.getElementById('widthDisplay');
        const productDemo = document.getElementById('productDemo');

        slider.addEventListener('input', function() {
            const val = this.value;
            widthDisplay.textContent = `Simulasi Lebar: ${val}px`;

            if (val <= 768) {
                productDemo.style.flexDirection = 'column';
                productDemo.style.border = '2px dashed #f472b6';
            } else {
                productDemo.style.flexDirection = 'row';
                productDemo.style.border = '1px dashed var(--border-color)';
            }
        });
    </script>
</body>
</html>
