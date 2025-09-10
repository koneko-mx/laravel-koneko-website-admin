//import '@vuexy-admin/assets/js/notifications/LivewireNotification.js';
//import FormCustomListener from '@vuexy-admin/assets/js/forms/formCustomListener';

// Inicializar formularios de ajustes de chat
window.ChatSettingsForm = new FormCustomListener({
    formSelector: '#website-chat-settings-card',
    buttonSelectors: ['.btn-save', '.btn-cancel'],
    callbacks: [() => {}],
    dispatchOnSubmit: 'save',
    validationConfig: {
        fields: {
            chat_whatsapp_number: {
                validators: {
                    callback: {
                        message: 'Por favor, introduce un número de teléfono válido para México.',
                        callback: function (input) {
                            // Obtener el proveedor directamente dentro de la validación
                            const provider = document.querySelector('#chat_provider')?.value;

                            // Validar solo si el proveedor es WhatsApp
                            if (provider !== 'whatsapp') return true;

                            const cleanValue = input.value.replace(/\D/g, '');
                            const regex = /^[1-9]\d{9}$/; // Exactamente 10 dígitos

                            return regex.test(cleanValue);
                        }
                    },
                    notEmpty: {
                        message: 'El número de teléfono es obligatorio.',
                        enabled: () => {
                            // Obtener el proveedor directamente dentro de la validación
                            const provider = document.querySelector('#chat_provider')?.value;

                            return provider === 'whatsapp'; // Habilita solo si es WhatsApp
                        }
                    }
                }
            },
            chat_whatsapp_message: {
                validators: {
                    stringLength: {
                        max: 500,
                        message: 'El mensaje no puede exceder los 500 caracteres.'
                    },
                    notEmpty: {
                        message: 'El mensaje es obligatorio.',
                        enabled: () => {
                            // Obtener el proveedor directamente dentro de la validación
                            const provider = document.querySelector('#chat_provider')?.value;

                            return provider === 'whatsapp'; // Habilita solo si es WhatsApp
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
