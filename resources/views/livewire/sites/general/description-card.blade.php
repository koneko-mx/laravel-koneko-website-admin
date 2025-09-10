<div>
    <x-vuexy-admin::form.card-form
        id="website-description-card"
        title="Descripción del sitio"
        subtitle="Configura datos básicos del sitio"
        showActions
        wire:submit.prevent="save"
    >
        <x-vuexy-admin::form.input model="domain" id="domain" label="Nombre de dominio" icon="ti ti-world" placeholder="ejemplo.com" />
        <x-vuexy-admin::form.input model="title" id="title" label="Titulo del sitio" icon="ti ti-label-important" placeholder="Titulo del sitio" />
    </x-vuexy-admin::form.card-form>
</div>

@push('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            window.websiteDescriptionForm = new formCustomListener({
                formSelector: '#website-description-card',
                buttonSelectors: ['.btn-save', '.btn-cancel'],
                dispatchOnSubmit: 'save',
                fieldsValidation: {
                    domain: {
                        validators: {
                            notEmpty: {
                                message: 'El dominio es obligatorio.',
                            },
                            regexp: {
                                // Sin protocolo, sin www, dominio válido (con punto), labels 1–63 chars
                                regexp: /^(?!https?:\/\/)(?!www\.)(?=.{1,253}$)(?!-)[A-Za-z0-9-]{1,63}(?<!-)(?:\.(?!-)[A-Za-z0-9-]{1,63}(?<!-))+$/,
                                message: 'Usa un dominio válido sin protocolo ni www (ej: ejemplo.com).',
                            },
                        },
                    },
                    title: {
                        validators: {
                            notEmpty: {
                                message: 'El título es obligatorio.',
                            },
                            stringLength: {
                                max: 96,
                                message: 'Máximo 96 caracteres.',
                            },
                        },
                    },
                }
            });
        });
    </script>
@endpush
