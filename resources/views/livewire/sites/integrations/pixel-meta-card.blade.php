<div x-data="{ ena: @entangle('pixel_enabled') }">
  <x-vuexy-admin::form.card-form
    id="website-meta-pixel-settings-card"
    title="Meta Pixel"
    subtitle="Activa Meta Pixel e indica el Pixel ID"
    linkHref="https://business.facebook.com/events_manager2/list"
    linkText="Abrir Events Manager"
    showActions
  >
    <x-vuexy-admin::form.checkbox
      model="pixel_enabled"
      id="pixel_enabled"
      label="Habilitar Meta Pixel"
      switch
    />

    <x-vuexy-admin::form.input
      model="pixel_id"
      id="pixel_id"
      label="Pixel ID"
      icon="ti ti-brand-meta"
      placeholder="123456789012345"
      autocomplete="off"
      x-bind:disabled="!ena"
    />

    <small class="text-muted">
      El ID es numérico, lo encuentras en Events Manager.
    </small>
  </x-vuexy-admin::form.card-form>
</div>

@push('page-script')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const pixelPattern = /^\d{5,20}$/;

    window.MetaPixelSettingsForm = new formCustomListener({
      formSelector:    '#website-meta-pixel-settings-card',
      buttonSelectors: ['.btn-save', '.btn-cancel'],
      dispatchOnSubmit: 'save',
      fieldsValidation: {
        pixel_id: {
          validators: {
            callback: {
              message: 'El Pixel ID debe ser numérico (5 a 20 dígitos).',
              callback: function (input) {
                const ena = document.getElementById('pixel_enabled')?.checked;
                if (!ena) return true;
                return pixelPattern.test((input.value || '').trim());
              }
            }
          }
        }
      }
    });
  });
</script>
@endpush
