@props([
    'scope' => 'site',
    'model',              // p.ej. "schema_mode"
    'group',  // para IDs únicos y dataset
    'value' => null,      // valor actual: pásalo desde el padre
    'btnClass' => 'btn btn-outline-secondary',
    'size' => 'sm',
])

@php
    $uid = 'mode-'.$group;
    $name = $uid.'-name';

    $siteLabel    = $scope == 'site' ? 'Habilitar' : 'Heredado (Sitio)';
    $contentLabel = 'Personalizar';
    $disableLabel = 'Deshabilitar';

    $sizeClass = $size ? "btn-group-{$size}" : '';
@endphp

{{-- Radios SIN wire:model; los controla JS + delegación (wire:ignore opcional) --}}
<div id="{{ $uid }}-group" class="btn-group {{ $sizeClass }} mb-4"
     role="group" aria-label="Modo" data-hidden="#{{ $uid }}-hidden"
     {{-- opcional: el padre puede setear estos datasets para auto-toggle de UI --}}
     {{ $attributes }}
>
    <input class="btn-check" wire:model="{{ $model }}" type="radio" id="{{ $uid }}-site"  name="{{ $name }}" value="site" {{ $value === 'site' ?'checked' :'' }}>
    <label class="{{ $btnClass }}" for="{{ $uid }}-site">{{ $siteLabel }}</label>

    @if($scope === 'content')
        <input class="btn-check" wire:model="{{ $model }}" type="radio" id="{{ $uid }}-content" name="{{ $name }}" value="content" {{ $value === 'content' ?'checked' :'' }}>
        <label class="{{ $btnClass }}" for="{{ $uid }}-content">{{ $contentLabel }}</label>
    @endif

    <input class="btn-check" wire:model="{{ $model }}" type="radio" id="{{ $uid }}-disable" name="{{ $name }}" value="disable"  {{ $value === 'disable' ?'checked' :'' }}>
    <label class="{{ $btnClass }}" for="{{ $uid }}-disable">{{ $disableLabel }}</label>
</div>
