<div x-data="{ prov: @entangle('chat_provider') }">
  <x-vuexy-admin::form.card-form
    id="website-chat-settings-card"
    title="Chat del sitio"
    subtitle="Activa un chat y selecciona el proveedor"
    linkHref="https://analytics.google.com/analytics/web/"
    linkText="Abrir Google Analytics"
    showActions
  >
    {{-- Proveedor --}}
    <div class="mb-3">
      <label class="form-label">Proveedor</label>
      <div class="d-flex gap-3 flex-wrap">
        <label class="form-check"><input type="radio" class="form-check-input" value="none"     x-model="prov" @change="$wire.chat_provider = prov"> Ninguno</label>
        <label class="form-check"><input type="radio" class="form-check-input" value="whatsapp" x-model="prov" @change="$wire.chat_provider = prov"> WhatsApp</label>
        <label class="form-check"><input type="radio" class="form-check-input" value="crisp"    x-model="prov" @change="$wire.chat_provider = prov"> Crisp</label>
        <label class="form-check"><input type="radio" class="form-check-input" value="tawkto"   x-model="prov" @change="$wire.chat_provider = prov"> Tawk.to</label>
        <label class="form-check"><input type="radio" class="form-check-input" value="tidio"    x-model="prov" @change="$wire.chat_provider = prov"> Tidio</label>
        <label class="form-check"><input type="radio" class="form-check-input" value="livechat" x-model="prov" @change="$wire.chat_provider = prov"> LiveChat</label>
      </div>
      <small class="text-muted">Sólo un proveedor puede estar activo a la vez. Cambia cuando quieras: conservamos la configuración de cada proveedor.</small>
    </div>

    {{-- WhatsApp --}}
    <div x-show="prov === 'whatsapp'" x-cloak>
      <x-vuexy-admin::form.input model="wa_phone" id="wa_phone" label="Teléfono (E.164)" icon="ti ti-brand-whatsapp" placeholder="+525512345678" autocomplete="off" />
      <x-vuexy-admin::form.textarea model="wa_greeting" id="wa_greeting" label="Saludo / Mensaje inicial" rows="3" placeholder="Hola 👋, vengo de {site}. Estoy viendo “{title}”. ¿Podrías ayudarme?" />
      <x-vuexy-admin::form.input model="wa_button_text" id="wa_button_text" label="Texto del botón (opcional)" placeholder="Chatea por WhatsApp" />
      <div class="row g-3">
        <div class="col-sm-6">
          <label for="wa_position" class="form-label">Posición</label>
          <select id="wa_position" class="form-select" wire:model="wa_position">
            <option value="right">Derecha</option>
            <option value="left">Izquierda</option>
          </select>
        </div>
        <div class="col-sm-6">
          <x-vuexy-admin::form.input model="wa_theme" id="wa_theme" label="Color del botón (hex)" placeholder="#25D366" autocomplete="off" />
        </div>
      </div>
      <small class="text-muted d-block mt-2">Macros: <code>{site}</code>, <code>{title}</code>, <code>{url}</code>. Se aplican antes del URL-encode en el enlace <code>wa.me</code>.</small>
    </div>

    {{-- Crisp --}}
    <div x-show="prov === 'crisp'" x-cloak>
      <x-vuexy-admin::form.input model="crisp_website_id" id="crisp_website_id" label="CRISP_WEBSITE_ID (UUID)" icon="ti ti-brand-crunchbase" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" autocomplete="off" />
      <small class="text-muted">Usa el ID del sitio desde el panel de Crisp.</small>
    </div>

    {{-- Tawk.to --}}
    <div x-show="prov === 'tawkto'" x-cloak>
      <x-vuexy-admin::form.input model="tawk_property_id" id="tawk_property_id" label="Property ID" icon="ti ti-message-circle" placeholder="ej. 64a1bc2def34567890abcd12" autocomplete="off" />
      <x-vuexy-admin::form.input model="tawk_widget_id" id="tawk_widget_id" label="Widget ID" icon="ti ti-puzzle" placeholder="default" autocomplete="off" />
    </div>

    {{-- Tidio --}}
    <div x-show="prov === 'tidio'" x-cloak>
      <x-vuexy-admin::form.input model="tidio_public_key" id="tidio_public_key" label="Public Key" icon="ti ti-key" placeholder="ej. abcdefgh12345678..." autocomplete="off" />
    </div>

    {{-- LiveChat --}}
    <div x-show="prov === 'livechat'" x-cloak>
      <x-vuexy-admin::form.input model="livechat_license" id="livechat_license" label="License ID" icon="ti ti-id" placeholder="ej. 1234567" autocomplete="off" />
    </div>
  </x-vuexy-admin::form.card-form>
</div>

@push('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const phonePattern  = /^[+]?[1-9][0-9]{7,14}$/;
            const hexPattern    = /^#(?:[A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/;
            const uuidPattern   = /^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/;
            const tawkProp      = /^[A-Za-z0-9-]{10,64}$/;
            const tawkWidget    = /^(default|[A-Za-z0-9-]{4,32})$/;
            const tidioKey      = /^[A-Za-z0-9]{8,64}$/;
            const livechatLic   = /^\d{4,12}$/;

            window.ChatSettingsForm = new formCustomListener({
                formSelector:    '#website-chat-settings-card',
                buttonSelectors: ['.btn-save', '.btn-cancel'],
                dispatchOnSubmit: 'save',
                fieldsValidation: {
                    wa_phone: { validators: { callback: { message: 'Teléfono E.164, ej. +525512345678.', callback: (i) => {
                        const prov = document.querySelector('input[value="whatsapp"]')?.checked; if (!prov) return true; return phonePattern.test((i.value||'').trim()); } } } },
                    wa_theme: { validators: { callback: { message: 'Color inválido (#RGB o #RRGGBB).', callback: (i) => {
                        const prov = document.querySelector('input[value="whatsapp"]')?.checked; if (!prov || !i.value) return true; return hexPattern.test((i.value||'').trim()); } } } },
                    crisp_website_id: { validators: { callback: { message: 'UUID inválido.', callback: (i) => {
                        const prov = document.querySelector('input[value="crisp"]')?.checked; if (!prov) return true; return uuidPattern.test((i.value||'').trim()); } } } },
                    tawk_property_id: { validators: { callback: { message: 'Property ID inválido (10-64).', callback: (i) => {
                        const prov = document.querySelector('input[value="tawkto"]')?.checked; if (!prov) return true; return tawkProp.test((i.value||'').trim()); } } } },
                    tawk_widget_id: { validators: { callback: { message: 'Widget ID inválido ("default" o 4-32).', callback: (i) => {
                        const prov = document.querySelector('input[value="tawkto"]')?.checked; if (!prov) return true; return tawkWidget.test((i.value||'').trim()); } } } },
                    tidio_public_key: { validators: { callback: { message: 'Public Key inválida (8-64 alfanum).', callback: (i) => {
                        const prov = document.querySelector('input[value="tidio"]')?.checked; if (!prov) return true; return tidioKey.test((i.value||'').trim()); } } } },
                    livechat_license: { validators: { callback: { message: 'License ID inválido (4-12 dígitos).', callback: (i) => {
                        const prov = document.querySelector('input[value="livechat"]')?.checked; if (!prov) return true; return livechatLic.test((i.value||'').trim()); } } } },
                }
            });
        });
    </script>
@endpush
