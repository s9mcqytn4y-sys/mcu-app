@extends('errors.layout')

@section('title', __('Terlalu Banyak Permintaan'))
@section('code', '429')
@section('message', __('Sistem mendeteksi terlalu banyak permintaan dalam waktu singkat. Demi stabilitas dan perlindungan data, silakan tunggu beberapa saat sebelum mencoba kembali.'))
@section('image')
<div class="w-24 h-24 bg-rose-50 rounded-full flex items-center justify-center mb-6" aria-hidden="true">
    <svg class="w-12 h-12 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
    </svg>
</div>
@endsection
