<!DOCTYPE html>
<html lang="id" class="antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Setup & Konfigurasi Server - Klinik Indosehat 2003</title>
    <meta name="robots" content="noindex, follow">
    <link rel="icon" href="{{ asset('favicon.ico?v=2') }}" type="image/x-icon">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind / Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen flex flex-col justify-center items-center p-4">

    <div class="max-w-xl w-full bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-blue-700 to-indigo-800 p-6 text-white text-center flex flex-col items-center">
            <img src="{{ asset('logo.svg') }}" alt="Logo Klinik Indosehat 2003" class="w-16 h-16 object-contain mb-4 drop-shadow-md">
            <h1 class="text-2xl font-bold">Server Setup</h1>
            <p class="text-blue-100 text-sm mt-1">Klinik Indosehat 2003</p>
        </div>

        <!-- Body -->
        <div class="p-6 md:p-8">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-800">Status Proses</h2>
                @if($isSuccess)
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        BERHASIL
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        ADA KENDALA
                    </span>
                @endif
            </div>

            <ul class="space-y-4">
                @foreach($output as $item)
                    @php
                        $bgColor = match($item['status']) {
                            'success' => 'bg-green-50 border-green-200',
                            'warning' => 'bg-yellow-50 border-yellow-200',
                            'error' => 'bg-red-50 border-red-200',
                            default => 'bg-gray-50 border-gray-200',
                        };
                        $iconColor = match($item['status']) {
                            'success' => 'text-green-500',
                            'warning' => 'text-yellow-500',
                            'error' => 'text-red-500',
                            default => 'text-gray-500',
                        };
                    @endphp
                    <li class="flex items-start gap-4 p-4 rounded-xl border {{ $bgColor }}">
                        <div class="mt-0.5 shrink-0 {{ $iconColor }}">
                            @if($item['status'] === 'success')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            @elseif($item['status'] === 'warning')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            @endif
                        </div>
                        <p class="text-sm font-medium text-gray-800 leading-snug">{{ $item['message'] }}</p>
                    </li>
                @endforeach
            </ul>

            <div class="mt-8 space-y-3">
                <a href="/setup-hosting?migrate=true" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg focus:ring-4 focus:ring-indigo-100">
                    Jalankan Migrasi Database
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                </a>
                <a href="/admin" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg focus:ring-4 focus:ring-blue-100">
                    Masuk ke Dashboard
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </a>
            </div>
        </div>
    </div>
    
</body>
</html>
