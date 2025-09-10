<div x-data="{}" wire:key="contact-form-card-{{ $site->id }}">
    <x-vuexy-admin::form.card-form
      id="website-contact-form-card"
      title="Formulario de contacto"
      subtitle="Configura destinatarios, asunto y mensaje de confirmación"
      showActions
      wire:submit.prevent="save"
    >
      <div class="row">
        <div class="col-md-6">
          <x-vuexy-admin::form.input
            model="to_email"
            id="to_email"
            type="email"
            label="Correo principal"
            icon="ti ti-mail"
            placeholder="donde@recibes.tus.mensajes"
            autocomplete="off"
            required
          />

          <x-vuexy-admin::form.input
            model="to_email_cc"
            id="to_email_cc"
            label="Correos CC (opcional)"
            icon="ti ti-mail-forward"
            placeholder="uno@ejemplo.com, dos@ejemplo.com"
            autocomplete="off"
          />
          <small class="text-muted">Separa múltiples correos por coma o punto y coma. Se normaliza y de-duplica al guardar.</small>
        </div>

        <div class="col-md-6">
          <x-vuexy-admin::form.input
            model="subject"
            id="subject"
            label="Asunto del correo"
            placeholder="Mensaje desde {site}"
            autocomplete="off"
            required
          />

          <x-vuexy-admin::form.textarea
            model="submit_message"
            id="submit_message"
            label="Mensaje de confirmación"
            rows="5"
            placeholder="¡Gracias! Hemos recibido tu mensaje."
            required
          />
          <small class="text-muted d-block">Macros disponibles en asunto y confirmación: <code>{site}</code>, <code>{url}</code>, <code>{date}</code>, <code>{time}</code>.</small>
        </div>
      </div>
    </x-vuexy-admin::form.card-form>
  </div>

  @push('page-script')
  <script>
    document.addEventListener('livewire:init', () => {
      const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      window.ContactFormSettingsForm = new formCustomListener({
        formSelector:    '#website-contact-form-card',
        buttonSelectors: ['.btn-save', '.btn-cancel'],
        dispatchOnSubmit: 'save',
        fieldsValidation: {
          to_email: {
            validators: {
              notEmpty: { message: 'Requerido.' },
              emailAddress: { message: 'Correo inválido.' },
              stringLength: { max: 254, message: 'Máximo 254 caracteres.' }
            }
          },
          to_email_cc: {
            validators: {
              callback: {
                message: 'Lista de correos inválida.',
                callback: function (input) {
                  const v = (input.value || '').trim();
                  if (!v) return true;
                  const list = v.split(/[;,]+/).map(s => s.trim()).filter(Boolean);
                  return list.every(m => emailRe.test(m));
                }
              },
              stringLength: { max: 1000, message: 'Máximo 1000 caracteres.' }
            }
          },
          subject: {
            validators: {
              notEmpty: { message: 'Requerido.' },
              stringLength: { min: 3, max: 120, message: 'Entre 3 y 120 caracteres.' }
            }
          },
          submit_message: {
            validators: {
              notEmpty: { message: 'Requerido.' },
              stringLength: { min: 3, max: 1000, message: 'Entre 3 y 1000 caracteres.' }
            }
          }
        }
      });
    });
  </script>
  @endpush
