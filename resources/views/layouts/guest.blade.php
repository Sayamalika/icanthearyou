<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - I Can't Hear You!</title>
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.dataset.theme = 'dark';
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app-style.css') }}">
</head>
<body>
    <button
        type="button"
        class="theme-toggle guest-theme-toggle"
        id="theme-toggle"
        aria-label="Aktifkan mode gelap"
        aria-pressed="false"
    >
        Gelap
    </button>

    <div class="auth-wrap">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="brand-logo">
                    <svg class="ico" viewBox="0 0 24 24"><path d="M2 12h2M6 8v8M10 4v16M14 7v10M18 9v6M22 12h-2"/></svg>
                </div>
                <div>
                    <div class="brand-name">I Can't Hear You!</div>
                    <div class="brand-sub">Music Store</div>
                </div>
            </div>

            <h1 class="auth-title">@yield('judul')</h1>
            <p class="auth-desc">@yield('deskripsi')</p>

            @include('layouts.alerts')

            @yield('content')
        </div>
    </div>

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
