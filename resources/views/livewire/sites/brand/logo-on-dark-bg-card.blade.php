<div>
    <x-vuexy-admin::form.card-form
        id="logo-on-dark-bg-card"
        title="Logotipo sobre fondo oscuro"
        subtitle="Formato recomendado: PNG/WebP con fondo transparente"
        showActions
        wire:submit.prevent="saveVerticalDark"
        overlayTarget="upload_logo_dark,saveVerticalDark"
    >
        <x-vuexy-admin::form.input type="file" label="Archivo de logotipo" model="upload_logo_dark" id="upload_logo_dark" accept="image/png,image/jpeg,image/webp" />

        @php
            $srcDark = $upload_logo_dark
                ? $upload_logo_dark->temporaryUrl()
                : $logo_dark_path;
        @endphp

        <div class="mb-3">
            <div class="d-flex justify-content-center align-items-center p-4 rounded-3" style="min-height:240px; background:#0b1220;">
                <img src="{{ $srcDark }}" alt="Vista previa logotipo oscuro" style="max-height:220px; object-fit:contain;"/>
            </div>
            <small class="text-muted">Se generan tamaños small/medium/large automáticamente para la variante <code>dark</code>.</small>
        </div>

        @slot('actions')
            <div class="row">
                <div class="col-12 text-end mb-4">
                    <x-vuexy-admin::button.basic type="submit" variant="primary" size="sm" icon="ti ti-device-floppy" class="btn-save mt-2 mr-2" no-waves
                        label="Guardar cambios"
                        disabled="{{ $upload_logo_dark === null }}" />
                    <x-vuexy-admin::button.basic variant="secondary" size="sm" icon="ti ti-rotate-2" class="btn-cancel mt-2 mr-2"
                        label="Cancelar"
                        wire:click="resetVerticalDark"
                        disabled="{{ $upload_logo_dark === null }}" />
                </div>
            </div>
        @endslot
    </x-vuexy-admin::form.card-form>
    <x-vuexy-admin::form.card-form
        id="logo-h-on-dark-bg-card"
        title="Logotipo horizontal sobre fondo oscuro"
        subtitle="Sugerido ~4:1 o 5:1, PNG/WebP con transparencia"
        showActions
        wire:submit.prevent="saveHorizontalDark"
        overlayTarget="upload_logo_h_dark,saveHorizontalDark"
    >
        <x-vuexy-admin::form.input type="file" label="Archivo de logotipo horizontal" model="upload_logo_h_dark" id="upload_logo_h_dark" accept="image/png,image/jpeg,image/webp" />

        @php
            $srcHDark = $upload_logo_h_dark
                ? $upload_logo_h_dark->temporaryUrl()
                : $logo_h_dark_path;
        @endphp

        <div class="mb-3">
            <div class="d-flex justify-content-center align-items-center p-4 rounded-3" style="min-height:140px; background:#0b1220;">
                <img src="{{ $srcHDark }}" alt="Vista previa logotipo horizontal oscuro" style="max-height:90px; max-width:100%; object-fit:contain;"/>
            </div>
            <small class="text-muted">Se generan tamaños small/medium/large automáticamente para la variante <code>h_dark</code>.</small>
        </div>

        @slot('actions')
            <div class="row">
                <div class="col-12 text-end mb-4">
                    <x-vuexy-admin::button.basic type="submit" variant="primary" size="sm" icon="ti ti-device-floppy" class="btn-save mt-2 mr-2" no-waves
                        label="Guardar cambios"
                        disabled="{{ $upload_logo_h_dark === null }}" />
                    <x-vuexy-admin::button.basic variant="secondary" size="sm" icon="ti ti-rotate-2" class="btn-cancel mt-2 mr-2"
                        label="Cancelar"
                        wire:click="resetHorizontalDark"
                        disabled="{{ $upload_logo_h_dark === null }}" />
                </div>
            </div>
        @endslot
    </x-vuexy-admin::form.card-form>
</div>
