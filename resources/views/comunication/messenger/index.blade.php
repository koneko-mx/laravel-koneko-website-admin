@extends('vuexy-admin::layouts.vuexy.layoutMaster')

@section('title', 'Facebook Messenger')

@section('content')
    <div class="row">
        <div class="col-md-6">
            @livewire('koneko-website-admin::messenger-card')
        </div>
    </div>
@endsection
