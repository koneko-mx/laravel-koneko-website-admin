<div x-data="{ ena: @entangle('gsc_enabled') }">
    <x-vuexy-admin::form.card-form
      id="website-gsc-settings-card"
      title="Google Search Console"
      subtitle="Activa GSC e indica tu meta-token de verificación"
      linkHref="https://search.google.com/search-console"
      linkText="Abrir Search Console"
      showActions
    >
      <x-vuexy-admin::form.checkbox
        model="gsc_enabled"
        id="gsc_enabled"
        label="Habilitar Google Search Console"
        switch
      />

      <x-vuexy-admin::form.input
        model="gsc_verification_token"
        id="gsc_verification_token"
        label="Token de verificación (meta)"
        icon="ti ti-shield-check"
        placeholder="p. ej. xxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
        autocomplete="off"
        x-bind:disabled="!ena"
      />

      <small class="text-muted">
        Copia el valor del atributo <code>content</code> del meta-tag
        <code>&lt;meta name="google-site-verification" content="..." /&gt;</code>.
      </small>
    </x-vuexy-admin::form.card-form>
</div>

@push('page-script')
<script>
    document.addEventListener('DOMContentLoaded', () => {
      const tokenPattern = /^[A-Za-z0-9_-]{10,128}$/;

      window.GSCSettingsForm = new formCustomListener({
        formSelector:    '#website-gsc-settings-card',
        buttonSelectors: ['.btn-save', '.btn-cancel'],
        dispatchOnSubmit: 'save',
        fieldsValidation: {
          gsc_verification_token: {
            validators: {
              callback: {
                message: 'Token inválido (solo letras, números, - y _; 10-128).',
                callback: function (input) {
                  const ena = document.getElementById('gsc_enabled')?.checked;
                  if (!ena) return true;
                  return tokenPattern.test((input.value || '').trim());
                }
              }
            }
          }
        }
      });
    });
  </script>
  @endpush
