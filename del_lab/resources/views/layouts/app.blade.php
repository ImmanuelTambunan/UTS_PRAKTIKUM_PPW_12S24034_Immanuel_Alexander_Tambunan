<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'DEL-LEATS - Sistem Manajemen Aset & Inventaris Laboratorium')</title>
    
    <!-- Meta SEO Tags -->
    <meta name="description" content="Sistem Manajemen Aset dan Peminjaman Inventaris Laboratorium Terpadu Institut Teknologi Del (DEL-LEATS)">
    <meta name="author" content="Immanuel Alexander Tambunan - 12S24034">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- Heroicons / SVG support if needed -->
</head>
<body>

    <!-- Header Semantik HTML5 -->
    <header class="site-header">
        <div class="header-container">
            <a href="{{ route('equipments.index') }}" class="brand-wrapper">
                <div class="brand-logo-badge">DL</div>
                <div class="brand-text">
                    <h1>DEL-LEATS</h1>
                    <span>Lab Asset & Tracking</span>
                </div>
            </a>

            <!-- Navigasi Utama Semantik HTML5 -->
            <nav class="main-nav" aria-label="Navigasi Utama">
                <a href="{{ route('equipments.index') }}" class="nav-link {{ request()->routeIs('equipments.index') ? 'active' : '' }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Katalog Aset
                </a>
                <a href="{{ route('equipments.create') }}" class="nav-link {{ request()->routeIs('equipments.create') ? 'active' : '' }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Tambah Aset
                </a>
                <a href="{{ route('loans.index') }}" class="nav-link {{ request()->routeIs('loans.index') ? 'active' : '' }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    Riwayat Peminjaman
                </a>
            </nav>
        </div>
    </header>

    <!-- Main Content Semantik HTML5 -->
    <main class="site-main">
        <!-- Flash Session Alerts -->
        @if (session('success'))
            <aside class="alert-container" aria-label="Notifikasi Sukses">
                <div class="alert alert-success">
                    <span class="alert-icon">✓</span>
                    <div>{{ session('success') }}</div>
                </div>
            </aside>
        @endif

        @if (session('error'))
            <aside class="alert-container" aria-label="Notifikasi Galat">
                <div class="alert alert-danger">
                    <span class="alert-icon">⚠</span>
                    <div>{{ session('error') }}</div>
                </div>
            </aside>
        @endif

        @yield('content')
    </main>

    <!-- Footer Semantik HTML5 -->
    <footer class="site-footer">
        <div class="footer-container">
            <div>
                &copy; {{ date('Y') }} <strong>DEL-LEATS</strong> - Laboratorium Terpadu Institut Teknologi Del.
            </div>
            <div class="footer-author">
                Praktikum Pemrograman & Pengujian Web (12S3101) &bull; 12S24034 Immanuel Alexander Tambunan
            </div>
        </div>
    </footer>

</body>
</html>
