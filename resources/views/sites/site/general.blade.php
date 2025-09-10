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

<div class="row">
    <div class="col-xl-6 col-md-6 col-sm-6 col-xs-12">
        @livewire('koneko-website-admin::site.description-card', ['site' => $site])
    </div>
    <div class="col-xl-6 col-md-6 col-sm-6 col-xs-12">
        @livewire('koneko-website-admin::site.visibility-security-card', ['site' => $site])
    </div>
</div>
