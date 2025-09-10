@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Información de Contacto')

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
    @vite('vendor/koneko/laravel-koneko-website-admin/resources/js/contact-info-card-card.js')
@endpush

@section('content')
    <div class="row">
        <div class="col-md-6">
            @livewire('koneko-website-admin::contact-info-card')
        </div>
        <div class="col-md-6">
            @livewire('koneko-website-admin::contact-location-card')
        </div>
    </div>
@endsection
