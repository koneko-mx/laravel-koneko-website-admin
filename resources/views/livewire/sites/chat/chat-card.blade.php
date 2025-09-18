<div x-data="chatSettingsCard()" x-init="init()" class="tw-space-y-4">
    <x-vuexy-admin::form.card-form
            id="website-chat-settings-card"
            title="Chat del sitio"
            subtitle="Activa un chat, elige proveedor y configura"
            linkText="Abrir panel del proveedor"
            :linkUrl="$this->provider_link['href'] ?? null"
            showActions
    >
        {{-- Filtros por grupo (píldoras) --}}
        <div class="d-flex flex-wrap gap-2 mb-2">
            <template x-for="f in filters" :key="f.id">
                <button type="button"
                                class="btn btn-sm"
                                :class="filterBtnClass(f.id)"
                                @click="tier = f.id">
                    <span x-text="f.label"></span>
                </button>
            </template>
        </div>

        {{-- Grid de proveedores (cards seleccionables) --}}
        <div class="row g-3">
            <template x-for="p in filteredProviders()" :key="p.id">
                <div class="col-6 col-md-4 col-lg-3">
                    <label class="card h-100 shadow-sm cursor-pointer tw-transition"
                        :class="prov===p.id ? 'border-primary' : 'border-0'">
                        <div class="card-body d-flex align-items-start gap-2">
                            <input type="radio"
                                class="form-check-input mt-1"
                                :value="p.id"
                                x-model="prov"
                                @change="$wire.chat_provider = $event.target.value" />
                            <div class="tw-flex tw-flex-col tw-gap-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="ti fs-4" :class="p.icon"></i>
                                    <span class="fw-semibold" x-text="p.label"></span>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <span class="badge" :class="p.badgeClass" x-text="p.badge"></span>
                                    <template x-for="b in p.tags" :key="b"><span class="badge bg-label-secondary" x-text="b"></span></template>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </template>
        </div>

        {{-- Ficha informativa + slider --}}
        <template x-if="prov && prov!=='none'">
            <div class="card mt-3 border-0 shadow-sm" x-cloak>
                <div class="card-body">
                    <div class="row g-4 align-items-stretch">

                        {{-- IZQ: Info --}}
                        <div class="col-12 col-lg-7 d-flex">
                            <div class="w-100 d-flex flex-column tw-gap-3">

                                {{-- Encabezado --}}
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                    <div class="d-flex gap-2 align-items-start">
                                        <i class="ti fs-3" :class="meta().icon"></i>
                                        <div>
                                            <h6 class="mb-1" x-text="meta().label"></h6>
                                            <div class="d-flex flex-wrap gap-2">
                                                <span class="badge" :class="meta().badgeClass" x-text="meta().badge"></span>
                                                <template x-for="t in meta().tags" :key="t"><span class="badge bg-label-secondary" x-text="t"></span></template>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a :href="meta().href" target="_blank" class="btn btn-sm btn-outline-primary" x-show="meta().href">
                                            <i class="ti ti-external-link me-1"></i><span x-text="meta().cta || 'Sitio / Panel'"></span>
                                        </a>
                                        <a :href="meta().docs" target="_blank" class="btn btn-sm btn-outline-secondary" x-show="meta().docs">
                                            <i class="ti ti-book me-1"></i>Docs
                                        </a>
                                    </div>
                                </div>

                                {{-- Descripción breve --}}
                                <p class="text-muted small mb-2" x-text="meta().desc"></p>

                                {{-- Indicadores rápidos --}}
                                <div class="row g-2 small mb-2">
                                    <div class="col-6">
                                        <div class="border rounded p-2 h-100">
                                            <div class="text-muted">Tipo</div>
                                            <div class="fw-semibold text-center" x-text="meta().tierLabel"></div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-2 h-100">
                                            <div class="text-muted">Tiempo de instalación</div>
                                            <div class="fw-semibold text-center" x-text="meta().eta"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Dificultad --}}
                                <div class="border rounded p-2 mb-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="text-muted">Dificultad</div>
                                        <div class="d-flex gap-1">
                                            <template x-for="i in 5" :key="i">
                                                <i class="ti" :class="i<=meta().difficulty ? 'ti-bolt-filled text-warning' : 'ti-bolt text-muted'"></i>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                {{-- Características --}}
                                <div class="d-flex flex-wrap gap-1 mb-2">
                                    <template x-for="f in meta().features" :key="f.text">
                                        <span class="badge bg-label-info">
                                            <i class="ti me-1" :class="f.icon"></i><span x-text="f.text"></span>
                                        </span>
                                    </template>
                                </div>

                                {{-- Pros / Contras --}}
                                <div class="row g-2 small mb-2">
                                    <div class="col-6">
                                        <div class="border rounded p-2 h-100">
                                            <div class="fw-semibold mb-1"><i class="ti ti-thumb-up text-success me-1"></i>Pros</div>
                                            <ul class="mb-0 ps-3">
                                                <template x-for="p in meta().pros" :key="p"><li x-text="p"></li></template>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-2 h-100">
                                            <div class="fw-semibold mb-1"><i class="ti ti-thumb-down text-danger me-1"></i>Contras</div>
                                            <ul class="mb-0 ps-3">
                                                <template x-for="c in meta().cons" :key="c"><li x-text="c"></li></template>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- CTA Self-Hosted --}}
                                <div class="alert alert-secondary d-flex align-items-center gap-2 m-0" x-show="meta().ctaSelf">
                                    <i class="ti ti-server"></i>
                                    <div>¿Quieres control total y datos propios? <strong>Lo hospedamos en Docker Swarm</strong>.</div>
                                    <button type="button" class="btn btn-sm btn-secondary" @click="$dispatch('open-quote', { product: meta().id })">
                                        Solicitar propuesta
                                    </button>
                                </div>

                            </div>
                        </div>

                        {{-- DER: Carrusel --}}
                        <div class="col-12 col-lg-5">
                            <div id="chatProviderCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner rounded border">
                                    <template x-for="(img, idx) in shots()" :key="idx">
                                        <div class="carousel-item ratio ratio-1x1" :class="{'active': idx===activeIdx}">
                                            <img :src="img" class="w-100 h-100" style="object-fit: cover;" alt="Preview">
                                        </div>
                                    </template>
                                </div>
                                <button class="carousel-control-prev" type="button" @click="prevSlide()">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" @click="nextSlide()">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            </div>

                            <div class="d-flex gap-2 mt-2">
                                <template x-for="(img, idx) in shots()" :key="'t'+idx">
                                    <button type="button"
                                                    class="border rounded ratio ratio-1x1 overflow-hidden"
                                                    :class="idx===activeIdx ? 'border-primary' : 'border-300'"
                                                    style="width:86px;"
                                                    @click="goSlide(idx)">
                                        <img :src="img" class="w-100 h-100" style="object-fit: cover;" alt="Thumb">
                                    </button>
                                </template>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </template>

        {{-- Formularios por proveedor --}}
        <div class="mt-3">

            {{-- WhatsApp --}}
            <div x-show="prov==='whatsapp'" x-cloak>
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
                        <label for="wa_theme" class="form-label">Color icono flotante</label>
                        <div class="input-group">
                            <input
                                id="wa_theme"
                                type="color"
                                class="form-control form-control-lg"
                                wire:model="wa_theme"
                                x-ref="waColor"
                                autocomplete="off"
                            />
                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                title="Restaurar color por defecto (#25D366)"
                                @click="resetWaColor()"
                                x-show="!isWaDefault()"
                                x-cloak
                            >
                                <i class="ti ti-restore"></i> Por defecto
                            </button>
                        </div>
                        <small class="text-muted d-block">
                            Actual: <code x-text="$wire.wa_theme || defaultWaColor"></code> · Predeterminado WhatsApp: <code x-text="defaultWaColor"></code>
                        </small>
                    </div>
                </div>

                <small class="text-muted d-block mt-2">
                    Macros: <code>{site}</code>, <code>{title}</code>, <code>{url}</code>. Se aplican antes del URL-encode en el enlace <code>wa.me</code>.
                </small>
            </div>


            {{-- Crisp --}}
            <div x-show="prov==='crisp'" x-cloak>
                <x-vuexy-admin::form.input model="crisp_website_id" id="crisp_website_id" label="CRISP_WEBSITE_ID (UUID)" icon="ti ti-brand-crunchbase" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" autocomplete="off" />
                <small class="text-muted">Usa el Website ID desde tu panel de Crisp.</small>
            </div>

            {{-- Tawk.to --}}
            <div x-show="prov==='tawkto'" x-cloak>
                <x-vuexy-admin::form.input model="tawk_property_id" id="tawk_property_id" label="Property ID" icon="ti ti-message-circle" placeholder="ej. 64a1bc2def34567890abcd12" autocomplete="off" />
                <x-vuexy-admin::form.input model="tawk_widget_id" id="tawk_widget_id" label="Widget ID" icon="ti ti-puzzle" placeholder="default" autocomplete="off" />
            </div>

            {{-- Tidio --}}
            <div x-show="prov==='tidio'" x-cloak>
                <x-vuexy-admin::form.input model="tidio_public_key" id="tidio_public_key" label="Public Key" icon="ti ti-key" placeholder="ej. abcdefgh12345678..." autocomplete="off" />
            </div>

            {{-- LiveChat --}}
            <div x-show="prov==='livechat'" x-cloak>
                <x-vuexy-admin::form.input model="livechat_license" id="livechat_license" label="License ID" icon="ti ti-id" placeholder="ej. 1234567" autocomplete="off" />
            </div>

            {{-- Intercom --}}
            <div x-show="prov==='intercom'" x-cloak>
                <x-vuexy-admin::form.input model="intercom_app_id" id="intercom_app_id" label="app_id" icon="ti ti-message-chatbot" placeholder="ej. abc12345" autocomplete="off" />
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label">Posición</label>
                        <select class="form-select" wire:model="intercom_alignment">
                            <option value="right">Derecha</option>
                            <option value="left">Izquierda</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Chatwoot --}}
            <div x-show="prov==='chatwoot'" x-cloak>
                <x-vuexy-admin::form.input model="chatwoot_base_url" id="chatwoot_base_url" label="Base URL" icon="ti ti-server" placeholder="https://chat.tu-dominio.com" autocomplete="off" />
                <x-vuexy-admin::form.input model="chatwoot_website_token" id="chatwoot_website_token" label="Website Token" icon="ti ti-code" placeholder="token..." autocomplete="off" />
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label">Posición</label>
                        <select class="form-select" wire:model="chatwoot_position">
                            <option value="right">Derecha</option>
                            <option value="left">Izquierda</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <x-vuexy-admin::form.input model="chatwoot_brand_color" id="chatwoot_brand_color" label="Color de marca (hex)" placeholder="#1F93FF" autocomplete="off" />
                    </div>
                </div>
            </div>

            {{-- Rocket.Chat Livechat --}}
            <div x-show="prov==='rocketchat'" x-cloak>
                <x-vuexy-admin::form.input model="rocketchat_base_url" id="rocketchat_base_url" label="Base URL (tu instancia)" icon="ti ti-server" placeholder="https://chat.tu-dominio.com" autocomplete="off" />
                <x-vuexy-admin::form.input model="rocketchat_department" id="rocketchat_department" label="Departamento (opcional)" icon="ti ti-building" placeholder="sales-support" autocomplete="off" />
            </div>

            {{-- Papercups --}}
            <div x-show="prov==='papercups'" x-cloak>
                <x-vuexy-admin::form.input model="papercups_base_url" id="papercups_base_url" label="Base URL" icon="ti ti-server" placeholder="https://app.papercups.io" autocomplete="off" />
                <x-vuexy-admin::form.input model="papercups_account_id" id="papercups_account_id" label="accountId (UUID)" icon="ti ti-id" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" autocomplete="off" />
            </div>

            {{-- Live Helper Chat --}}
            <div x-show="prov==='livehelperchat'" x-cloak>
                <x-vuexy-admin::form.input model="lhc_embed_script_url" id="lhc_embed_script_url" label="Embed Script URL" icon="ti ti-code" placeholder="https://lhc.tu-dominio.com/index.php/..." autocomplete="off" />
                <small class="text-muted">Copia la URL del JS desde <em>Embed code → Widget embed code (new)</em>.</small>
            </div>

        </div>

        <small class="text-muted d-block mt-3">
            Sólo un proveedor activo a la vez. Puedes cambiar cuando quieras: conservamos la configuración de cada proveedor.
        </small>

    </x-vuexy-admin::form.card-form>
</div>

@push('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const phoneE164 = /^[+]?[1-9][0-9]{7,14}$/;
            const cleanPhone = v => (v||"").replace(/[\s().-]/g, "");
            const hexPattern    = /^#(?:[A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/;
            const uuidPattern   = /^[0-9a-fA-F-]{36,}$/;
            const tawkProp      = /^[A-Za-z0-9-]{10,64}$/;
            const tawkWidget    = /^(default|[A-Za-z0-9-]{4,32})$/;
            const tidioKey      = /^[A-Za-z0-9]{8,64}$/;
            const livechatLic   = /^\d{4,12}$/;
            const urlPattern    = /^(https?):\/\/[^\s]+$/i;
            const intercomAppId = /^[a-z0-9_-]{5,32}$/;
            const chatwootToken = /^[A-Za-z0-9_-]{10,64}$/;
            const deptPattern   = /^[A-Za-z0-9_-]{0,64}$/;

            window.ChatSettingsForm = new formCustomListener({
                formSelector:    '#website-chat-settings-card',
                buttonSelectors: ['.btn-save', '.btn-cancel'],
                dispatchOnSubmit: 'save',
                fieldsValidation: {
                    wa_phone: { validators: { callback: { message: 'Teléfono válido (se permiten espacios, guiones y paréntesis).', callback: (i) => {
                        const on = document.querySelector('input[value="whatsapp"]')?.checked; if (!on) return true;
                        const cleaned = cleanPhone((i.value||'').trim());
                        return phoneE164.test(cleaned);
                    } } } },
                    wa_theme: { validators: { callback: { message: 'Color inválido (#RGB o #RRGGBB).', callback: (i) => {
                        const on = document.querySelector('input[value="whatsapp"]')?.checked; if (!on || !i.value) return true; return hexPattern.test((i.value||'').trim()); } } } },

                    crisp_website_id: { validators: { callback: { message: 'UUID inválido.', callback: (i) => {
                        const on = document.querySelector('input[value="crisp"]')?.checked; if (!on) return true; return uuidPattern.test((i.value||'').trim()); } } } },

                    tawk_property_id: { validators: { callback: { message: 'Property ID inválido (10-64).', callback: (i) => {
                        const on = document.querySelector('input[value="tawkto"]')?.checked; if (!on) return true; return tawkProp.test((i.value||'').trim()); } } } },
                    tawk_widget_id: { validators: { callback: { message: 'Widget ID inválido (\"default\" o 4-32).', callback: (i) => {
                        const on = document.querySelector('input[value="tawkto"]')?.checked; if (!on) return true; return tawkWidget.test((i.value||'').trim()); } } } },

                    tidio_public_key: { validators: { callback: { message: 'Public Key inválida (8-64).', callback: (i) => {
                        const on = document.querySelector('input[value="tidio"]')?.checked; if (!on) return true; return tidioKey.test((i.value||'').trim()); } } } },

                    livechat_license: { validators: { callback: { message: 'License ID inválido (4-12 dígitos).', callback: (i) => {
                        const on = document.querySelector('input[value="livechat"]')?.checked; if (!on) return true; return livechatLic.test((i.value||'').trim()); } } } },

                    intercom_app_id: { validators: { callback: { message: 'app_id inválido (5-32).', callback: (i) => {
                        const on = document.querySelector('input[value="intercom"]')?.checked; if (!on) return true; return intercomAppId.test((i.value||'').trim()); } } } },

                    chatwoot_base_url: { validators: { callback: { message: 'URL inválida (http/https).', callback: (i) => {
                        const on = document.querySelector('input[value="chatwoot"]')?.checked; if (!on) return true; return urlPattern.test((i.value||'').trim()); } } } },
                    chatwoot_website_token: { validators: { callback: { message: 'Website token inválido (10-64).', callback: (i) => {
                        const on = document.querySelector('input[value="chatwoot"]')?.checked; if (!on) return true; return chatwootToken.test((i.value||'').trim()); } } } },
                    chatwoot_brand_color: { validators: { callback: { message: 'Color inválido (#RGB o #RRGGBB).', callback: (i) => {
                        const on = document.querySelector('input[value="chatwoot"]')?.checked; if (!on || !i.value) return true; return hexPattern.test((i.value||'').trim()); } } } },

                    rocketchat_base_url: { validators: { callback: { message: 'URL inválida (http/https).', callback: (i) => {
                        const on = document.querySelector('input[value="rocketchat"]')?.checked; if (!on) return true; return urlPattern.test((i.value||'').trim()); } } } },
                    rocketchat_department: { validators: { callback: { message: 'Departamento inválido.', callback: (i) => {
                        const on = document.querySelector('input[value="rocketchat"]')?.checked; if (!on || !i.value) return true; return deptPattern.test((i.value||'').trim()); } } } },

                    papercups_base_url: { validators: { callback: { message: 'URL inválida (http/https).', callback: (i) => {
                        const on = document.querySelector('input[value="papercups"]')?.checked; if (!on) return true; return urlPattern.test((i.value||'').trim()); } } } },
                    papercups_account_id: { validators: { callback: { message: 'UUID inválido.', callback: (i) => {
                        const on = document.querySelector('input[value="papercups"]')?.checked; if (!on) return true; return uuidPattern.test((i.value||'').trim()); } } } },

                    lhc_embed_script_url: { validators: { callback: { message: 'URL inválida (http/https).', callback: (i) => {
                        const on = document.querySelector('input[value="livehelperchat"]')?.checked; if (!on) return true; return urlPattern.test((i.value||'').trim()); } } } },
                }
            });
        });

        function chatSettingsCard() {
            return {
                // Estado
                prov: @entangle('chat_provider').defer,
                tier: 'all',
                activeIdx: 0,

                waTheme: @entangle('wa_theme').defer,

                defaultWaColor: '#25D366',
                currentWa(){
                    // Lee siempre el DOM para tener el valor más fresco
                    const el = this.$refs.waColor || document.getElementById('wa_theme');
                    return (el?.value || '').trim() || this.defaultWaColor;
                },
                isWaDefault(){
                    return this.currentWa().toLowerCase() === this.defaultWaColor.toLowerCase();
                },
                resetWaColor(){
                    const el = this.$refs.waColor || document.getElementById('wa_theme');
                    if (!el) return;
                    // 1) Setea el DOM
                    el.value = this.defaultWaColor;
                    // 2) Dispara eventos para que:
                    el.dispatchEvent(new Event('input',  { bubbles: true }));
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                },

                // Filtros
                filters: [
                    { id: 'all',      label: 'Todos' },
                    { id: 'free',     label: 'Gratis' },
                    { id: 'freemium', label: 'Freemium' },
                    { id: 'saas',     label: 'SaaS' },
                    { id: 'self',     label: 'Self-Hosted' },
                ],
                filterBtnClass(id){
                    const base = 'btn-outline-secondary';
                    const map  = { free: 'btn-outline-success', freemium: 'btn-outline-primary', saas: 'btn-outline-danger', self: 'btn-outline-secondary', all: 'btn-outline-secondary' };
                    return (this.tier===id ? 'active ' : '') + (map[id] || base);
                },

                // Proveedores
                providers: [
                    { id: 'none',     label: 'Ninguno',  tier: 'all',      icon: 'ti-ban',              badge:'Off',         badgeClass:'bg-label-secondary', tags:['Deshabilitado'],  href:'',                              docs:'' },
                    { id: 'whatsapp', label: 'WhatsApp', tier: 'free',     icon: 'ti-brand-whatsapp',   badge:'Gratis',      badgeClass:'bg-label-success',   tags:['Click-to-Chat'],  href:'https://wa.me/',                docs:'https://faq.whatsapp.com' },
                    { id: 'crisp',    label: 'Crisp',    tier: 'freemium', icon: 'ti-brand-crunchbase', badge:'Freemium',    badgeClass:'bg-label-primary',   tags:['Inbox','KB'],     href:'https://crisp.chat',            docs:'https://help.crisp.chat' },
                    { id: 'tawkto',   label: 'Tawk.to',  tier: 'freemium', icon: 'ti-message-circle',   badge:'Gratis',      badgeClass:'bg-label-success',   tags:['Inbox'],          href:'https://www.tawk.to',           docs:'https://help.tawk.to' },
                    { id: 'tidio',    label: 'Tidio',    tier: 'freemium', icon: 'ti-robot',            badge:'Freemium',    badgeClass:'bg-label-primary',   tags:['Bots'],           href:'https://www.tidio.com',         docs:'https://help.tidio.com' },
                    { id: 'livechat', label: 'LiveChat', tier: 'saas',     icon: 'ti-headset',          badge:'De pago',     badgeClass:'bg-label-danger',    tags:['Suite'],          href:'https://www.livechat.com',      docs:'https://platform.text.com' },
                    { id: 'intercom', label: 'Intercom', tier: 'saas',     icon: 'ti-message-chatbot',  badge:'De pago',     badgeClass:'bg-label-danger',    tags:['Suite'],          href:'https://www.intercom.com',      docs:'https://developers.intercom.com' },
                    { id: 'chatwoot', label: 'Chatwoot', tier: 'self',     icon: 'ti-server',           badge:'Self-Hosted', badgeClass:'bg-label-secondary', tags:['Open-Source'],    href:'https://www.chatwoot.com',      docs:'https://www.chatwoot.com/hc' },
                    { id: 'rocketchat',label:'Rocket.Chat', tier:'self',   icon: 'ti-server-2',         badge:'Self-Hosted', badgeClass:'bg-label-secondary', tags:['Omnichannel'],    href:'https://www.rocket.chat',       docs:'https://docs.rocket.chat' },
                    { id: 'papercups',label:'Papercups', tier:'self',      icon: 'ti-messages',         badge:'Self-Hosted', badgeClass:'bg-label-secondary', tags:['Elixir'],         href:'https://papercups.io',          docs:'https://docs.papercups.io' },
                    { id: 'livehelperchat', label:'Live Helper Chat', tier:'self', icon:'ti-code',      badge:'Self-Hosted', badgeClass:'bg-label-secondary', tags:['Ligero'],         href:'https://livehelperchat.com',    docs:'https://doc.livehelperchat.com' },
                ],

                // Init & watches (compactos)
                init(){
                    this.$nextTick(() => {
                        if (!this.prov || this.prov === 'none') {
                            const v = this.$wire.chat_provider; if (v) this.prov = v;
                        }
                        this.syncTierWithProvider();
                        this.resetCarousel();

                        // Forzar un refresh visual de “Actual:” tras la primera hidratación
                        const el = this.$refs.waColor || document.getElementById('wa_theme');
                        if (el) {
                            // No dispara red; sólo UI
                            el.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    });

                    this.$watch('prov', () => { this.syncTierWithProvider(); this.resetCarousel(); });
                },

                resetCarousel(){
                    this.activeIdx = 0;
                    const el = document.getElementById('chatProviderCarousel');
                    if (!el) return;
                    const c = bootstrap.Carousel.getOrCreateInstance(el, { interval: 0, ride: false });
                    c.to(0);
                },

                // Lógica de filtros
                filteredProviders(){
                    return this.providers.filter(p => this.tier==='all' ? true : (this.tier==='self' ? p.tier==='self' : p.tier===this.tier));
                },
                syncTierWithProvider(){
                    const cur = this.providers.find(p => p.id === this.prov)?.tier; if (!cur) return;
                    const visible = (this.tier==='all') || (this.tier===cur) || (this.tier==='self' && cur==='self');
                    if (!visible) this.tier = 'all';
                },

                // Metadata compacta (sin "Requiere" ni "CSP")
                meta(){
                    const base = {
                        whatsapp: { label:'WhatsApp', tierLabel:'Gratis', desc:'Botón que abre conversación en WhatsApp. Sin panel.', badge:'Gratis', badgeClass:'bg-label-success', icon:'ti-brand-whatsapp', features:[{icon:'ti-click',text:'Click-to-Chat'}], eta:'~5–10 min', difficulty:1, pros:['Cero costo','Adopción inmediata'], cons:['Sin inbox compartida','Sin métricas'], ctaSelf:false },
                        crisp: { label:'Crisp', tierLabel:'Freemium (SaaS)', desc:'Widget moderno con inbox, KB y triggers.', badge:'Freemium', badgeClass:'bg-label-primary', icon:'ti-brand-crunchbase', features:[{icon:'ti-inbox',text:'Inbox'},{icon:'ti-book',text:'KB'},{icon:'ti-bolt',text:'Triggers'}], eta:'~15–30 min', difficulty:2, pros:['UI pulida','KB integrada'], cons:['Límites en plan gratis'], ctaSelf:false },
                        tawkto: { label:'Tawk.to', tierLabel:'Gratis (SaaS)', desc:'Gratis con funciones completas; extras de pago.', badge:'Gratis', badgeClass:'bg-label-success', icon:'ti-message-circle', features:[{icon:'ti-inbox',text:'Inbox'}], eta:'~10–20 min', difficulty:2, pros:['Costo $0','Instalación simple'], cons:['Branding por defecto'], ctaSelf:false },
                        tidio: { label:'Tidio', tierLabel:'Freemium (SaaS)', desc:'Chat + bots orientado a e-commerce.', badge:'Freemium', badgeClass:'bg-label-primary', icon:'ti-robot', features:[{icon:'ti-robot',text:'Bots'},{icon:'ti-activity',text:'Automatización'}], eta:'~15–30 min', difficulty:2, pros:['Bots incluidos','Rápido de integrar'], cons:['IA avanzada en pago'], ctaSelf:false },
                        livechat: { label:'LiveChat', tierLabel:'SaaS (pago)', desc:'Suite profesional enfocada en ventas/soporte.', badge:'De pago', badgeClass:'bg-label-danger', icon:'ti-headset', features:[{icon:'ti-route',text:'Routing'},{icon:'ti-chart-donut',text:'Analytics'}], eta:'~15–30 min', difficulty:2, pros:['Robusto','Ecosistema amplio'], cons:['Costo mensual'], ctaSelf:false },
                        intercom: { label:'Intercom', tierLabel:'SaaS (pago)', desc:'Messenger + automatización + base de clientes.', badge:'De pago', badgeClass:'bg-label-danger', icon:'ti-message-chatbot', features:[{icon:'ti-robot',text:'Automatización'},{icon:'ti-database',text:'CDP'}], eta:'~15–30 min', difficulty:2, pros:['Automatización potente','Segmentación'], cons:['Costo alto','Vendor lock-in'], ctaSelf:false },
                        chatwoot: { label:'Chatwoot', tierLabel:'Self-Hosted (Open-Source)', desc:'Omnicanal open-source. Perfecto para tu Swarm.', badge:'Self-Hosted', badgeClass:'bg-label-secondary', icon:'ti-server', features:[{icon:'ti-inbox',text:'Inbox'},{icon:'ti-brand-whatsapp',text:'WhatsApp API'},{icon:'ti-webhook',text:'Webhooks'}], eta:'~1–2 h', difficulty:3, pros:['Datos propios','API/Webhooks'], cons:['Operación/updates a cargo'], ctaSelf:true },
                        rocketchat: { label:'Rocket.Chat (Livechat)', tierLabel:'Self-Hosted (Open-Source)', desc:'Tu plataforma + widget Livechat.', badge:'Self-Hosted', badgeClass:'bg-label-secondary', icon:'ti-server-2', features:[{icon:'ti-building-community',text:'Omnichannel'},{icon:'ti-video',text:'Video/Apps'}], eta:'~1–2 h', difficulty:3, pros:['Muy integrable','Comunidad grande'], cons:['Más recursos'], ctaSelf:true },
                        papercups: { label:'Papercups', tierLabel:'Self-Hosted (Open-Source)', desc:'Ligero (Elixir). Widget + panel.', badge:'Self-Hosted', badgeClass:'bg-label-secondary', icon:'ti-messages', features:[{icon:'ti-inbox',text:'Inbox'},{icon:'ti-code',text:'Embebible'}], eta:'~45–90 min', difficulty:2, pros:['Ligero','MIT'], cons:['Menos features'], ctaSelf:true },
                        livehelperchat: { label:'Live Helper Chat', tierLabel:'Self-Hosted (Open-Source)', desc:'Muy liviano, PHP + MySQL.', badge:'Self-Hosted', badgeClass:'bg-label-secondary', icon:'ti-code', features:[{icon:'ti-bolt',text:'Ligero'},{icon:'ti-puzzle',text:'Widget flexible'}], eta:'~30–60 min', difficulty:2, pros:['Rápido','Hosting simple'], cons:['UI menos moderna'], ctaSelf:true },
                    };
                    const p = this.providers.find(x => x.id===this.prov) || {};
                    return { id:this.prov, ...base[this.prov], href:p.href, docs:p.docs, badge:p.badge || (base[this.prov]?.badge), badgeClass:p.badgeClass || (base[this.prov]?.badgeClass), tags:p.tags || [] };
                },

                // Imágenes
                shots(){
                    const base = `/vendor/koneko-vuexy-admin/chat/${this.prov}`;
                    const allow = ['whatsapp','crisp','tawkto','tidio','livechat','intercom','chatwoot','rocketchat','papercups','livehelperchat'];
                    return allow.includes(this.prov) ? [`${base}/cover.jpg`, `${base}/thumbnail-1.jpg`, `${base}/thumbnail-2.jpg`] : [];
                },

                // Carousel helpers
                goSlide(idx){ this.activeIdx = idx; const el = document.getElementById('chatProviderCarousel'); if(!el) return; const c = bootstrap.Carousel.getOrCreateInstance(el); c.to(idx); },
                prevSlide(){ const el = document.getElementById('chatProviderCarousel'); if(!el) return; const c = bootstrap.Carousel.getOrCreateInstance(el); this.activeIdx = Math.max(0, this.activeIdx-1); c.prev(); },
                nextSlide(){ const el = document.getElementById('chatProviderCarousel'); if(!el) return; const c = bootstrap.Carousel.getOrCreateInstance(el); this.activeIdx = Math.min(this.shots().length-1, this.activeIdx+1); c.next(); },
            }
        }
    </script>
@endpush
