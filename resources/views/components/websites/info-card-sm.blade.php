@props([
    'site',
])

<div class="card">
    <div class="card-body text-center">
        <div class="dropdown btn-pinned">
            <button type="button" class="btn btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow p-4 waves-effect waves-light" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical ti-md text-muted"></i></button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item waves-effect" href="javascript:void(0);">Share connection</a></li>
                <li><a class="dropdown-item waves-effect" href="javascript:void(0);">Block connection</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-danger waves-effect" href="javascript:void(0);">Delete</a></li>
            </ul>
        </div>
        <div class="mx-auto my-6">
            @if (!$site)
                <img src="http://127.0.0.1:8091/assets/img/avatars/12.png" alt="Avatar Image" class="inline-block w-px-100">
            @else
                <i class="ti ti-world text-7xl text-primary mt-1"></i>
            @endif
        </div>
        <h5 class="mb-1 card-title">{{ $site->title }}</h5>
        <div class="mb-4">{{ $site->domain }}</div>

        <div class="mb-4">
            <p class="text-base mb-0">{{ $site->layout ?? 'Ninguna' }}</p>
            <span class="text-muted">Plantilla</span>
        </div>
        <span class="badge bg-label-{{ $site->status->color() }}">{{ $site->status->label() }}</span>

        <div class="align-items-center justify-content-center my-6">
            @if ($site->theme_color)
                <span class="badge me-2 mb-2" style="background-color: {{ $site->theme_color }}">Theme {{ $site->theme_color }}</span>
            @endif
            @if ($site->www_redirect)
                <span class="badge me-2 mb-2 bg-label-secondary">www redirect</span>
            @endif
            @if ($site->prevent_indexed)
                <span class="badge me-2 mb-2 bg-label-danger">No indexado</span>
            @endif
        </div>

        <div class="d-flex align-items-center justify-content-around mb-6">
            <div>
                <h5 class="mb-0">{{ $site->contents()->count() }}</h5>
                <a href="javascript:;" class="btn btn-sm btn-label-primary align-items-center waves-effect">Contenidos</a>
            </div>
            <div>
                <h5 class="mb-0">{{ $site->menus()->count() }}</h5>
                <a href="javascript:;" class="btn btn-sm btn-label-primary align-items-center waves-effect">Menus</a>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-center">
            <a href="{{ route('admin.website-admin.websites.manager.site', [$site, 'general']) }}" class="btn btn-label-success me-3 waves-effect"><i class="ti-xs ti ti-edit me-2"></i>Editar</a>
            <a href="{{ $site->getFullDomainUrl() }}" class="btn btn-text-dark me-3 waves-effect waves-light">Visitar</a>
        </div>
    </div>
</div>
