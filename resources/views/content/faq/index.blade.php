@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Preguntas Frecuentes')

@section('vendor-style')
    @vite([
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/vendor/libs/select2/select2.scss',
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/vendor/libs/bootstrap-table/bootstrap-table.scss',
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/vendor/fonts/bootstrap-icons.scss',
    ])
@endsection

@push('page-script')
    @vite([
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/vendor/libs/select2/select2.js',
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/js/bootstrap-table/bootstrapTableManager.js',
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/js/forms/formConvasHelper.js',
    ])
@endpush

@section('content')
    @livewire('koneko-website-admin::faq-index')
@endsection
