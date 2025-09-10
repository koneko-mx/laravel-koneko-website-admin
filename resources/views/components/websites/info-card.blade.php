@props([
    'site_id'    => null,
    'btn_edit'   => false,
    'btn_delete' => true,
])

@php
    $site = \Koneko\KonekoWebsiteAdmin\Models\Websites\WebsiteSite::find($site_id);
@endphp

<div class="card mb-6">
    <div class="card-body pt-12">
        <div class="user-avatar-section">
            <div class=" d-flex align-items-center flex-column">
                <img class="img-fluid rounded mb-4" src="{{ asset('assets/img/avatars/1.png') }}" height="120" width="120" alt="avatar" />
                <div class="user-info text-center">
                    <h5>{{ $site->title }}</h5>
                    <span class="badge bg-label-{{ $site->status->color() }}">{{ $site->status->label() }}</span>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-around flex-wrap my-6 gap-0 gap-md-3 gap-lg-4">
            <div class="d-flex align-items-center me-5 gap-4">
                <div class="avatar">
                    <div class="avatar-initial bg-label-primary rounded">
                        <i class='ti ti-file ti-lg'></i>
                    </div>
                </div>
                <div>
                    <h5 class="mb-0">{{ $site->contents()->count() }}</h5>
                    <span>Contenidos</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-4">
                <div class="avatar">
                    <div class="avatar-initial bg-label-primary rounded">
                        <i class='ti ti-list ti-lg'></i>
                    </div>
                </div>
                <div>
                    <h5 class="mb-0">{{ $site->menus()->count() }}</h5>
                    <span>Menus</span>
                </div>
            </div>
        </div>
        <h5 class="pb-4 border-bottom mb-4">Details</h5>
        <div class="info-container">
            <ul class="list-unstyled mb-6">
                <li class="mb-2">
                    <span class="h6">Dominio:</span>
                    <span>{{ $site->domain }}</span>
                </li>
                <li class="mb-2">
                    <span class="h6">Plantilla:</span>
                    <span>{{ $site->template ? $site->template->layout : 'Ninguna' }}</span>
                </li>
            </ul>
            <div class="d-flex justify-content-center">
                @if ($btn_edit)
                    <a href="javascript:;" class="btn btn-primary me-4" data-bs-target="#editUser" data-bs-toggle="modal">Edit</a>
                @endif
                @if ($btn_delete)
                    <a href="javascript:;" class="btn btn-label-danger">Eliminar</a>
                @endif
            </div>
        </div>
    </div>
</div>
