<div x-data="{ ena: @entangle('gtm_enabled') }">
    <x-vuexy-admin::form.card-form
      id="website-google-tags-settings-card"
      title="Google Tag Manager (GTM)"
      subtitle="Activa GTM e indica el ID del contenedor (p. ej. GTM-ABC1234)"
      showActions
    >
      <div class="mb-3">
        <a href="https://tagmanager.google.com/" target="_blank" rel="noopener noreferrer">
          Abrir Google Tag Manager
        </a>
      </div>

      <x-vuexy-admin::form.checkbox
        model="gtm_enabled"
        id="gtm_enabled"
        label="Habilitar Google Tag Manager"
        switch
      />

      <x-vuexy-admin::form.input
        model="gtm_container_id"
        id="gtm_container_id"
        label="ID de contenedor (GTM)"
        icon="ti ti-tags"
        placeholder="GTM-ABC1234"
        autocomplete="off"
        oninput="this.value=this.value.toUpperCase()"
        x-bind:disabled="!ena"
      />

      <small class="text-muted">
        Formato: <code>GTM-XXXXXXXX</code> (letras y números). Desactiva el switch si aún no tienes tu ID.
      </small>
    </x-vuexy-admin::form.card-form>
</div>

@push('page-script')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const gtmPattern = /^GTM-[A-Za-z0-9]{4,16}$/;

    window.GoogleTagsSettingsForm = new formCustomListener({
      formSelector:    '#website-google-tags-settings-card',
      buttonSelectors: ['.btn-save', '.btn-cancel'],
      dispatchOnSubmit: 'save',
      fieldsValidation: {
        gtm_container_id: {
          validators: {
            callback: {
              message: 'Usa un ID GTM válido, p. ej. GTM-ABC1234.',
              callback: function (input) {
                const ena = document.getElementById('gtm_enabled')?.checked;
                if (!ena) return true;
                return gtmPattern.test((input.value || '').trim());
              }
            }
          }
        }
      }
    });
  });
</script>
@endpush
