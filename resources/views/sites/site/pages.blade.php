@section('vendor-style')
    @vite([
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/vendor/libs/select2/select2.scss',
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/vendor/libs/bootstrap-table/bootstrap-table.scss',
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/vendor/fonts/bootstrap-icons.scss',
    ])
@endsection

@section('vendor-script')
    @vite([
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/vendor/libs/select2/select2.js',
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/js/notifications/LivewireNotification.js',
    ])
@endsection

@push('page-script')
    @vite([
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/js/bootstrap-table/bootstrapTableManager.js',
        'vendor/koneko/laravel-koneko-vuexy-admin/resources/assets/js/forms/formConvasHelper.js',
    ])
@endpush

<div class="row">
    <div class="col-12">
        @livewire('koneko-website-admin::site.pages-table', ['site' => $site])
        @livewire('koneko-website-admin::site.pages-offcanvas-form', ['site' => $site])
    </div>
</div>
