<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beranda') - I Can't Hear You!</title>
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.dataset.theme = 'dark';
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app-style.css') }}">
    @stack('styles')
</head>
<body>
@php
    // Menandai menu aktif berdasarkan alamat yang sedang dibuka
    $aktif = fn (string $pola) => request()->is($pola) ? 'active' : '';

    // Ikon garis sederhana (path SVG)
    $ikon = [
        'dasbor'    => '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>',
        'barang'    => '<path d="M21 8v13H3V8"/><path d="M1 3h22v5H1z"/><path d="M10 12h4"/>',
        'vendor'    => '<path d="M1 3h15v13H1z"/><path d="M16 8h4l3 3v5h-7z"/><circle cx="5.5" cy="18.5" r="2"/><circle cx="18.5" cy="18.5" r="2"/>',
        'margin'    => '<path d="M19 5L5 19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/>',
        'pengadaan' => '<path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 01-8 0"/>',
        'terima'    => '<path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.5 5.1L2 12v6a2 2 0 002 2h16a2 2 0 002-2v-6l-3.5-6.9A2 2 0 0016.8 4H7.2a2 2 0 00-1.7 1.1z"/>',
        'jual'      => '<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 002 1.6h9.7a2 2 0 002-1.6L23 6H6"/>',
        'user'      => '<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'role'      => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        'keluar'    => '<path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/>',
        'tanggal'   => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
        'musik'     => '<path d="M2 12h2M6 8v8M10 4v16M14 7v10M18 9v6M22 12h-2"/>',
    ];
    $svg = fn (string $nama) => '<svg class="ico" viewBox="0 0 24 24">' . $ikon[$nama] . '</svg>';

    $pengguna   = auth()->user();
    $idrole     = $pengguna->idrole ?? 0;
    $isAdministrator = $idrole === 1;
    $isCashier = $idrole === 2;
    $isWarehouse = $idrole === 3;
    $namaRole   = [1 => 'Administrator', 2 => 'Kasir', 3 => 'Staf Gudang'][$idrole] ?? 'Pengguna';
    $namaTampil = $pengguna->username ?? 'Tamu';
@endphp

<aside class="sidebar">
    <div class="brand">
        <div class="brand-logo">{!! $svg('musik') !!}</div>
        <div>
            <div class="brand-name">I Can't Hear You!</div>
            <div class="brand-sub">Music Store</div>
        </div>
    </div>

    <div class="nav-label">Operasional</div>
    <nav class="nav">
        <a href="/" class="{{ $aktif('/') }}">{!! $svg('dasbor') !!} Dashboard</a>
        <a href="/barang" class="{{ $aktif('barang*') }}">{!! $svg('barang') !!} Barang</a>
        <a href="/satuan" class="{{ $aktif('satuan*') }}">{!! $svg('barang') !!} Satuan</a>
        @if ($isAdministrator || $isWarehouse)
            <a href="/vendor" class="{{ $aktif('vendor*') }}">{!! $svg('vendor') !!} Vendor</a>
        @endif
        @if ($isAdministrator || $isCashier)
            <a href="/margin" class="{{ $aktif('margin*') }}">{!! $svg('margin') !!} Margin Penjualan</a>
        @endif
    </nav>

    <div class="nav-label">Transaksi</div>
    <nav class="nav">
        @if ($isAdministrator || $isWarehouse)
            <a href="/pengadaan" class="{{ $aktif('pengadaan*') }}">{!! $svg('pengadaan') !!} Pengadaan</a>
            <a href="/penerimaan" class="{{ $aktif('penerimaan*') }}">{!! $svg('terima') !!} Penerimaan</a>
        @endif
        @if ($isAdministrator || $isCashier)
            <a href="/penjualan" class="{{ $aktif('penjualan*') }}">{!! $svg('jual') !!} Penjualan</a>
        @endif
    </nav>

    @if ($isAdministrator)
        <div class="nav-label">Otoritas</div>
        <nav class="nav">
            <a href="/user" class="{{ $aktif('user*') }}">{!! $svg('user') !!} Pengguna</a>
            <a href="/role" class="{{ $aktif('role*') }}">{!! $svg('role') !!} Role</a>
        </nav>
    @endif

    <div class="sidebar-foot">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">{!! $svg('keluar') !!} Keluar</button>
        </form>
    </div>
</aside>

<div class="main">
    <header class="topbar">
        <div>
            <div class="topbar-role">{{ $namaRole }}</div>
            <div class="topbar-sub">Sistem Manajemen Inventaris Musik &amp; Audio</div>
        </div>
        <div class="topbar-right">
            <button
                type="button"
                class="theme-toggle"
                id="theme-toggle"
                aria-label="Aktifkan mode gelap"
                aria-pressed="false"
            >
                Gelap
            </button>
            <div class="chip-tanggal">{!! $svg('tanggal') !!} {{ now()->locale('id')->translatedFormat('l, j F Y') }}</div>
            <div class="avatar" title="{{ $namaTampil }}">{{ mb_substr($namaTampil, 0, 2) }}</div>
        </div>
    </header>

    <main class="content">
        <div class="page-head">
            <div>
                <h1 class="page-title">@yield('judul') @hasSection('tag')<span class="tag">@yield('tag')</span>@endif</h1>
                @hasSection('deskripsi')<p class="page-desc">@yield('deskripsi')</p>@endif
            </div>
            @hasSection('aksi')<div class="page-actions">@yield('aksi')</div>@endif
        </div>

        @include('layouts.alerts')

        @yield('content')
    </main>
</div>

@stack('scripts')
<script>
    const themeToggle = document.getElementById('theme-toggle');
    const rootElement = document.documentElement;

    function updateThemeToggle() {
        const isDark = rootElement.dataset.theme === 'dark';

        themeToggle.textContent = isDark ? 'Terang' : 'Gelap';
        themeToggle.setAttribute(
            'aria-label',
            isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap'
        );
        themeToggle.setAttribute('aria-pressed', String(isDark));
    }

    themeToggle.addEventListener('click', function () {
        const nextTheme = rootElement.dataset.theme === 'dark' ? 'light' : 'dark';

        if (nextTheme === 'dark') {
            rootElement.dataset.theme = 'dark';
        } else {
            rootElement.removeAttribute('data-theme');
        }

        localStorage.setItem('theme', nextTheme);
        updateThemeToggle();
    });

    updateThemeToggle();
</script>
</body>
</html>
