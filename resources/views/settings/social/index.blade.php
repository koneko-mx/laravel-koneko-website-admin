@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Enlaces de redes sociales')

@section('vendor-style')
    @vite([
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/vendor/libs/@form-validation/form-validation.scss'
    ])
@endsection

@section('vendor-script')
    @vite([
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/vendor/libs/@form-validation/popular.js',
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/vendor/libs/@form-validation/bootstrap5.js',
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/vendor/libs/@form-validation/auto-focus.js',
    ])
@endsection

@push('page-script')
    @vite([
        //'vendor/koneko/laravel-koneko-website-admin/resources/js/website-settings-card.js'
        ])
@endpush

@section('content')
    @livewire('koneko-website-admin::social-card')
@endsection
