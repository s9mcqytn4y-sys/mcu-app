@extends('errors.layout')

@section('title', __('Autentikasi Diperlukan'))
@section('code', '401')
@section('message', __('Silakan masuk terlebih dahulu dengan kredensial Anda untuk mengakses halaman ini.'))
@section('image')
<div class="w-24 h-24 bg-amber-50 rounded-full flex items-center justify-center mb-6" aria-hidden="true">
    <svg class="w-12 h-12 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
    </svg>
</div>
@endsection
