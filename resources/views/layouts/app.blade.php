<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Sistem Manajemen Inventaris</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="{{ asset('css/app.css') }}?v={{ time() }}" rel="stylesheet">
    @stack('styles')
</head>

<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                @include('components.telkomsel-seeklogo')
                <span>Inventaris Telkomsel</span>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('products.index') }}"
                    class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i> Master Barang
                </a>
                @auth
                    @if (auth()->user()->hasRole(['admin', 'staff']))
                        <a href="{{ route('categories.index') }}"
                            class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            <i class="bi bi-tags"></i> Kategori
                        </a>
                    @endif
                @endauth
                <a href="{{ route('borrowings.index') }}"
                    class="nav-link {{ request()->routeIs('borrowings.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right"></i> Peminjaman
                </a>
                @auth
                    @if (auth()->user()->hasRole(['admin', 'manager']))
                        <div class="nav-section-title">Laporan</div>
                        <a href="{{ route('export.products.excel') }}" class="nav-link">
                            <i class="bi bi-file-earmark-excel"></i> Export Barang (Excel)
                        </a>
                        <a href="{{ route('export.products.pdf') }}" class="nav-link">
                            <i class="bi bi-file-earmark-pdf"></i> Export Barang (PDF)
                        </a>
                        <a href="{{ route('export.borrowings.pdf') }}" class="nav-link">
                            <i class="bi bi-file-earmark-pdf"></i> Export Peminjaman (PDF)
                        </a>
                    @endif
                @endauth
            </nav>
        </aside>

        <!-- Main content -->
        <div class="main-content">
            <header class="topbar">
                <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>

                <div class="ms-auto d-flex align-items-center gap-3">
                    <button class="btn btn-sm btn-outline-secondary" id="darkModeToggle" title="Mode Gelap">
                        <i class="bi bi-moon-stars" id="darkModeIcon"></i>
                    </button>

                    @auth
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2"
                                type="button" data-bs-toggle="dropdown">
                                <span class="avatar-circle">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                                <span
                                    class="badge text-bg-secondary text-uppercase">{{ auth()->user()->role->name ?? '-' }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endauth
                </div>
            </header>

            <main class="page-content">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Dark mode: preferensi disimpan di localStorage (murni sisi klien, aman untuk aplikasi web biasa)
        const htmlEl = document.documentElement;
        const darkModeIcon = document.getElementById('darkModeIcon');

        function applyTheme(theme) {
            htmlEl.setAttribute('data-bs-theme', theme);
            darkModeIcon.className = theme === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
        }

        applyTheme(localStorage.getItem('theme') || 'dark');

        document.getElementById('darkModeToggle').addEventListener('click', function() {
            const newTheme = htmlEl.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', newTheme);
            applyTheme(newTheme);
        });

        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
    </script>
    @stack('scripts')
</body>

</html>
