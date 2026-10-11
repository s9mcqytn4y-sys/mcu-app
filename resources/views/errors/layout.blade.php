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
<body class="bg-[#090d16] text-slate-100 flex flex-col min-h-screen">
    <header class="bg-[#0f172a]/90 backdrop-blur-md shadow-sm border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <!-- Medical Cross Icon -->
                <svg class="w-8 h-8 text-sky-400" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M19 10.5h-5.5V5a1.5 1.5 0 0 0-3 0v5.5H5a1.5 1.5 0 0 0 0 3h5.5V19a1.5 1.5 0 0 0 3 0v-5.5H19a1.5 1.5 0 0 0 0-3z"/>
                </svg>
                <span class="font-bold text-xl text-white tracking-tight">Klinik Indosehat <span class="text-sky-400">2003</span></span>
            </div>
            <nav aria-label="Main Navigation">
                <a href="/" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">Beranda</a>
            </nav>
        </div>
    </header>

    <main class="grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <section class="max-w-md w-full space-y-8 text-center bg-[#131d31] p-10 rounded-2xl shadow-2xl border border-slate-800">
            <div class="flex justify-center">
                @yield('image')
            </div>
            
            <div class="space-y-4">
                <h1 class="text-4xl font-extrabold text-white tracking-tight">@yield('code')</h1>
                <h2 class="text-xl font-semibold text-slate-200">@yield('title')</h2>
                <p class="text-slate-400">@yield('message')</p>
            </div>

            <div class="pt-6">
                <a href="/" class="inline-flex items-center justify-center min-h-11 px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-sky-600 hover:bg-sky-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-sky-500 transition-all shadow-md hover:shadow-lg active:scale-[0.98]">
                    Kembali ke Beranda
                </a>
            </div>
        </section>
    </main>

    <footer class="bg-[#0f172a] border-t border-slate-800 mt-auto">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm text-slate-400 text-center md:text-left">
                &copy; {{ date('Y') }} Klinik Indosehat 2003. Hak Cipta Dilindungi.
            </p>
            <div class="flex space-x-6">
                <a href="mailto:info@indosehat2003.com" class="text-sm text-slate-400 hover:text-slate-200 transition-colors">
                    Bantuan & Dukungan
                </a>
                <a href="/" class="text-sm text-slate-400 hover:text-slate-200 transition-colors">
                    Beranda
                </a>
            </div>
        </div>
    </footer>
</body>
</html>
