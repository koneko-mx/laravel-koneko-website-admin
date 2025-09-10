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
    <div class="col-lg-10 col-md-12">
        @livewire('koneko-website-admin::site.contact-location-card', ['site' => $site])
    </div>
    <div class="col-lg-10 col-md-12">
        @livewire('koneko-website-admin::site.contact-branches-card', ['site' => $site])
    </div>
</div>
