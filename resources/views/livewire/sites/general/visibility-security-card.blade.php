<div>
    <x-vuexy-admin::form.card-form
        id="website-visibility-security-card"
        title="Visibilidad y seguridad"
        showActions
        wire:submit.prevent="save"
    >
        <x-vuexy-admin::form.checkbox model="www_redirect" switch label="Redirigir sin www." />
        <x-vuexy-admin::form.checkbox model="force_https" switch label="Forzar HTTPS" />
        <hr>
        <x-vuexy-admin::form.select model="status" label="Estado del sitio" :options="$status_options" no-placeholder />
        <hr>
        <x-vuexy-admin::form.select model="coming_soon_content_id" label="Pagina de proximamente" :options="$pages_options" />
        <x-vuexy-admin::form.select model="maintenance_content_id" label="Pagina de mantenimiento" :options="$pages_options" />
    </x-vuexy-admin::form.card-form>
</div>

@push('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const STATUS_COMING_SOON = '{{ \Koneko\KonekoWebsiteAdmin\Application\Enums\Websites\WebsiteSiteStatus::COMING_SOON->value }}';
            const STATUS_MAINTENANCE = '{{ \Koneko\KonekoWebsiteAdmin\Application\Enums\Websites\WebsiteSiteStatus::MAINTENANCE->value }}';

            // Inicializar formularios de Visibilidad y seguridad
            window.VisibilitySecuritySettingsForm = new formCustomListener({
                formSelector: '#website-visibility-security-card',
                buttonSelectors: ['.btn-save', '.btn-cancel'],
                dispatchOnSubmit: 'save',
                fieldsValidation: {
                    status: {
                        validators: {
                            notEmpty: { message: 'Requerido.' }
                        }
                    },
                    coming_soon_content_id: {
                        validators: {
                            callback: {
                                message: 'Selecciona la página para “Próximamente”.',
                                callback: function (input, ctx) {
                                    const statusEl = document.querySelector('#website-visibility-security-card [name="status"]');
                                    const statusVal = statusEl ? statusEl.value : '';
                                    const v = (input.value || '').trim();
                                    if (statusVal === STATUS_COMING_SOON) {
                                        return !!v; // requerido
                                    }
                                    return true; // no aplica
                                }
                            }
                        }
                    },
                    maintenance_content_id: {
                        validators: {
                            callback: {
                                message: 'Selecciona la página para “Mantenimiento”.',
                                callback: function (input, ctx) {
                                    const statusEl = document.querySelector('#website-visibility-security-card [name="status"]');
                                    const statusVal = statusEl ? statusEl.value : '';
                                    const v = (input.value || '').trim();
                                    if (statusVal === STATUS_MAINTENANCE) {
                                        return !!v; // requerido
                                    }
                                    return true; // no aplica
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
