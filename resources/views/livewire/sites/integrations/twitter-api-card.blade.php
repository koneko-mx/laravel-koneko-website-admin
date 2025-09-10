<div x-data="{ ena: @entangle('tw_enabled') }">
    <x-vuexy-admin::form.card-form
      id="website-twitter-api-settings-card"
      title="Twitter API (X)"
      subtitle="Activa y configura tus credenciales de API"
      linkHref="https://developer.x.com/"
      linkText="Abrir developer.x.com"
      showActions
    >
      <x-vuexy-admin::form.checkbox
        model="tw_enabled"
        id="tw_enabled"
        label="Habilitar Twitter API"
        switch
      />

      <x-vuexy-admin::form.input
        model="tw_api_key"
        id="tw_api_key"
        label="API Key"
        icon="ti ti-key"
        placeholder="p. ej. ABCD1234..."
        autocomplete="off"
        x-bind:disabled="!ena"
      />

      <x-vuexy-admin::form.input
        model="tw_api_secret"
        id="tw_api_secret"
        label="API Secret Key"
        icon="ti ti-lock"
        placeholder="p. ej. A1B2C3..."
        autocomplete="off"
        x-bind:disabled="!ena"
      />

      <x-vuexy-admin::form.input
        model="tw_bearer_token"
        id="tw_bearer_token"
        label="Bearer Token"
        icon="ti ti-shield"
        placeholder="p. ej. AAAAAA..."
        autocomplete="off"
        x-bind:disabled="!ena"
      />

      <small class="text-muted">
        Recomendación: usar <em>secrets</em> del proyecto para credenciales en producción.
      </small>
    </x-vuexy-admin::form.card-form>
</div>

@push('page-script')
<script>
    document.addEventListener('DOMContentLoaded', () => {
      const keyPattern     = /^[A-Za-z0-9-_]{10,80}$/;
      const secretPattern  = /^[A-Za-z0-9-_]{10,120}$/;
      const bearerPattern  = /^[A-Za-z0-9._-]{20,200}$/;

      window.TwitterApiSettingsForm = new formCustomListener({
        formSelector:    '#website-twitter-api-settings-card',
        buttonSelectors: ['.btn-save', '.btn-cancel'],
        dispatchOnSubmit: 'save',
        fieldsValidation: {
          tw_api_key: {
            validators: {
              callback: {
                message: 'API Key inválida (10-80, letras/números y -_).',
                callback: function (input) {
                  const ena = document.getElementById('tw_enabled')?.checked;
                  if (!ena) return true;
                  return keyPattern.test((input.value || '').trim());
                }
              }
            }
          },
          tw_api_secret: {
            validators: {
              callback: {
                message: 'API Secret inválida (10-120, letras/números y -_).',
                callback: function (input) {
                  const ena = document.getElementById('tw_enabled')?.checked;
                  if (!ena) return true;
                  return secretPattern.test((input.value || '').trim());
                }
              }
            }
          },
          tw_bearer_token: {
            validators: {
              callback: {
                message: 'Bearer Token inválido (20-200, letras/números y -_.).',
                callback: function (input) {
                  const ena = document.getElementById('tw_enabled')?.checked;
                  if (!ena) return true;
                  return bearerPattern.test((input.value || '').trim());
                }
              }
            }
          }
        }
      });
    });
</script>
@endpush
