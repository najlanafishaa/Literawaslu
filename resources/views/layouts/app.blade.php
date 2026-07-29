<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Literawaslu') - Sistem Informasi Perpustakaan</title>

    
    <!-- CSS Dependencies -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @yield('styles')
</head>
<body class="@guest auth-page @endguest">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <a href="{{ route('dashboard') }}" style="text-decoration: none; color: inherit; display: block;">
                <div class="sidebar-brand" style="display: flex; flex-direction: column; align-items: flex-start; gap: 6px; padding: 20px 24px; cursor: pointer;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <img src="{{ asset('images/logo-bawaslu.png') }}" alt="Logo Bawaslu" style="height: 38px; width: auto; object-fit: contain;">
                        <div style="font-size: 1.35rem; font-weight: 700; color: var(--dark); line-height: 1;">
                            Litera<span style="color: var(--primary);">waslu</span>
                        </div>
                    </div>
                    <div style="font-size: 0.62rem; text-transform: uppercase; letter-spacing: 0.8px; color: var(--gray-600); font-weight: 700; line-height: 1; margin-left: 2px;">
                        Bawaslu Prov. Lampung
                    </div>
                </div>
            </a>
            
            <ul class="sidebar-menu">
                @auth
                    <!-- Common for all authenticated users -->
                    <li>
                        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-gauge-high"></i> Dashboard
                        </a>
                    </li>
                    
                    <!-- Super Admin Menus -->
                    @if(auth()->user()->role === 'super_admin')
                        <li>
                            <a href="{{ route('books.index') }}" class="sidebar-link {{ request()->routeIs('books.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-book-open"></i> Kelola Buku
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('members.index') }}" class="sidebar-link {{ request()->routeIs('members.index') ? 'active' : '' }}">
                                <i class="fa-solid fa-users"></i> Kelola Pengguna
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('accounts.index') }}" class="sidebar-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-user-shield"></i> Manajemen Akun
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('borrows.history') }}" class="sidebar-link {{ request()->routeIs('borrows.history') ? 'active' : '' }}">
                                <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Transaksi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                                <i class="fa-solid fa-chart-column"></i> Laporan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('verifications.index') }}" class="sidebar-link {{ request()->routeIs('verifications.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-check-double"></i> Verifikasi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.index') }}" class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-sliders"></i> Pengaturan
                            </a>
                        </li>
                        <li>
                            @php
                                $pendingQuestionCount = \App\Models\Question::where('status', 'pending')->count();
                            @endphp
                            <a href="{{ route('questions.index') }}" class="sidebar-link {{ request()->routeIs('questions.*') ? 'active' : '' }}" style="display: flex; align-items: center; justify-content: space-between;">
                                <span><i class="fa-solid fa-comments"></i> Kelola Pertanyaan</span>
                                @if($pendingQuestionCount > 0)
                                    <span style="background: #ef4444; color: #ffffff; font-size: 11px; font-weight: 700; padding: 2px 7px; border-radius: 20px; line-height: 1;">{{ $pendingQuestionCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif
                    
                    <!-- Regular Admin (Petugas) Menus -->
                    @if(in_array(auth()->user()->role, ['admin', 'petugas']))
                        <li>
                            <a href="{{ route('borrows.index') }}" class="sidebar-link {{ request()->routeIs('borrows.index') ? 'active' : '' }}">
                                <i class="fa-solid fa-hand-holding-hand"></i> Peminjaman Buku
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('members.index') }}" class="sidebar-link {{ request()->routeIs('members.index') ? 'active' : '' }}">
                                <i class="fa-solid fa-users"></i> Daftar Pengguna
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('books.index') }}" class="sidebar-link {{ request()->routeIs('books.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-book-open"></i> Kelola Buku
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                                <i class="fa-solid fa-chart-column"></i> Laporan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('verifications.index') }}" class="sidebar-link {{ request()->routeIs('verifications.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-check-double"></i> Verifikasi
                            </a>
                        </li>
                    @endif
                    
                    <!-- Member Menus -->
                    @if(in_array(auth()->user()->role, ['user', 'member']))
                        <li>
                            <a href="{{ route('catalog') }}" class="sidebar-link {{ request()->routeIs('catalog') ? 'active' : '' }}">
                                <i class="fa-solid fa-book-open-reader"></i> Katalog Buku
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('member.card') }}" class="sidebar-link {{ request()->routeIs('member.card') ? 'active' : '' }}">
                                <i class="fa-solid fa-id-card"></i> Kartu Digital
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('member.history') }}" class="sidebar-link {{ request()->routeIs('member.history') ? 'active' : '' }}">
                                <i class="fa-solid fa-scroll"></i> Riwayat Pinjam
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('member.rewards') }}" class="sidebar-link {{ request()->routeIs('member.rewards') ? 'active' : '' }}">
                                <i class="fa-solid fa-trophy"></i> Hadiah & Poin
                            </a>
                        </li>
                    @endif
                @else
                    <li>
                        <a href="{{ route('login') }}" class="sidebar-link {{ request()->routeIs('login') ? 'active' : '' }}">
                            <i class="fa-solid fa-right-to-bracket"></i> Masuk
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('register') }}" class="sidebar-link {{ request()->routeIs('register') ? 'active' : '' }}">
                            <i class="fa-solid fa-user-plus"></i> Daftar Pengguna
                        </a>
                    </li>
                @endauth
            </ul>
            
            <div class="sidebar-footer">
                @auth
                    <a href="{{ route('profile.edit') }}" style="text-decoration: none; color: inherit; display: block;">
                        <div class="user-badge" style="cursor: pointer; transition: background-color 0.2s; border-radius: 8px;">
                            <div class="user-avatar">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="user-info">
                                <h4>{{ auth()->user()->name }}</h4>
                            <p>
                                @if(auth()->user()->role === 'super_admin')
                                    Super Admin
                                @elseif(in_array(auth()->user()->role, ['admin', 'petugas']))
                                    Admin
                                @else
                                    Pengguna
                                @endif
                            </p>
                        </div>
                        <i class="fa-solid fa-pen-to-square" style="margin-left: auto; color: var(--gray-500); font-size: 0.8rem;"></i>
                        </div>
                    </a>
                @else
                    <p style="font-size: 0.8rem; text-align: center; color: var(--gray-600)">Sistem Perpustakaan</p>
                @endauth
            </div>
            <div class="sidebar-resizer" id="sidebarResizer"></div>
        </aside>
        
        <!-- Main Wrapper -->
        <div class="main-wrapper">
            <header class="header-nav">
                <div class="header-left" style="flex:1; min-width:0;">
                    <button type="button" class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Buka menu navigasi">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="mobile-header-brand">
                        <a href="{{ route('dashboard') }}" style="display: flex; align-items: center; gap: 6px; text-decoration: none;">
                            <img src="{{ asset('images/logo-bawaslu.png') }}" alt="Logo Bawaslu" style="height: 26px; width: auto; object-fit: contain;">
                            <div style="font-size: 1rem; font-weight: 700; color: var(--dark); line-height: 1;">
                                Litera<span style="color: var(--primary);">waslu</span>
                            </div>
                        </a>
                    </div>
                    <div class="page-title" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        @yield('header_title')
                    </div>
                </div>
                
                <div class="header-actions" style="flex-shrink:0;">
                    @auth
                        <a href="{{ route('profile.edit') }}" style="text-decoration: none;" title="Ubah Profil Saya">
                            @if(auth()->user()->role === 'super_admin')
                                <span class="role-badge role-super">Super Admin</span>
                            @elseif(in_array(auth()->user()->role, ['admin', 'petugas']))
                                <span class="role-badge role-petugas">Admin</span>
                            @else
                                <span class="role-badge role-member">Pengguna</span>
                            @endif
                        </a>
                        
                    @if(auth()->user()->role === 'super_admin')
                        @php
                            $headerPendingQuestions = \App\Models\Question::where('status', 'pending')->count();
                        @endphp
                        <a href="{{ route('questions.index') }}" title="Pertanyaan Baru Belum Dibalas" style="position: relative; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 8px; background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; transition: background 0.2s ease;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                            <i class="fa-solid fa-comments" style="font-size: 15px;"></i>
                            @if($headerPendingQuestions > 0)
                                <span style="position: absolute; top: -4px; right: -4px; background: #dc2626; color: white; font-size: 10px; font-weight: 700; min-width: 17px; height: 17px; padding: 0 4px; border-radius: 10px; display: flex; align-items: center; justify-content: center; line-height: 1; font-family: 'Plus Jakarta Sans', sans-serif; border: 2px solid white; box-shadow: 0 2px 4px rgba(220,38,38,0.3);">{{ $headerPendingQuestions }}</span>
                            @endif
                        </a>
                    @endif

                    @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'petugas']))
                        @php
                            $pendingResetCount = \App\Models\MemberResetRequest::where('status', 'pending')->count();
                        @endphp
                        @if($pendingResetCount > 0)
                            <a href="{{ route('verifications.index') }}" title="Ada permintaan reset password dari pengguna" style="position: relative; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 8px; background: #FEF3C7; border: 1px solid #FDE68A; color: #92400E; transition: background 0.2s ease;" onmouseover="this.style.background='#FDE68A'" onmouseout="this.style.background='#FEF3C7'">
                                <i class="fa-solid fa-bell" style="font-size: 15px;"></i>
                                <span style="position: absolute; top: -4px; right: -4px; background: #D62027; color: white; font-size: 10px; font-weight: 700; width: 17px; height: 17px; border-radius: 50%; display: flex; align-items: center; justify-content: center; line-height: 1; font-family: 'Plus Jakarta Sans', sans-serif; border: 2px solid white;">{{ $pendingResetCount }}</span>
                            </a>
                        @endif
                    @endif

                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-outline btn-sm" title="Log Out">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                <span class="btn-label-desktop">Keluar</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Masuk</a>
                    @endauth
                </div>
            </header>
            
            <main class="content-body">
                @yield('content')
            </main>

            <!-- Custom footer styles for social icon hovers -->
            <style>
                .footer-social-icon {
                    color: #FFFFFF !important;
                    font-size: 1.8rem;
                    text-decoration: none;
                    transition: color 0.2s ease-in-out;
                }
                .footer-social-icon:hover {
                    color: #D62027 !important;
                }
            </style>
            
            <footer class="app-footer" style="padding: 40px 24px; background: #000000; color: #cbd5e1; border-top: 4px solid #D62027; font-family: 'Plus Jakarta Sans', sans-serif;">
                <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; gap: 32px; justify-content: space-between; text-align: left;">
                    <!-- Maps Section (Left side) -->
                    <div style="flex: 1; min-width: 280px; max-width: 500px;">
                        <h4 style="color: #F5B025; font-size: 1.6rem; font-weight: 700; margin: 0 0 16px 0;">Lokasi Bawaslu Provinsi Lampung</h4>
                        <div style="border-radius: 12px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.5); border: 1px solid #222222; height: 230px; width: 100%; position: relative;">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1986.05771410446!2d105.281644!3d-5.39938!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e40db000915bd07%3A0xc8a687b6657dad9d!2sKantor%20Bawaslu%20Provinsi%20Lampung!5e0!3m2!1sid!2sid!4v1785314012897!5m2!1sid!2sid" width="100%" height="230" style="border:0; display:block; width:100%; height:100%;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
                        </div>
                    </div>
                    
                    <!-- Contact Section (Right side) -->
                    <div style="flex: 1; min-width: 280px;">
                        <h4 style="color: #F5B025; font-size: 1.6rem; font-weight: 700; margin: 0 0 16px 0;">Kontak Kami</h4>
                        
                        <div style="margin-bottom: 16px;">
                            <strong style="color: #FFFFFF; display: block; font-size: 1.1rem; font-weight: 700; margin-bottom: 6px;">Alamat</strong>
                            <p style="margin: 0; font-size: 0.95rem; line-height: 1.6; color: #cbd5e1;">
                                Badan Pengawas Pemilihan Umum Provinsi Lampung<br>
                                Jl. Arif Rahman Hakim No.5, Jagabaya III, Kec. Way Halim, Kota Bandar Lampung, Lampung 35132, Bandar Lampung, Indonesia
                            </p>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <strong style="color: #FFFFFF; display: block; font-size: 1.1rem; font-weight: 700; margin-bottom: 6px;">Email</strong>
                            <a href="mailto:bawasluu@gmail.com" style="color: #cbd5e1; text-decoration: underline; font-size: 0.95rem; transition: color 0.2s;" onmouseover="this.style.color='#D62027'" onmouseout="this.style.color='#cbd5e1'">bawasluu@gmail.com</a>
                        </div>

                        <!-- Social Media Icons -->
                        <div style="display: flex; gap: 20px; margin-top: 16px;">
                            <a href="https://www.facebook.com/bawasluprovinsilampung" target="_blank" title="Facebook" class="footer-social-icon"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="https://x.com/BawasluLampung_" target="_blank" title="Twitter/X" class="footer-social-icon"><i class="fa-brands fa-twitter"></i></a>
                            <a href="https://www.instagram.com/accounts/login/?next=https%3A%2F%2Fwww.instagram.com%2Fbawaslulampung%2F&is_from_rle" target="_blank" title="Instagram" class="footer-social-icon"><i class="fa-brands fa-instagram"></i></a>
                            <a href="https://www.youtube.com/@bawaslulampung3009" target="_blank" title="YouTube" class="footer-social-icon"><i class="fa-brands fa-youtube"></i></a>
                        </div>
                    </div>
                </div>

                <div style="max-width: 1200px; margin: 32px auto 0 auto; padding-top: 24px; border-top: 1px solid #222222; text-align: center; font-size: 0.85rem; color: #888888; line-height: 1.6;">
                    <div>&copy; 2026 Badan Pengawas Pemilihan Umum.</div>
                    <div>Seluruh hak cipta dilindungi undang-undang.</div>
                    <div style="font-size: 0.75rem; margin-top: 6px; color: #555555;">by develop nya najla & Annisa &#x1F478;&#x1F3FB;</div>
                </div>
            </footer>
        </div>
    </div>
    
    <!-- Toast Notifications -->
    <div class="toast-container" id="toastContainer">
        @if(session('success'))
            <div class="toast toast-success">
                <i class="fa-solid fa-bookmark"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="toast">
                <i class="fa-solid fa-circle-xmark"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if(session('warning'))
            <div class="toast toast-warning">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ session('warning') }}</span>
            </div>
        @endif
    </div>

    <!-- JS Scripts -->
    <script>
        // Simple Toast Auto Hide
        document.addEventListener('DOMContentLoaded', () => {
            const toasts = document.querySelectorAll('.toast');
            toasts.forEach(toast => {
                setTimeout(() => {
                    toast.style.animation = 'slideOut 0.3s forwards cubic-bezier(0.4, 0, 0.2, 1)';
                    setTimeout(() => {
                        toast.remove();
                    }, 300);
                }, 4000);
            });
        });

        // Dynamic Toast Helper
        function showToast(message, type = 'danger') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type === 'success' ? 'toast-success' : (type === 'warning' ? 'toast-warning' : '')}`;
            
            let icon = '<i class="fa-solid fa-triangle-exclamation"></i>';
            if (type === 'success') {
                icon = '<i class="fa-solid fa-bookmark"></i>';
            } else if (type === 'warning') {
                icon = '<i class="fa-solid fa-hourglass-half"></i>';
            }

            toast.innerHTML = `
                ${icon}
                <span>${message}</span>
            `;
            container.appendChild(toast);

            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s forwards cubic-bezier(0.4, 0, 0.2, 1)';
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 4000);
        }

        // Mobile Sidebar Toggle
        function toggleSidebar(force) {
            const shouldOpen = typeof force === 'boolean' ? force : !document.body.classList.contains('sidebar-open');
            document.body.classList.toggle('sidebar-open', shouldOpen);
        }

        function closeSidebar() {
            document.body.classList.remove('sidebar-open');
        }

        // Sidebar Resizer Logic
        function initSidebarResizer() {
            const resizer = document.getElementById('sidebarResizer');
            if (!resizer) return;

            let isDragging = false;

            resizer.addEventListener('mousedown', (e) => {
                isDragging = true;
                resizer.classList.add('is-dragging');
                document.body.style.cursor = 'col-resize';
                document.body.style.userSelect = 'none'; // prevent text selection
                e.preventDefault();
            });

            document.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                
                // Bounded between 200px and 450px
                let newWidth = e.clientX;
                if (newWidth < 200) newWidth = 200;
                if (newWidth > 450) newWidth = 450;
                
                document.documentElement.style.setProperty('--sidebar-width', newWidth + 'px');
                localStorage.setItem('sidebar-width', newWidth);
            });

            document.addEventListener('mouseup', () => {
                if (!isDragging) return;
                isDragging = false;
                resizer.classList.remove('is-dragging');
                document.body.style.cursor = '';
                document.body.style.userSelect = '';
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const overlay = document.getElementById('sidebarOverlay');

            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', () => toggleSidebar());
            }

            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }

            window.addEventListener('resize', () => {
                if (window.innerWidth > 992) {
                    closeSidebar();
                }
            });

            initSidebarResizer();
        });
    </script>
    <style>
        @keyframes slideOut {
            to {
                transform: translateY(100px);
                opacity: 0;
            }
        }
    </style>
    @if(!auth()->check() || !in_array(auth()->user()->role, ['super_admin', 'admin', 'petugas']))
        @include('components.fab_question')
    @endif
    @yield('scripts')
</body>
</html>
