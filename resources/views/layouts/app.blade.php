<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MCI System') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            :root {
                --sidebar-width: 280px;
                --mci-blue: #0d6efd;
                --mci-blue-dark: #0a58ca;
                --mci-accent: #7c3aed;
                --mci-accent-dark: #6d28d9;
                --sidebar-bg: #ffffff;
                --body-bg: #f4f7fe;
                --font-sans: "Plus Jakarta Sans", system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
            }
            body {
                background:
                    radial-gradient(950px circle at 12% 0%, rgba(13, 110, 253, 0.16) 0%, rgba(13, 110, 253, 0) 58%),
                    radial-gradient(950px circle at 88% 12%, rgba(124, 58, 237, 0.14) 0%, rgba(124, 58, 237, 0) 58%),
                    var(--body-bg);
                overflow-x: hidden;
                font-family: var(--font-sans);
                font-size: 0.95rem;
                line-height: 1.45;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }
            h1, h2, h3, h4, h5, h6 {
                letter-spacing: -0.02em;
            }
            #wrapper {
                display: flex;
                width: 100%;
            }
            #sidebar-wrapper {
                width: var(--sidebar-width);
                height: 100vh;
                background:
                    radial-gradient(700px circle at 20% 0%, rgba(13, 110, 253, 0.20) 0%, rgba(13, 110, 253, 0) 62%),
                    radial-gradient(700px circle at 85% 18%, rgba(124, 58, 237, 0.16) 0%, rgba(124, 58, 237, 0) 62%),
                    linear-gradient(180deg, #0f172a 0%, #111827 100%);
                border-right: 1px solid rgba(255,255,255,0.08);
                position: fixed;
                left: 0;
                top: 0;
                z-index: 1030;
                transition: all 0.3s ease;
                box-shadow: 14px 0 38px rgba(2, 6, 23, 0.18);
                overflow-y: auto;
            }
            #page-content-wrapper {
                width: 100%;
                margin-left: var(--sidebar-width);
                transition: all 0.3s ease;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }
            
            /* Toggle Sidebar */
            #wrapper.toggled #sidebar-wrapper {
                left: calc(var(--sidebar-width) * -1);
            }
            #wrapper.toggled #page-content-wrapper {
                margin-left: 0;
            }

            @media (max-width: 991.98px) {
                #sidebar-wrapper {
                    left: calc(var(--sidebar-width) * -1);
                }
                #page-content-wrapper {
                    margin-left: 0;
                }
                #wrapper.toggled #sidebar-wrapper {
                    left: 0;
                }
            }

            .sidebar-heading {
                padding: 2rem 1.5rem;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                position: relative;
            }
            .sidebar-heading::after {
                content: "";
                position: absolute;
                left: 50%;
                bottom: 12px;
                transform: translateX(-50%);
                width: 64px;
                height: 4px;
                border-radius: 999px;
                background: linear-gradient(90deg, rgba(13, 110, 253, 0.95), rgba(124, 58, 237, 0.85));
            }
            
            .sidebar-nav {
                padding: 0.5rem 1rem;
            }

            .list-group-item {
                padding: 0.9rem 1.25rem;
                border: none;
                font-weight: 500;
                color: rgba(255,255,255,0.78);
                background: transparent;
                display: flex;
                align-items: center;
                gap: 15px;
                border-radius: 12px;
                margin-bottom: 5px;
                transition: all 0.2s ease;
            }
            .list-group-item i {
                font-size: 1.25rem;
                color: rgba(255,255,255,0.86);
                transition: transform 0.2s ease;
            }
            .list-group-item:hover {
                background-color: rgba(255,255,255,0.10);
                color: rgba(255,255,255,0.95);
                transform: translateX(5px);
            }
            .list-group-item:hover i {
                color: rgba(255,255,255,0.95);
            }
            .list-group-item.active {
                background: linear-gradient(135deg, var(--mci-blue) 0%, var(--mci-accent) 100%) !important;
                color: white !important;
                box-shadow: 0 10px 20px rgba(13, 110, 253, 0.18), 0 8px 18px rgba(124, 58, 237, 0.12);
            }
            .list-group-item.active i {
                color: #fff;
            }

            #sidebar-wrapper .text-muted {
                color: rgba(255,255,255,0.65) !important;
            }
            #sidebar-wrapper .text-primary {
                color: rgba(255,255,255,0.95) !important;
            }
            #sidebar-wrapper .dropdown-toggle {
                color: rgba(255,255,255,0.95) !important;
            }
            
            .top-navbar {
                background: rgba(255, 255, 255, 0.8);
                background-image: linear-gradient(90deg, rgba(13, 110, 253, 0.06), rgba(124, 58, 237, 0.04));
                backdrop-filter: blur(10px);
                border-bottom: 1px solid rgba(0,0,0,0.05);
                padding: 0.8rem 2rem;
                position: sticky;
                top: 0;
                z-index: 1020;
            }
            
            .card {
                border: none;
                border-radius: 16px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.03);
                background: rgba(255, 255, 255, 0.92);
                backdrop-filter: blur(10px);
            }
            
            .main-content {
                flex: 1;
                padding: 2rem;
                padding-bottom: 5rem; /* Space for fixed footer */
            }

            footer {
                position: fixed;
                bottom: 0;
                right: 0;
                width: calc(100% - var(--sidebar-width));
                z-index: 1020;
                transition: all 0.3s ease;
            }

            #wrapper.toggled footer {
                width: 100%;
            }

            @media (max-width: 991.98px) {
                footer {
                    width: 100%;
                }
            }

            /* Custom Scrollbar */
            ::-webkit-scrollbar {
                width: 6px;
            }
            ::-webkit-scrollbar-track {
                background: transparent;
            }
            ::-webkit-scrollbar-thumb {
                background: #dee2e6;
                border-radius: 10px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #adb5bd;
            }

            #sidebar-wrapper::-webkit-scrollbar-thumb {
                background: rgba(255,255,255,0.18);
            }
            #sidebar-wrapper::-webkit-scrollbar-thumb:hover {
                background: rgba(255,255,255,0.28);
            }
        </style>
    </head>
    <body>
        <div id="wrapper">
            <!-- Sidebar -->
            <div id="sidebar-wrapper">
                <div class="sidebar-heading">
                    <img src="{{ asset('img/logomci.png') }}" alt="Logo MCI" class="mb-3" style="width: 60px; height: 60px; object-fit: contain;">
                    <div class="fw-bold text-primary h6 mb-1 text-uppercase">{{ \App\Models\Setting::getValue('company_name', 'MCI SYSTEM') }}</div>
                    <div class="text-muted small px-3" style="font-size: 0.6rem; font-weight: 500;">Designing and Manufacture</div>
                </div>
                <div class="sidebar-nav">
                    <div class="text-uppercase text-muted small fw-bold mb-3 px-3" style="font-size: 0.65rem; letter-spacing: 1px;">Menu Utama</div>
                    
                    @if(auth()->user()?->isAdmin())
                        <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-grid-1x2-fill"></i> Dashboard
                        </a>
                        <a href="{{ route('purchase-orders.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-text-fill"></i> Data PO
                        </a>
                        <a href="{{ route('deliveries.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('deliveries.*') ? 'active' : '' }}">
                            <i class="bi bi-truck-flatbed"></i> Data Pengiriman
                        </a>
                        <a href="{{ route('invoices.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                            <i class="bi bi-receipt-cutoff"></i> Data Penagihan
                        </a>
                        <a href="{{ route('archives.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('archives.*') ? 'active' : '' }}">
                            <i class="bi bi-archive-fill"></i> Arsip Dokumen
                        </a>
                        
                        <div class="text-uppercase text-muted small fw-bold mt-4 mb-3 px-3" style="font-size: 0.65rem; letter-spacing: 1px;">Sistem</div>
                        <a href="{{ route('users.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill"></i> Manajemen User
                        </a>
                        <a href="{{ route('settings.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <i class="bi bi-gear-fill"></i> Pengaturan
                        </a>
                    @endif

                    @if(auth()->user()?->isManager())
                        <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') || (request()->routeIs('manager.dashboard') && !request()->has('insight')) ? 'active' : '' }}">
                            <i class="bi bi-grid-1x2-fill"></i> Dashboard
                        </a>
                        <a href="{{ route('purchase-orders.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('purchase-orders.*') || request()->routeIs('deliveries.*') || request()->routeIs('invoices.*') ? 'active' : '' }}">
                            <i class="bi bi-bar-chart-line-fill"></i> Monitoring Pesanan
                        </a>
                        <a href="{{ route('archives.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('archives.*') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan & Analisis
                        </a>
                        <a href="{{ route('manager.dashboard', ['insight' => 1]) }}" class="list-group-item list-group-item-action {{ request()->has('insight') ? 'active' : '' }}">
                            <i class="bi bi-chat-left-dots-fill"></i> Insight Bisnis
                        </a>
                    @endif
                </div>
            </div>

            <!-- Page Content -->
            <div id="page-content-wrapper">
                <nav class="navbar navbar-expand-lg top-navbar">
                    <div class="container-fluid">
                        <button class="btn btn-light rounded-circle shadow-sm me-3" id="menu-toggle" style="width: 40px; height: 40px;">
                            <i class="bi bi-list"></i>
                        </button>
                        
                        <div class="d-none d-md-block">
                            <h5 class="mb-0 fw-bold text-dark">@yield('title', 'Dashboard')</h5>
                        </div>

                        <div class="ms-auto d-flex align-items-center gap-3">
                            <div class="vr mx-2 d-none d-md-block" style="height: 20px; opacity: 0.1;"></div>
                            <div class="dropdown">
                                <a class="text-decoration-none dropdown-toggle text-dark fw-semibold d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                    <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-2" style="min-width: 200px; border-radius: 12px;">
                                    <li><div class="dropdown-header small text-uppercase fw-bold text-muted">Akun Saya</div></li>
                                    <li><a class="dropdown-item rounded-8 py-2" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Profil</a></li>
                                    <li><hr class="dropdown-divider opacity-50"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item rounded-8 py-2 text-danger"><i class="bi bi-box-arrow-right me-2"></i> Keluar</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <main class="main-content">
                    @isset($header)
                        <div class="mb-4">
                            {{ $header }}
                        </div>
                    @endisset

                    {{ $slot }}
                </main>

                <footer class="py-3 px-5 text-center text-muted small border-top bg-white">
                    &copy; 2025 {{ \App\Models\Setting::getValue('company_name', 'CV MIRSA CIPTA INDONESIA') }}. {{ \App\Models\Setting::getValue('company_slogan', 'Designing and manufacture for Jig, SPM and Mechanical component.') }}
                </footer>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            window.addEventListener('DOMContentLoaded', event => {
                const menuToggle = document.body.querySelector('#menu-toggle');
                if (menuToggle) {
                    menuToggle.addEventListener('click', event => {
                        event.preventDefault();
                        document.body.querySelector('#wrapper').classList.toggle('toggled');
                    });
                }

                // Global SweetAlert Delete Confirmation
                document.addEventListener('click', function(e) {
                    // Find the closest submit button inside a form that has 'confirm' in its onsubmit
                    const deleteBtn = e.target.closest('button[type="submit"]');
                    if (!deleteBtn) return;
                    
                    const form = deleteBtn.closest('form');
                    if (!form) return;
                    
                    const onsubmitAttr = form.getAttribute('onsubmit');
                    if (onsubmitAttr && onsubmitAttr.includes('confirm')) {
                        e.preventDefault();
                        
                        // Extract message from confirm('...')
                        let message = 'Apakah Anda yakin ingin menghapus data ini?';
                        const match = onsubmitAttr.match(/confirm\(['"](.+?)['"]\)/);
                        if (match && match[1]) {
                            message = match[1];
                        }
                        
                        Swal.fire({
                            title: 'Konfirmasi Hapus',
                            text: message,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, Hapus!',
                            cancelButtonText: 'Batal',
                            reverseButtons: true,
                            customClass: {
                                confirmButton: 'rounded-pill px-4',
                                cancelButton: 'rounded-pill px-4'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Important: Remove the onsubmit attribute to avoid recursion
                                form.removeAttribute('onsubmit');
                                form.submit();
                            }
                        });
                    }
                });

                const toastSuccess = @json(session('success'));
                if (toastSuccess) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: toastSuccess,
                        showConfirmButton: false,
                        timer: 2200,
                        timerProgressBar: true,
                    });
                }

                const toastError = @json(session('error'));
                if (toastError) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: toastError,
                        showConfirmButton: false,
                        timer: 2600,
                        timerProgressBar: true,
                    });
                }
            });
        </script>
    </body>
</html>
