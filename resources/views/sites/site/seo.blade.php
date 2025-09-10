@section('vendor-style')
    @vite('vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/vendor/libs/@form-validation/form-validation.scss')
@endsection

@section('vendor-script')
    @vite([
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/vendor/libs/@form-validation/popular.js',
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/vendor/libs/@form-validation/bootstrap5.js',
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/vendor/libs/@form-validation/auto-focus.js',
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/js/forms/formCustomListener.js',
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/js/notifications/LivewireNotification.js',
    ])
@endsection

<x-vuexy-admin::media.image-processor-scripts />

<div class="row">
    <div class="col-xl-6 col-md-6 col-sm-6 col-xs-12">
        @livewire('koneko-website-admin::site.schema-org-card', ['scope' => 'site', 'scopeId' => $site->id])
    </div>
    <div class="col-xl-6 col-md-6 col-sm-6 col-xs-12">
        @livewire('koneko-website-admin::site.locale-card', ['scope' => 'site', 'scopeId' => $site->id])
    </div>
</div>
<div class="row">
    <div class="col-xl-6 col-lg-10 col-sm-12 col-xs-12">
        @livewire('koneko-website-admin::site.og-card', ['scope' => 'site', 'scopeId' => $site->id])
    </div>
    <div class="col-xl-6 col-lg-10 col-sm-12 col-xs-12">
        @livewire('koneko-website-admin::site.twitter-card', ['scope' => 'site', 'scopeId' => $site->id])
    </div>
</div>

