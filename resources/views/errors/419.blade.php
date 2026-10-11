@extends('errors.layout')

@section('title', __('Sesi Kadaluarsa'))
@section('code', '419')
@section('message', __('Sesi penjelajahan Anda telah berakhir demi keamanan data medis. Silakan muat ulang halaman atau login kembali.'))
@section('image')
<div class="w-24 h-24 bg-amber-50 rounded-full flex items-center justify-center mb-6" aria-hidden="true">
    <svg class="w-12 h-12 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
</div>
@endsection
