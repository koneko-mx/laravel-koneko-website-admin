@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Indexación')

@section('content')
    <div class="row">
        <div class="col-lg-5">
            @livewire('koneko-website-admin::indexing-card')
        </div>
    </div>
@endsection
