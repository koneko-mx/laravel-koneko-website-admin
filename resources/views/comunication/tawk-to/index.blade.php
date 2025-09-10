@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Tawk-to Chat')

@section('content')
    <div class="row">
        <div class="col-md-6">
            @livewire('koneko-website-admin::tawk-to-card')
        </div>
    </div>
@endsection
