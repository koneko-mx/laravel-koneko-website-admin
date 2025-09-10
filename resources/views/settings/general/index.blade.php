@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Ajustes Generales')

@push('page-script')
    @vite('vendor/koneko/laravel-koneko-vuexy-admin/resources/js/pages/admin-settings-scripts.js')
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-5">
            @livewire('koneko-website-admin::website-description-card')
            @livewire('koneko-website-admin::website-favicon-card')
        </div>
        <div class="col-lg-4">
            @livewire('koneko-website-admin::logo-on-light-bg-card')
            @livewire('koneko-website-admin::logo-on-dark-bg-card')
        </div>
    </div>
@endsection
