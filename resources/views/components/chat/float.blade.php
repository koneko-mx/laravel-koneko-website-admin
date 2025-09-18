@props([
  'chat'      => $_chat ?? [],
  // Overrides por template/uso puntual:
  'position'  => null,    // 'left'|'right'
  'offsetY'   => null,    // p.ej. '64px'
  'offsetX'   => null,    // p.ej. '24px'
  'offsetYSm' => null,    // p.ej. '20px' (solo móvil)
  'offsetXSm' => null,    // p.ej. '14px' (solo móvil)
  'z'         => null,    // p.ej. 1045
  'icon'      => 'fa',    // 'fa' | 'ti'  (evita doble ícono)
  'class'     => '',
])

@php
  $provider = $chat['provider'] ?? 'none';
  $cfg      = (array)($chat['config'] ?? []);
  $posKey   = $position ?: ($cfg['wa_position'] ?? $cfg['chatwoot_position'] ?? 'right');
  $posClass = ($posKey === 'left') ? 'is-left' : 'is-right';

  // Vars CSS (con fallback)
  $theme    = $cfg['wa_theme'] ?? $cfg['chatwoot_brand_color'] ?? '#25D366';
  $bottom   = $offsetY ?: ($cfg['wa_offset_bottom'] ?? null);
  $side     = $offsetX ?: null;
  $bottomSm = $offsetYSm ?: null; // móvil
  $sideSm   = $offsetXSm ?: null; // móvil
  $zIndex   = $z ?: null;

  $style = [];
  $style[] = "--kna-chat-bg: {$theme}";
  if ($bottom)  $style[] = "--kna-chat-bottom: {$bottom}";
  if ($side)    $style[] = "--kna-chat-side: {$side}";
  if ($bottomSm)$style[] = "--kna-chat-bottom-sm: {$bottomSm}";
  if ($sideSm)  $style[] = "--kna-chat-side-sm: {$sideSm}";
  if ($zIndex)  $style[] = "--kna-chat-z: {$zIndex}";
  $styleAttr = $style ? 'style="'.implode(';', $style).'"' : '';

  $label = $cfg['wa_button_text'] ?? null;
@endphp

@if($provider === 'whatsapp' && !empty($cfg['wa_url']))
  <a
    class="kna-chat-float kna-chat-float--wa {{ $posClass }} {{ $label ? 'has-label' : '' }} {{ $class }}"
    href="{{ $cfg['wa_url'] }}" target="_blank" rel="noopener"
    aria-label="WhatsApp" {!! $styleAttr !!}
  >
    @if($icon === 'ti')
      <i class="ti ti-brand-whatsapp" aria-hidden="true"></i>
    @else
      <i class="fab fa-whatsapp" aria-hidden="true"></i>
    @endif

    @if($label)
      <span class="kna-chat-float__label d-none d-lg-inline">{{ $label }}</span>
    @endif
  </a>
@endif
