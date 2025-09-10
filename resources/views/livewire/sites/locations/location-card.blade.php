<div x-data="{}" wire:key="location-card-{{ $site->id }}">
    <x-vuexy-admin::form.card-form
      id="website-location-card"
      title="Ubicación"
      subtitle="Dirección y coordenadas (se genera enlace a Google Maps)"
      showActions
      wire:submit.prevent="save"
    >
      <x-vuexy-admin::form.input model="address_line" id="address_line" label="Dirección" icon="ti ti-map-pin" placeholder="Calle y número, colonia" autocomplete="off" />

      <div class="row">
        <div class="col-md-4">
          <x-vuexy-admin::form.input model="city" id="city" label="Ciudad" icon="ti ti-building" placeholder="Ciudad" autocomplete="off" />
        </div>
        <div class="col-md-4">
          <x-vuexy-admin::form.input model="state" id="state" label="Estado/Provincia" icon="ti ti-map" placeholder="Estado" autocomplete="off" />
        </div>
        <div class="col-md-4">
          <x-vuexy-admin::form.input model="postal_code" id="postal_code" label="C.P." icon="ti ti-hash" placeholder="00000" autocomplete="off" />
        </div>
      </div>

      <div class="row">
        <div class="col-6">
          <x-vuexy-admin::form.input type="number" step="0.000001" model="location_lat" id="location_lat" label="Latitud" icon="ti ti-map-pin-2" placeholder="19.432608" />
        </div>
        <div class="col-6">
          <x-vuexy-admin::form.input type="number" step="0.000001" model="location_lng" id="location_lng" label="Longitud" icon="ti ti-map-pin-2" placeholder="-99.133209" />
        </div>
      </div>

      @if($maps_url)
        <div class="mt-2">
          <a href="{{ $maps_url }}" target="_blank" rel="noopener" class="text-primary">Ver en Google Maps</a>
        </div>
      @endif

      <div class="notification-container mb-4" wire:ignore></div>
    </x-vuexy-admin::form.card-form>
  </div>

  @push('page-script')
  <script>
    document.addEventListener('livewire:init', () => {
      const inRange = (val, min, max) => val === '' || (Number(val) >= min && Number(val) <= max);

      window.LocationSettingsForm = new formCustomListener({
        formSelector:    '#website-location-card',
        buttonSelectors: ['.btn-save', '.btn-cancel'],
        dispatchOnSubmit: 'save',
        fieldsValidation: {
          location_lat: { validators: { callback: { message: 'Latitud fuera de rango (-90..90).', callback: (i) => inRange(i.value, -90, 90) } } },
          location_lng: { validators: { callback: { message: 'Longitud fuera de rango (-180..180).', callback: (i) => inRange(i.value, -180, 180) } } },
        }
      });
    });
  </script>
  @endpush
