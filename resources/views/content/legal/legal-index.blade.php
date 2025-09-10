@extends('admin::layouts.vuexy.layoutMaster')

@section('title', 'Avisos legales')

<!-- Vendor Styles -->
@section('vendor-style')
    @vite([
        'modules/Admin/Resources/assets/vendor/libs/quill/typography.scss',
        //'modules/Admin/Resources/assets/vendor/libs/quill/katex.scss',
        'modules/Admin/Resources/assets/vendor/libs/quill/editor.scss'
    ])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
    @vite([
        //'modules/Admin/Resources/assets/vendor/libs/quill/katex.js',
        'modules/Admin/Resources/assets/vendor/libs/quill/quill.js'
    ])
@endsection

@section('page-script')
    @vite([
        //'modules/Admin/Resources/js/website-settings/legal-settings-scripts.js'
        ])
@endsection

@section('content')
    @livewire('legal-index')
@endsection
