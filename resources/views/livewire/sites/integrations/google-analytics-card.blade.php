<div x-data="{ ena: @entangle('ga_enabled') }">
    <x-vuexy-admin::form.card-form
        id="website-analytics-settings-card"
        title="Google Analytics (GA4)"
        subtitle="Activa GA4 e indica tu ID de medición"
        linkHref="https://analytics.google.com/analytics/web/"
        linkText="Abrir Google Analytics"
        showActions
    >
        {{-- Switch ON/OFF --}}
        <x-vuexy-admin::form.checkbox
            model="ga_enabled"
            id="ga_enabled"
            label="Habilitar Google Analytics"
            switch
        />

        {{-- ID de medición GA4 --}}
        <x-vuexy-admin::form.input
            model="ga_id"
            id="ga_id"
            label="ID de medición (GA4)"
            icon="ti ti-brand-google-filled"
            placeholder="G-ABC123DEF4"
            autocomplete="off"
            oninput="this.value=this.value.toUpperCase()"
            x-bind:disabled="!ena"
        />

        <small class="text-muted">
            Formato GA4: <code>G-XXXXXXXXXX</code>. No uses <code>UA-</code> (Universal Analytics retirado).
        </small>
    </x-vuexy-admin::form.card-form>
</div>

@push('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Regla GA4
            const gaIdPattern = /^G-[A-Za-z0-9]{8,16}$/;

            // Instancia del listener (JS puro, sin data-fv)
            window.AnalyticsSettingsForm = new formCustomListener({
                formSelector: '#website-analytics-settings-card',
                buttonSelectors: ['.btn-save', '.btn-cancel'],
                dispatchOnSubmit: 'save',
                fieldsValidation: {
                    ga_id: {
                        validators: {
                            callback: {
                                message: 'Usa un ID GA4 válido (p. ej. G-ABC123DEF4).',
                                callback: function (input) {
                                    const ena = document.getElementById('ga_enabled')?.checked;
                                    if (!ena) return true; // si está apagado, no exigir
                                    return gaIdPattern.test((input.value || '').trim());
                                }
                            }
                        }
                    }
                },
            });
        });
    </script>
@endpush
