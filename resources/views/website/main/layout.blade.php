@php
    if (!function_exists('is_active_route')) {
        function is_active_route($path, $activeClass = 'active', $default = '')
        {
            return request()->is($path) ? $activeClass : $default;
        }
    }
@endphp

@extends("{$_layout['package']}::layouts.{$_layout['template']}.master")

@section('header')
    @foreach ($_blocks['header'] as $headerBlock)
        @if ($headerBlock['type'] === 'blade-file')
            @include($headerBlock['path'])
        @endif
    @endforeach
@endsection

@section('content')
    @foreach ($_blocks['content'] as $contentBlock)
        @if ($contentBlock['type'] === 'blade-file')
            @include($contentBlock['path'])
        @endif
    @endforeach
@endsection

@section('footer')
    @foreach ($_blocks['footer'] as $footerBlock)
        @if ($footerBlock['type'] === 'blade-file')
            @include($footerBlock['path'])
        @endif
    @endforeach
@endsection
