<div>
    <x-vuexy-admin::form.card-form
        id="website-seo-locale-card"
        title="Idioma"
        showActions
        wire:submit.prevent="save"
    >
        <x-vuexy-admin::form.select model="locale" label="Idioma" :options="$localeOptions" />
    </x-vuexy-admin::form.card-form>
</div>

@push('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            window.seoLocaleForm = new formCustomListener({
                formSelector: '#website-seo-locale-card',
                buttonSelectors: ['.btn-save', '.btn-cancel'],
                dispatchOnSubmit: 'save',
                fieldsValidation: {
                    locale: {
                        validators: {
                            notEmpty: {
                                message: 'El idioma es obligatorio.',
                            },
                        },
                    },
                }
            });
        });
    </script>
@endpush
