// Inicializar formularios de ajustes de información de contacto
window.ContactInfoSettingsForm = new FormCustomListener({
    formSelector: '#website-contact-info-card-card',
    buttonSelectors: ['.btn-save', '.btn-cancel'],
    callbacks: [() => {}],
    dispatchOnSubmit: 'save',
    validationConfig: {
        fields: {
            // Validación para número telefónico
            phone_number: {
                validators: {
                    callback: {
                        message: 'Por favor, introduce un número de teléfono válido para México.',
                        callback: function (input) {
                            // Si el campo está vacío, no hacemos validación
                            if (input.value.trim() === '') {
                                return true; // Permitir vacío
                            }

                            // Si no está vacío, validamos el formato del número
                            const cleanValue = input.value.replace(/\D/g, '');
                            const regex = /^[1-9]\d{9}$/; // Exactamente 10 dígitos

                            return regex.test(cleanValue); // Valida solo si hay un número
                        }
                    }
                }
            },
            // Validación para extensión telefónica (opcional, pero solo si phone_number tiene valor)
            phone_number_ext: {
                validators: {
                    stringLength: {
                        max: 10,
                        message: 'La extensión no debe exceder los 10 caracteres.'
                    },
                    callback: {
                        message: 'La extensión requiere de ingresar un número telefónico.',
                        callback: function (input) {
                            // Obtener el valor de 'phone_number'
                            const phoneNumber = document.querySelector('[name="phone_number"]')?.value.trim();

                            // Si el número telefónico tiene valor, entonces la extensión es obligatoria
                            if (phoneNumber !== '') {
                                // Si la extensión está vacía, la validación falla
                                return true; // Permitir vacío
                            }

                            // Si no se ha ingresado un número telefónico, la extensión no debe tener valor
                            return input.value.trim() === '';
                        }
                    }
                }
            },
            // Validación para número telefónico
            phone_number_2: {
                validators: {
                    callback: {
                        message: 'Por favor, introduce un número de teléfono válido para México.',
                        callback: function (input) {
                            // Si el campo está vacío, no hacemos validación
                            if (input.value.trim() === '') {
                                return true; // Permitir vacío
                            }

                            // Si no está vacío, validamos el formato del número
                            const cleanValue = input.value.replace(/\D/g, '');
                            const regex = /^[1-9]\d{9}$/; // Exactamente 10 dígitos

                            return regex.test(cleanValue); // Valida solo si hay un número
                        }
                    }
                }
            },
            // Validación para extensión telefónica (opcional, pero solo si phone_number tiene valor)
            phone_number_2_ext: {
                validators: {
                    stringLength: {
                        max: 10,
                        message: 'La extensión no debe exceder los 10 caracteres.'
                    },
                    callback: {
                        message: 'La extensión requiere de ingresar un número telefónico.',
                        callback: function (input) {
                            // Obtener el valor de 'phone_number'
                            const phoneNumber = document.querySelector('[name="phone_number_2"]')?.value.trim();

                            // Si el número telefónico tiene valor, entonces la extensión es obligatoria
                            if (phoneNumber !== '') {
                                // Si la extensión está vacía, la validación falla
                                return true; // Permitir vacío
                            }

                            // Si no se ha ingresado un número telefónico, la extensión no debe tener valor
                            return input.value.trim() === '';
                        }
                    }
                }
            },
            // Validación para correo electrónico de contacto (opcional)
            email: {
                validators: {
                    emailAddress: {
                        message: 'Por favor, introduce un correo electrónico válido.'
                    }
                }
            },
            // Validación para horario (No obligatorio, máximo 160 caracteres)
            horario: {
                validators: {
                    stringLength: {
                        max: 160,
                        message: 'El horario no puede exceder los 160 caracteres.'
                    }
                }
            },
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

// Inicializar formularios de ajustes de ubicación
window.LocationSettingsForm = new FormCustomListener({
    formSelector: '#website-location-card-card',
    buttonSelectors: ['.btn-save', '.btn-cancel'],
    callbacks: [() => {}],
    dispatchOnSubmit: 'save',
    validationConfig: {
        fields: {
            // Validación para dirección (No obligatorio, máximo 160 caracteres)
            direccion: {
                validators: {
                    stringLength: {
                        max: 160,
                        message: 'La dirección no puede exceder los 160 caracteres.'
                    }
                }
            },
            // Validación para latitud (No obligatorio, pero debe ser un número si se ingresa)
            location_lat: {
                validators: {
                    numeric: {
                        message: 'La latitud debe ser un número.'
                    },
                    callback: {
                        message: 'La latitud es obligatoria si se ingresa longitud.',
                        callback: function (input) {
                            // Obtener el valor de longitud
                            const longitude = document.querySelector('[name="location_lng"]')?.value.trim();

                            // Si longitud tiene un valor, entonces latitud es obligatorio
                            if (longitude !== '') {
                                return input.value.trim() !== ''; // La latitud no puede estar vacía
                            }

                            return true; // Si longitud está vacío, no se valida latitud
                        }
                    }
                }
            },
            // Validación para longitud (No obligatorio, pero debe ser un número si se ingresa)
            location_lng: {
                validators: {
                    numeric: {
                        message: 'La longitud debe ser un número.'
                    },
                    callback: {
                        message: 'La longitud es obligatoria si se ingresa latitud.',
                        callback: function (input) {
                            // Obtener el valor de latitud
                            const latitude = document.querySelector('[name="location_lat"]')?.value.trim();

                            // Si latitud tiene un valor, entonces longitud es obligatorio
                            if (latitude !== '') {
                                return input.value.trim() !== ''; // La longitud no puede estar vacía
                            }

                            return true; // Si latitud está vacío, no se valida longitud
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
