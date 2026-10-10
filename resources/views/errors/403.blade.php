@extends('errors.layout')

@section('title', __('Akses Ditolak'))
@section('code', '403')
@section('message', __('Anda tidak memiliki izin (hak akses) untuk membuka halaman ini.'))
@section('image')
<div class="w-24 h-24 bg-orange-50 rounded-full flex items-center justify-center mb-6">
    <svg class="w-12 h-12 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
    </svg>
</div>
@endsection
