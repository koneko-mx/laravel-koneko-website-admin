<div x-data="{}" wire:key="branches-card-{{ $site->id }}">
    <x-vuexy-admin::form.card-form
      id="website-branches-card"
      title="Sucursales"
      subtitle="Lista de ubicaciones con datos básicos"
      showActions
      wire:submit.prevent="save"
    >
      <div class="mb-2">
        <x-vuexy-admin::button.basic type="button" variant="secondary" size="sm" icon="ti ti-plus" class="mr-2" label="Agregar sucursal" wire:click="addBranch" />
      </div>

      @forelse($items as $i => $b)
        <div class="card mb-3 border bg-light-subtle">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <strong>Sucursal #{{ $i + 1 }}</strong>
              <x-vuexy-admin::button.basic type="button" variant="text" size="sm" icon="ti ti-trash" class="text-danger" label="Quitar" wire:click="removeBranch({{ $i }})" />
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <x-vuexy-admin::form.input :model="'items.'.$i.'.name'" id="branch_{{ $i }}_name" label="Nombre" icon="ti ti-building" placeholder="Nombre de la sucursal" />
              </div>
              <div class="col-md-3">
                <x-vuexy-admin::form.input :model="'items.'.$i.'.phone'" id="branch_{{ $i }}_phone" label="Teléfono" icon="ti ti-phone" placeholder="+52..." />
              </div>
              <div class="col-md-3">
                <x-vuexy-admin::form.input :model="'items.'.$i.'.email'" id="branch_{{ $i }}_email" label="Email" icon="ti ti-mail" placeholder="contacto@..." />
              </div>

              <div class="col-md-9">
                <x-vuexy-admin::form.input :model="'items.'.$i.'.address'" id="branch_{{ $i }}_address" label="Dirección" icon="ti ti-map-pin" placeholder="Calle, número, colonia" />
              </div>
              <div class="col-md-3">
                <x-vuexy-admin::form.input type="number" step="0.000001" :model="'items.'.$i.'.lat'" id="branch_{{ $i }}_lat" label="Lat" icon="ti ti-map-pin-2" placeholder="19.43" />
              </div>
              <div class="col-md-3">
                <x-vuexy-admin::form.input type="number" step="0.000001" :model="'items.'.$i.'.lng'" id="branch_{{ $i }}_lng" label="Lng" icon="ti ti-map-pin-2" placeholder="-99.13" />
              </div>
              <div class="col-md-9">
                <x-vuexy-admin::form.input :model="'items.'.$i.'.hours'" id="branch_{{ $i }}_hours" label="Horario (texto)" icon="ti ti-clock" placeholder="L–V 9:00–18:00" />
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="alert alert-info">No hay sucursales. Agrega la primera con el botón de arriba.</div>
      @endforelse

      <div class="notification-container mb-4" wire:ignore></div>
    </x-vuexy-admin::form.card-form>
</div>

@push('page-script')
<script>
    document.addEventListener('livewire:init', () => {
      // Validación cliente mínima para campos dinámicos: confiamos en el servidor
      window.BranchesSettingsForm = new formCustomListener({
        formSelector:    '#website-branches-card',
        buttonSelectors: ['.btn-save', '.btn-cancel'],
        dispatchOnSubmit: 'save',
        fieldsValidation: { /* opcional: agregar reglas si fijas un máximo de items */ }
      });
    });
</script>
@endpush
