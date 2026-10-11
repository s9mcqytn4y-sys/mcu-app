<!DOCTYPE html>
<html lang="id" class="antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Klinik Indosehat 2003</title>
    
    <!-- Meta SEO & Security -->
    <meta name="description" content="Klinik Indosehat 2003 - Layanan Medical Check Up Terpercaya">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#2563EB">
    <meta property="og:title" content="Klinik Indosehat 2003">
    <meta property="og:description" content="Portal Verifikasi Rekam Medis Klinik Indosehat 2003">
    <meta property="og:type" content="website">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts / Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <!-- Medical Cross Icon -->
                <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 10.5h-5.5V5a1.5 1.5 0 0 0-3 0v5.5H5a1.5 1.5 0 0 0 0 3h5.5V19a1.5 1.5 0 0 0 3 0v-5.5H19a1.5 1.5 0 0 0 0-3z"/>
                </svg>
                <span class="font-bold text-xl text-gray-900 tracking-tight">Klinik Indosehat <span class="text-blue-600">2003</span></span>
            </div>
            <nav aria-label="Main Navigation">
                <a href="/" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">Beranda</a>
            </nav>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <section class="max-w-md w-full space-y-8 text-center bg-white p-10 rounded-2xl shadow-xl border border-gray-100">
            <div class="flex justify-center">
                @yield('image')
            </div>
            
            <div class="space-y-4">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">@yield('code')</h1>
                <h2 class="text-xl font-semibold text-gray-700">@yield('title')</h2>
                <p class="text-gray-500">@yield('message')</p>
            </div>

            <div class="pt-6">
                <a href="/" class="inline-flex items-center justify-center min-h-11 px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-600 transition-all shadow-md hover:shadow-lg active:scale-[0.98]">
                    Kembali ke Beranda
                </a>
            </div>
        </section>
    </main>

    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm text-gray-500 text-center md:text-left">
                &copy; {{ date('Y') }} Klinik Indosehat 2003. Hak Cipta Dilindungi.
            </p>
            <div class="flex space-x-6">
                <a href="#" class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Bantuan</span>
                    Bantuan
                </a>
                <a href="#" class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Kontak</span>
                    Hubungi Kami
                </a>
            </div>
        </div>
    </footer>
</body>
</html>
