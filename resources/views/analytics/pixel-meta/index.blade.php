@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Pixel Meta')

@section('content')
    <div class="row">
        <div class="col-md-6">
            @livewire('koneko-website-admin::pixel-meta-card')
        </div>
    </div>
@endsection
