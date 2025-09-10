@props([
    'site_id',
    'active' => 'general',
])

<div class="nav-align-top">
    <ul class="nav nav-pills flex-column flex-md-row flex-wrap mb-6 row-gap-2">
        <li class="nav-item"><a class="nav-link {{ $active == 'general' ? 'active' : '' }}" href="{{route('admin.website-admin.sites.site.index', $site_id)}}"><i class="ti ti-user-check ti-sm me-1_5"></i>General</a></li>
        <li class="nav-item"><a class="nav-link {{ $active == 'contact' ? 'active' : '' }}" href="{{route('admin.website-admin.sites.site.contact', $site_id)}}"><i class="ti ti-user-check ti-sm me-1_5"></i>Contacto</a></li>
        <li class="nav-item"><a class="nav-link {{ $active == 'template' ? 'active' : '' }}" href="{{route('admin.website-admin.sites.site.template', $site_id)}}"><i class="ti ti-user-check ti-sm me-1_5"></i>Plantilla</a></li>
        <li class="nav-item"><a class="nav-link {{ $active == 'seo' ? 'active' : '' }}" href="{{route('admin.website-admin.sites.site.seo', $site_id)}}"><i class="ti ti-user-check ti-sm me-1_5"></i>SEO</a></li>
        <li class="nav-item"><a class="nav-link {{ $active == 'social' ? 'active' : '' }}" href="{{route('admin.website-admin.sites.site.social', $site_id)}}"><i class="ti ti-user-check ti-sm me-1_5"></i>Social</a></li>
        <li class="nav-item"><a class="nav-link {{ $active == 'integrations' ? 'active' : '' }}" href="{{route('admin.website-admin.sites.site.integrations', $site_id)}}"><i class="ti ti-bookmark ti-sm me-1_5"></i>Integraciones</a></li>
        <li class="nav-item"><a class="nav-link {{ $active == 'menus' ? 'active' : '' }}" href="{{route('admin.website-admin.sites.site.menus', $site_id)}}"><i class="ti ti-bookmark ti-sm me-1_5"></i>Menús</a></li>
        <li class="nav-item"><a class="nav-link {{ $active == 'pages' ? 'active' : '' }}" href="{{route('admin.website-admin.sites.site.pages', $site_id)}}"><i class="ti ti-bookmark ti-sm me-1_5"></i>Páginas</a></li>
    </ul>
</div>
