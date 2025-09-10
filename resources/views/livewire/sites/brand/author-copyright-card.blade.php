<div>
    <x-vuexy-admin::form.card-form
        id="author-copyright-card"
        title="Autor y Copyright"
        showActions
        wire:submit.prevent="save"
    >
        @if (!$isSite)
            {{-- Selector de modo --}}
            <x-koneko-website-admin::form.mode-toggle
                :is-site="$isSite"
                model="author_mode"
                group="author"
                :value="$author_mode"
                {{-- el contenedor padre define dónde togglear --}}
                data-scope="#author-copyright-card"
                data-show-when-override=".display-enable"
                data-show-when-inherit=".display-inherit" />
        @endif
        <x-vuexy-admin::form.input model="author" label="Autor del sitio" />

        <hr>

        @if (!$isSite)
            {{-- Selector de modo --}}
            <x-koneko-website-admin::form.mode-toggle
                :is-site="$isSite"
                model="copyright_mode"
                group="copyright"
                :value="$copyright_mode"
                {{-- el contenedor padre define dónde togglear --}}
                data-scope="#author-copyright-card"
                data-show-when-override=".display-enable"
                data-show-when-inherit=".display-inherit" />
        @endif
        <x-vuexy-admin::form.input model="copyright" label="Copyright" />
    </x-vuexy-admin::form.card-form>
</div>

@push('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            window.websiteBrandForm = new formCustomListener({
                formSelector: '#author-copyright-card',
                buttonSelectors: ['.btn-save', '.btn-cancel'],
                dispatchOnSubmit: 'save',
                fieldsValidation: {
                    author: {
                        validators: {
                            // Opcional: solo valida si hay valor
                            stringLength: {
                                max: 70,
                                message: 'Máximo 70 caracteres.',
                            },
                            regexp: {
                                // Letras, números, espacios y puntuación común en nombres
                                regexp: /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ0-9 .,'’\-()]+$/,
                                message: 'Solo letras, números y puntuación básica.',
                            },
                        },
                    },
                    copyright: {
                        validators: {
                            stringLength: {
                                max: 160,
                                message: 'Máximo 160 caracteres.',
                            },
                        },
                    },
                }
            });
        });
    </script>
@endpush
