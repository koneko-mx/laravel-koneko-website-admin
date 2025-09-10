// Inicializar formularios de ajustes de Formularios de contacto
window.ContactFormSettingsForm = new FormCustomListener({
    formSelector: '#website-contact-form-card-card',
    buttonSelectors: ['.btn-save', '.btn-cancel'],
    callbacks: [() => {}],
    dispatchOnSubmit: 'save',
    validationConfig: {
        fields: {
            // Validación para correo electrónico de recepción
            to_email: {
                validators: {
                    emailAddress: {
                        message: 'Por favor, introduce un correo electrónico válido.'
                    },
                    notEmpty: {
                        message: 'El correo electrónico es obligatorio.'
                    }
                }
            },
            // Validación para correo electrónico con copia
            to_email_cc: {
                validators: {
                    emailAddress: {
                        message: 'Por favor, introduce un correo electrónico válido.'
                    },
                    // Validación personalizada para comparar ambos correos electrónicos
                    callback: {
                        message: 'Los correos electrónicos deben ser diferentes.',
                        callback: function (input) {
                            const email = document.querySelector('[name="to_email"]').value.trim();
                            const emailCC = input.value.trim();

                            // Si ambos correos son iguales, la validación falla
                            if (email === emailCC) {
                                return false; // Los correos son iguales, por lo que la validación falla
                            }

                            return true; // Si son diferentes, la validación pasa
                        }
                    }
                }
            },
            // Validación para el asunto del formulario de contacto
            subject: {
                validators: {
                    stringLength: {
                        max: 60,
                        message: 'El título del correo no puede exceder los 60 caracteres.'
                    },
                    notEmpty: {
                        message: 'El título del correo es obligatorio.'
                    }
                }
            },
            // Validación para el mensaje de envío
            submit_message: {
                validators: {
                    stringLength: {
                        max: 250,
                        message: 'El mensaje no puede exceder los 250 caracteres.'
                    },
                    notEmpty: {
                        message: 'El mensaje de envío es obligatorio.'
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
