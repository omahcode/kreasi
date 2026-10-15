<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Masuk') - KREASI RPL (PjBL & PKL Digital)</title>
    <!-- PWA Meta Tags -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#064A8E">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="KREASI RPL">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/thumbs/panda.png') }}">
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/thumbs/panda.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/thumbs/panda.png') }}">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- File upload -->
    <link rel="stylesheet" href="{{ asset('assets/css/file-upload.css') }}">
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <!-- KREASI RPL Custom Branding -->
    <link rel="stylesheet" href="{{ asset('assets/css/kreasi-custom.css') }}">
    @livewireStyles
    @stack('styles')
</head> 
<body>
    
    <!-- Preloader Start -->
    <div class="preloader">
        <div class="loader"></div>
    </div>
    <!-- Preloader End -->

    <!-- Sidebar Overlay -->
    <div class="side-overlay"></div>

    <section class="auth d-flex">
        <!-- Auth Left: Edmate Illustration Banner -->
        <div class="auth-left bg-main-50 flex-center p-24 d-none d-lg-flex flex-column text-center">
            <div class="mb-24 flex-center">
                <img src="{{ asset('assets/images/thumbs/panda.png') }}" alt="KREASI RPL Mascot" class="img-fluid" style="max-height: 390px; object-fit: contain;">
            </div>
            <div class="px-24">
                <h3 class="text-main-600 mb-8 fw-bold">KREASI RPL</h3>
                <p class="text-gray-700 text-15 mb-0" style="max-width: 480px; margin: 0 auto;">
                    Karya RPL Edukasi & Aplikasi Siswa Inovatif. Wadah digital Project Based Learning (PjBL) & Bimbingan Laporan PKL Kelas XII.
                </p>
            </div>
        </div>

        <!-- Auth Right: Content Form -->
        <div class="auth-right py-40 px-24 flex-center flex-column">
            <div class="auth-right__inner mx-auto w-100">
                <div class="text-center mb-24">
                    <a href="{{ url('/') }}" class="auth-right__logo d-inline-block">
                        <img src="{{ asset('assets/images/logo/kreasi-logo.png') }}" alt="KREASI RPL Logo" class="kreasi-logo-img">
                    </a>
                </div>

                @yield('content')
            </div>
        </div>
    </section>

    <!-- jQuery js -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <!-- Bootstrap Bundle Js -->
    <script src="{{ asset('assets/js/boostrap.bundle.min.js') }}"></script>
    <!-- Phosphor Js -->
    <script src="{{ asset('assets/js/phosphor-icon.js') }}"></script>
    <!-- Main js -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    @livewireScripts
    @stack('scripts')
    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(reg) {
                        console.log('KREASI RPL Auth ServiceWorker ready: ', reg.scope);
                    })
                    .catch(function(err) {
                        console.log('KREASI RPL ServiceWorker error: ', err);
                    });
            });
        }
    </script>
</body>
</html>
