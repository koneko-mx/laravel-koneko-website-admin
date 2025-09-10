@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Google Translate')

@section('content')
    <div class="row">
        <div class="col-lg-5">
            @livewire('koneko-website-admin::google-tanslate-card')
        </div>
    </div>
@endsection
