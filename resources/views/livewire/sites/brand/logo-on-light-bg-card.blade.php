<div>
    <x-vuexy-admin::form.card-form
        id="logo-on-light-bg-card"
        title="Logotipo sobre fondo claro"
        subtitle="Formato recomendado: PNG/WebP con fondo transparente"
        showActions
        wire:submit.prevent="saveVertical"
        overlayTarget="upload_logo,saveVertical"
    >
        <x-vuexy-admin::form.input type="file" label="Archivo de logotipo" model="upload_logo" id="upload_logo" accept="image/png,image/jpeg,image/webp" />

        @php
            $srcV = $upload_logo
                ? $upload_logo->temporaryUrl()
                : $logo_path;
        @endphp

        <div class="mb-3">
            <div class="d-flex justify-content-center align-items-center bg-slate-100 p-4 rounded-3" style="min-height:240px;">
                <img src="{{ $srcV }}" alt="Vista previa logotipo" style="max-height:220px; object-fit:contain;"/>
            </div>
            <small class="text-muted">Se generan tamaños small/medium/large automáticamente.</small>
        </div>

        @slot('actions')
            <div class="row">
                <div class="col-12 text-end mb-4">
                    <x-vuexy-admin::button.basic type="submit" variant="primary" size="sm" icon="ti ti-device-floppy" class="btn-save mt-2 mr-2" no-waves
                        label="Guardar cambios"
                        disabled="{{ $upload_logo === null }}" />
                    <x-vuexy-admin::button.basic variant="secondary" size="sm" icon="ti ti-rotate-2" class="btn-cancel mt-2 mr-2"
                        label="Cancelar"
                        wire:click="resetVertical"
                        disabled="{{ $upload_logo === null }}" />
                </div>
            </div>
        @endslot
    </x-vuexy-admin::form.card-form>
    <x-vuexy-admin::form.card-form
        id="logo-h-on-light-bg-card"
        title="Logotipo horizontal sobre fondo claro"
        subtitle="Sugerido ~4:1 o 5:1, PNG/WebP con transparencia"
        showActions
        wire:submit.prevent="saveHorizontal"
        overlayTarget="upload_logo_h,saveHorizontal"
    >
        <x-vuexy-admin::form.input type="file" label="Archivo de logotipo horizontal" model="upload_logo_h" id="upload_logo_h" accept="image/png,image/jpeg,image/webp" />

        @php
            $srcH = $upload_logo_h
                ? $upload_logo_h->temporaryUrl()
                : $logo_path_h;
        @endphp

        <div class="mb-3">
            <div class="d-flex justify-content-center align-items-center bg-slate-100 p-4 rounded-3" style="min-height:140px;">
                <img src="{{ $srcH }}" alt="Vista previa logotipo horizontal" style="max-height:90px; max-width:100%; object-fit:contain;"/>
            </div>
            <small class="text-muted">Se generan tamaños small/medium/large automáticamente para la variante <code>h</code>.</small>
        </div>

        @slot('actions')
            <div class="row">
                <div class="col-12 text-end mb-4">
                    <x-vuexy-admin::button.basic type="submit" variant="primary" size="sm" icon="ti ti-device-floppy" class="btn-save mt-2 mr-2" no-waves
                        label="Guardar cambios"
                        disabled="{{ $upload_logo_h === null }}" />
                    <x-vuexy-admin::button.basic variant="secondary" size="sm" icon="ti ti-rotate-2" class="btn-cancel mt-2 mr-2"
                        label="Cancelar"
                        wire:click="resetHorizontal"
                        disabled="{{ $upload_logo_h === null }}" />
                </div>
            </div>
        @endslot
    </x-vuexy-admin::form.card-form>
</div>

@push('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            window.logoOnLightVerticalForm = new formCustomListener({
                formSelector: '#logo-on-light-bg-card',
                buttonSelectors: ['.btn-save', '.btn-cancel'],
                dispatchOnSubmit: 'saveVertical',
            });

            window.logoOnLightHorizontalForm = new formCustomListener({
                formSelector: '#logo-h-on-light-bg-card',
                buttonSelectors: ['.btn-save', '.btn-cancel'],
                dispatchOnSubmit: 'saveHorizontal',
            });
        });
    </script>
@endpush
