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
    @php
    /*
    echo " ----- social ----- ";
    dump($_social);

    echo " ----- contact ----- ";
    dump($_contact);


    echo " ----- seo ----- ";
    dump($_seo);

    echo " ----- layout ----- ";
    dump($_layout);

    echo " ----- brand ----- ";
    dump($_brand);


    echo " ----- blocks ----- "
    dump($_blocks);

    echo " ----- chat ----- ";
    dump($_chat);

    dump(config('koneko.media.favicon_sizes'))

        echo " ----- img ----- ";
        dump($_img);

    */
    @endphp

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
