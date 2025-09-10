<div>
    <x-vuexy-admin::form.card-form
        id="website-brand-card"
        title="Marca"
        showActions
        wire:submit.prevent="save"
    >
        <x-vuexy-admin::form.input model="brand_name" label="Nombre comercial" />
        <x-vuexy-admin::form.textarea model="slogan" label="Eslogan de la marca" />
    </x-vuexy-admin::form.card-form>
</div>

@push('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            window.websiteBrandForm = new formCustomListener({
                formSelector: '#website-brand-card',
                buttonSelectors: ['.btn-save', '.btn-cancel'],
                dispatchOnSubmit: 'save',
                fieldsValidation: {
                    brand_name: {
                        validators: {
                            notEmpty: {
                                message: 'El nombre comercial es obligatorio.',
                            },
                            stringLength: {
                                max: 64,
                                message: 'Máximo 64 caracteres.',
                            },
                        },
                    },
                    slogan: {
                        validators: {
                            stringLength: {
                                max: 254,
                                message: 'Máximo 254 caracteres.',
                            },
                        },
                    },
                }
            });
        });
    </script>
@endpush
