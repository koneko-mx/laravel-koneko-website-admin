@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Galería de Imágenes')

@push('page-script')
    @vite('vendor/koneko/laravel-koneko-vuexy-admin/resources/js/pages/admin-settings-scripts.js')
@endpush

@section('content')
    @livewire('koneko-website-admin::gallery-index')
@endsection
