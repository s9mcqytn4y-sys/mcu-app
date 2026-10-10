@extends('errors.layout')

@section('title', __('Halaman Tidak Ditemukan'))
@section('code', '404')
@section('message', __('Maaf, halaman yang Anda cari tidak dapat ditemukan atau telah dipindahkan.'))
@section('image')
<div class="w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mb-6">
    <svg class="w-12 h-12 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
    </svg>
</div>
@endsection
