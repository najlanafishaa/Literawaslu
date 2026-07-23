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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @yield('styles')
</head>
<body>
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
                            <i class="fa-solid fa-chart-pie"></i> Dashboard
                        </a>
                    </li>
                    
                    <!-- Super Admin Menus -->
                    @if(auth()->user()->role === 'super_admin')
                        <li>
                            <a href="{{ route('books.index') }}" class="sidebar-link {{ request()->routeIs('books.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-book"></i> Kelola Buku
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('members.index') }}" class="sidebar-link {{ request()->routeIs('members.index') ? 'active' : '' }}">
                                <i class="fa-solid fa-users"></i> Kelola Member
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
                                <i class="fa-solid fa-file-invoice-dollar"></i> Laporan
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
                                <i class="fa-solid fa-users"></i> Daftar Member
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('books.index') }}" class="sidebar-link {{ request()->routeIs('books.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-book"></i> Kelola Buku
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                                <i class="fa-solid fa-calendar-days"></i> Laporan Bulanan
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
                                <i class="fa-solid fa-magnifying-glass"></i> Katalog Buku
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('member.card') }}" class="sidebar-link {{ request()->routeIs('member.card') ? 'active' : '' }}">
                                <i class="fa-solid fa-id-card"></i> Kartu Digital
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('member.history') }}" class="sidebar-link {{ request()->routeIs('member.history') ? 'active' : '' }}">
                                <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Pinjam
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('member.rewards') }}" class="sidebar-link {{ request()->routeIs('member.rewards') ? 'active' : '' }}">
                                <i class="fa-solid fa-award"></i> Reward & Poin
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
                            <i class="fa-solid fa-user-plus"></i> Daftar Member
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
                                @elseif(auth()->user()->role === 'petugas')
                                    Petugas
                                @else
                                    Member
                                @endif
                            </p>
                        </div>
                        <i class="fa-solid fa-pen-to-square" style="margin-left: auto; color: var(--gray-500); font-size: 0.8rem;"></i>
                        </div>
                    </a>
                @else
                    <p style="font-size: 0.8rem; text-align: center; color: rgba(255,255,255,0.4)">Sistem Perpustakaan</p>
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
                    <div class="page-title" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        @yield('header_title', 'Dashboard')
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

            <footer class="app-footer" style="padding: 16px 24px; text-align: center; background-color: var(--light); border-top: 1px solid var(--gray-200); margin-top: auto; font-size: 0.82rem; color: var(--gray-600);">
                <div style="font-weight: 600; color: var(--dark);">&copy; 2026 Bawaslu Provinsi Lampung</div>
                <div style="font-size: 0.77rem; color: var(--gray-600); margin-top: 2px;">Developed by Najla Princess&#x1F478;&#x1F3FB;</div>
            </footer>
        </div>
    </div>
    
    <!-- Toast Notifications -->
    <div class="toast-container" id="toastContainer">
        @if(session('success'))
            <div class="toast toast-success">
                <i class="fa-solid fa-circle-check" style="color: #22c55e;"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="toast">
                <i class="fa-solid fa-circle-xmark" style="color: var(--primary);"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if(session('warning'))
            <div class="toast toast-warning">
                <i class="fa-solid fa-circle-exclamation" style="color: var(--secondary);"></i>
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
            
            let icon = '<i class="fa-solid fa-circle-xmark" style="color: var(--primary);"></i>';
            if (type === 'success') {
                icon = '<i class="fa-solid fa-circle-check" style="color: #22c55e;"></i>';
            } else if (type === 'warning') {
                icon = '<i class="fa-solid fa-circle-exclamation" style="color: var(--secondary);"></i>';
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
    @yield('scripts')
</body>
</html>
