// Inicializar formularios de ajustes de análisis de datos
window.AnalyticsSettingsForm = new FormCustomListener({
    formSelector: '#website-analytics-settings-card',
    buttonSelectors: ['.btn-save', '.btn-cancel'],
    callbacks: [() => {}],
    dispatchOnSubmit: 'save',
    validationConfig: {
        fields: {
            google_analytics_id: {
                validators: {
                    callback: {
                        message: 'ID de medición de Google Analytics no tienen un formato válido.',
                        callback: function (input) {
                            if (document.getElementById('google_analytics_enabled').checked) {
                                return input.value.trim() !== '';
                            }
                            return true;
                        }
                    }
                }
            }
        },
        plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap5: new FormValidation.plugins.Bootstrap5({
                eleValidClass: '',
                rowSelector: '.fv-row'
            }),
            submitButton: new FormValidation.plugins.SubmitButton(),
            autoFocus: new FormValidation.plugins.AutoFocus()
        }
    }
});
