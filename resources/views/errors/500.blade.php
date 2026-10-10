@extends('errors.layout')

@section('title', __('Kesalahan Server'))
@section('code', '500')
@section('message', __('Mohon maaf, sedang terjadi gangguan teknis pada server kami. Tim kami sedang menanganinya. Silakan coba beberapa saat lagi.'))
@section('image')
<div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mb-6">
    <svg class="w-12 h-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
    </svg>
</div>
@endsection
