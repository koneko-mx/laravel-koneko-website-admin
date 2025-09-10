@props([
    'site',
    // acepta string o enum; normalizamos a string
    'active' => 'general',
])

@php
    use Koneko\KonekoWebsiteAdmin\Application\Enums\Websites\WebsiteTab;

    // Normaliza el activo a Enum seguro
    $activeTab = WebsiteTab::tryFrom((string)$active) ?? WebsiteTab::General;

    // Lista final de tabs visibles (permite feature-flags/permisos)
    $tabs = WebsiteTab::forSidebar(auth()->user(), $site);
@endphp

<h5 class="mb-2">{{ $site->title }}</h5>

<div class="d-flex justify-content-between flex-column mb-4">
    <ul class="nav nav-align-left nav-pills flex-column">
        @foreach ($tabs as $tab)
            @php
                $isActive = $activeTab === $tab;
                $url = route('admin.website-admin.websites.manager.site', [$site, $tab->value]);
            @endphp
            <li class="nav-item mb-1">
                <a href="{{ $url }}" @class([
                        'nav-link',
                        'waves-effect', 'waves-light',
                        'active' => $isActive,
                    ])
                    aria-current="{{ $isActive ? 'page' : 'false' }}"
                    title="{{ $tab->label() }}">
                    <i class="ti ti-{{ $tab->icon() }} ti-sm me-1_5" aria-hidden="true"></i>
                    <span class="align-middle text-wrap">{{ $tab->label() }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</div>
