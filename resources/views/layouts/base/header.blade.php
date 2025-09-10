@php($_seo = $_seo ?? [])
@if (!empty($_seo['canonical']) && $_seo['canonical'] !== url()->current())
    <link rel="canonical" href="{{ $_seo['canonical'] }}" />
@endif

@if ($_seo['preload-fonts'] ?? false)
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" />
@endif

    {{-- hreflang alternates (multi-language SEO) --}}
@foreach ($_seo['hreflangs'] ?? [] as $lang => $url)
    <link rel="alternate" hreflang="{{ $lang }}" href="{{ $url }}" />
@endforeach

    {{-- Título + SEO Meta --}}
    <title>{{ $_seo['title'] ?? config('app.name') }}</title>
    <meta name="description" content="{{ $_seo['description'] ?? '' }}">
    <meta name="robots" content="{{ $_seo['robots'] ?? 'index, follow' }}">
@if (!empty($_seo['language']))
    <meta name="language" content="{{ $_seo['language'] }}">
@endif
@if (!empty($_seo['author']))
    <meta name="author" content="{{ $_seo['author'] }}">
@endif
@if (!empty($_seo['copyright']))
    <meta name="copyright" content="{{ $_seo['copyright'] }}">
@endif

    {{-- CSRF (Laravel) --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

@isset($_seo['og'])
    {{-- OpenGraph --}}
    <meta property="og:title" content="{{ $_seo['og']['title'] ?? $_seo['title'] ?? '' }}">
    <meta property="og:site_name" content="{{ $_seo['og']['site_name'] ?? config('app.name') }}">
    <meta property="og:url" content="{{ $_seo['og']['url'] ?? url()->current() }}">
    <meta property="og:description" content="{{ $_seo['og']['description'] ?? $_seo['description'] ?? '' }}">
    <meta property="og:type" content="{{ $_seo['og']['type'] ?? 'website' }}">
    <meta property="og:image" content="{{ $_seo['og']['image'] ?? '' }}">
@endisset

@isset($_seo['twitter'])
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="{{ $_seo['twitter']['card'] ?? 'summary_large_image' }}">
    <meta name="twitter:title" content="{{ $_seo['twitter']['title'] ?? $_seo['title'] ?? '' }}">
    <meta name="twitter:description" content="{{ $_seo['twitter']['description'] ?? $_seo['description'] ?? '' }}">
    <meta name="twitter:image" content="{{ $_seo['twitter']['image'] ?? $_seo['og']['image'] ?? '' }}">
    <meta name="twitter:site" content="{{ $_seo['twitter']['site'] ?? '@' }}">
    <meta name="twitter:creator" content="{{ $_seo['twitter']['creator'] ?? '@' }}">
@endisset

    {{-- Favicons dinámicos --}}
    <!-- Favicons-->
    <link rel="icon" href="{{ $_seo['favicon']['16x16'] }}" type="image/x-icon">
    <link rel="icon" sizes="192x192" href="{{ $_seo['favicon']['192x192'] }}">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="76x76" href="{{ $_seo['favicon']['76x76'] }}">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="120x120" href="{{ $_seo['favicon']['120x120'] }}">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="152x152" href="{{ $_seo['favicon']['152x152'] }}">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="180x180" href="{{ $_seo['favicon']['180x180'] }}">

    {{-- Web App Manifest --}}
@if (!empty($_seo['manifest']))
    <link rel="manifest" href="{{ $_seo['manifest'] }}">
@endif
@if (!empty($_seo['theme-color']))
    <meta name="theme-color" content="{{ $_seo['theme-color'] }}">
@endif

    {{-- JSON-LD Structured Data --}}
@if (!empty($_seo['ld+json']))
    <script type="application/ld+json">@json($_seo['ld+json'])</script>
@endif
