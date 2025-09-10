<div>
    <x-vuexy-admin::form.card-form
        id="website-schemaorg-card"
        title="Schema.org (JSON-LD)"
        subtitle="Marcado estructurado para SEO"
        linkHref="{{ $richResultsUrl }}"
        linkText="Probar en Rich Results"
        showActions
    >
        {{-- Selector de modo --}}
        <x-koneko-website-admin::form.mode-toggle
            model="schema_mode"
            group="schema"
            :scope="$scope"
            :value="$schema_mode"
            {{-- el contenedor padre define dónde togglear --}}
            data-scope="#website-schemaorg-card"
            data-show-when-enable=".display-enabled"
            data-show-when-inherit=".display-inherited"
        />

        {{-- Aviso cuando está heredando (solo Content) --}}
        <div class="display-inherited">
            <div class="alert alert-secondary py-2 mb-3 small">
                Este contenido hereda el Schema del sitio. No hay campos editables aquí.
            </div>
        </div>

        {{-- Editor SOLO si está habilitado --}}
        <div class="display-enabled">
            <fieldset>
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-7">
                        <x-vuexy-admin::form.select
                            model="preset"
                            label="Plantillas"
                            :options="$presetOptions"
                            placeholder="Selecciona un preset" />
                    </div>
                    <div class="col-12 col-md-5">
                        <x-vuexy-admin::button.basic
                            variant="secondary" size="sm" icon="ti ti-file-plus"
                            class="mb-4 btn-insert-preset"
                            label="Insertar plantilla"
                            wire:click="applyPreset" />
                    </div>
                </div>

                <label for="schema_org_text" class="form-label mt-2">JSON-LD</label>
                <x-vuexy-admin::form.textarea
                    id="schema_org_text"
                    name="schema_org_text"
                    model="schema_org_text"
                    class="font-monospace"
                    style="min-height:260px"
                    spellcheck="false" autocapitalize="off" autocomplete="off" autocorrect="off"
                    :value="$schema_org_text" />

                <div class="mt-2 d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-schema-format">Formatear</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-schema-minify">Minificar</button>
                    <button type="button" class="btn btn-sm btn-outline-danger"    id="btn-schema-clear">Limpiar</button>
                </div>

                <div class="alert alert-info py-2 mt-2">
                    <div class="small">
                        Incluye <code>@context</code> y <code>@type</code> (o usa <code>@graph</code>). Usa URLs absolutas.
                    </div>
                </div>
            </fieldset>
        </div>
    </x-vuexy-admin::form.card-form>
</div>

@push('page-script')
    <script>
        (function () {
            /* ==============================
            * Utils
            * ============================== */
            const $  = (sel, root = document) => root.querySelector(sel);
            const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));




            const ModeToggle = {
                groupSelector: '[role="group"][data-scope]', // tu <div id="mode-*-group"...>
                _inited: new WeakSet(),

                _show(els, show) {
                    els.forEach(el => el.classList.toggle('d-none', !show));
                },

                _getState(group) {
                    const checked = $('input[type="radio"]:checked', group);
                    return checked ? checked.value : null;
                },

                _collectTargets(group) {
                    const scopeSel   = group.dataset.scope || 'body';
                    const container  = $(scopeSel) || document;

                    const selEnabled = group.dataset.showWhenEnable || '.display-enabled';
                    const selInherit = group.dataset.showWhenInherit || '.display-inherited';

                    const enabledEls = $$(selEnabled, container);
                    const inheritEls = $$(selInherit, container);

                    return { container, enabledEls, inheritEls };
                },

                _apply(group) {
                    const scope = (group.dataset.scopeType || group.getAttribute('data-scope-type') || group.dataset.scopeType) || // opcional si quieres distinguir
                                    (group.getAttribute('data-scope-kind') || '').trim() ||
                                    (group.getAttribute('data-scope') ? group.getAttribute('data-scope') : 'site'); // por compatibilidad
                    const value = this._getState(group);
                    const { enabledEls, inheritEls } = this._collectTargets(group);

                    // Normaliza scope a 'site' o 'content' (tu markup ya limita las opciones del radio)
                    const scopeKind = group.getAttribute('data-scope-kind') || group.getAttribute('data-scope-type') || (group.getAttribute('data-scope') === '#website-schemaorg-card' ? (group.dataset.scopeRole || 'site') : 'site');

                    // Reglas de visibilidad
                    // SITE:   site => enabled; disable => nada
                    // CONTENT: site => inherit; content => enabled; disable => nada
                    const hideBoth = () => { this._show(enabledEls, false); this._show(inheritEls, false); };

                    if ((scopeKind || '').includes('content')) {
                        if (value === 'site') {
                            // Hereda del sitio
                            this._show(enabledEls, false);
                            this._show(inheritEls, true);
                        } else if (value === 'content') {
                            this._show(inheritEls, false);
                            this._show(enabledEls, true);
                        } else { // disable
                            hideBoth();
                        }
                    } else { // scope = site
                        if (value === 'site') {
                            this._show(inheritEls, false);
                            this._show(enabledEls, true);
                        } else { // disable (o cualquier otro)
                            hideBoth();
                        }
                    }
                },

                _bind(group) {
                    if (this._inited.has(group)) return;

                    // Marca el scope lógico (site|content) si te es útil distinguir explícitamente
                    // Puedes pasar data-scope-kind="site" | "content" desde el componente Blade si prefieres.
                    if (!group.getAttribute('data-scope-kind')) {
                        // Heurística: si existe el radio "content" en el grupo, asumimos scope=content
                        const hasContentOption = !!$('input[type="radio"][value="content"]', group);
                        group.setAttribute('data-scope-kind', hasContentOption ? 'content' : 'site');
                    }

                    group.addEventListener('change', (ev) => {
                        if (ev.target && ev.target.matches('input[type="radio"]')) {
                            this._apply(group);
                        }
                    });

                    this._inited.add(group);
                    this._apply(group); // estado inicial
                },

                mountOnce(root = document) {
                    $$(this.groupSelector, root).forEach(g => this._bind(g));
                },

                mount() {
                    // DOM listo
                    if (document.readyState !== 'loading') this.mountOnce();
                    else document.addEventListener('DOMContentLoaded', () => this.mountOnce());

                    // Livewire
                    document.addEventListener('livewire:init', () => {
                        const re = () => this.mountOnce();
                        if (window.Livewire?.hook) {
                            Livewire.hook('morph.updated', re);
                            Livewire.hook('commit', re);
                            Livewire.hook('message.failed', re);
                        }
                    });
                }
            };





            /* =========================================================
            * SchemaEditor: acciones del JSON-LD + formCustomListener
            * ========================================================= */
            const SchemaEditor = {
                formId: '#website-schemaorg-card',

                enableActions(form) {
                    $$('.btn-save, .btn-cancel', form).forEach(btn => {
                        btn.disabled = false;
                        btn.classList.remove('disabled');
                    });
                    if (window.SchemaorgCard?.markAsDirty) window.SchemaorgCard.markAsDirty();
                    if (window.SchemaorgCard?.setDirty)    window.SchemaorgCard.setDirty(true);
                },

                notifyChange(form, ta) {
                    ta.dispatchEvent(new Event('input',  { bubbles: true }));
                    ta.dispatchEvent(new Event('change', { bubbles: true }));
                    form.dispatchEvent(new Event('change', { bubbles: true }));
                    this.enableActions(form);
                },

                bindButtons(form) {
                    const ta     = $('#schema_org_text', form);
                    const btnFmt = $('#btn-schema-format', form);
                    const btnMin = $('#btn-schema-minify', form);
                    const btnClr = $('#btn-schema-clear',  form);
                    if (!ta) return;

                    const setValue = (val) => { ta.value = val ?? ''; this.notifyChange(form, ta); };

                    btnFmt?.addEventListener('click', () => {
                        const v = ta.value.trim(); if (!v) return;
                        try { setValue(JSON.stringify(JSON.parse(v), null, 2)); }
                        catch (e) { window.SchemaorgCard?.showError?.('schema_org_text', 'JSON inválido: ' + e.message); }
                    });

                    btnMin?.addEventListener('click', () => {
                        const v = ta.value.trim(); if (!v) return setValue('');
                        try { setValue(JSON.stringify(JSON.parse(v))); }
                        catch (e) { window.SchemaorgCard?.showError?.('schema_org_text', 'JSON inválido: ' + e.message); }
                    });

                    btnClr?.addEventListener('click', () => setValue(''));
                },

                mountOnce() {
                    const form = $(this.formId);
                    if (!form || form.dataset.schemaInit === '1') return;
                    form.dataset.schemaInit = '1';

                    // Botones/textarea
                    this.bindButtons(form);

                    // Tu listener de formulario
                    window.SchemaorgCard = new formCustomListener({
                        formSelector: this.formId,
                        buttonSelectors: ['.btn-save', '.btn-cancel'],
                        dispatchOnSubmit: 'save',
                        fieldsValidation: {
                            schema_org_text: {
                                validators: {
                                    callback: {
                                        message: 'JSON inválido',
                                        callback: (input) => {
                                            const raw = (input.value || '').trim();
                                            if (!raw) return true; // si quieres requerirlo en override, valida en PHP
                                            try {
                                                const obj = JSON.parse(raw);
                                                if (!obj['@type'] && !obj['@graph']) {
                                                    return { valid:false, message:'Falta @type o @graph' };
                                                }
                                                return true;
                                            } catch (e) {
                                                return { valid:false, message: e.message };
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    });
                },

                mount() {
                    document.addEventListener('DOMContentLoaded', () => this.mountOnce());
                    document.addEventListener('livewire:init', () => {
                        const re = () => this.mountOnce();
                        Livewire.hook('morph.updated', re);
                        Livewire.hook('commit',        re);
                        Livewire.hook('message.failed',re);
                    });
                }
            };

            /* ==============================
            * Arranque
            * ============================== */
            ModeToggle.mount();
            SchemaEditor.mount();
        })();
    </script>
@endpush
