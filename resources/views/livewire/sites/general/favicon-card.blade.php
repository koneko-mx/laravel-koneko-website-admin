<div>
    <x-vuexy-admin::form.card-form
        id="website-favicon-card"
        title="Favicon"
        subtitle="Sube una imagen y previsualiza tamaños comunes"
        showActions
        wire:submit.prevent="save"
    >
        <x-vuexy-admin::form.input type="file" label="Icono de navegador" model="upload_image_favicon" accept="image/png,image/jpeg,image/webp" />

        @php
            $src = fn($path) => \Illuminate\Support\Str::startsWith($path, '../')
                ? asset($path)
                : asset('storage/'.$path);
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="text-center mx-auto">
                <div class="image-wrapper-16x16 d-flex justify-content-center align-items-center mx-auto bg-light rounded">
                    <img src="{{ $upload_image_favicon ? $upload_image_favicon->temporaryUrl() : $src($website_favicon_16x16) }}" alt="16x16" style="width:16px;height:16px;image-rendering:pixelated;" />
                </div>
                <small class="text-muted d-block mt-1">Navegadores (16×16)</small>
            </div>
            <div class="text-center mx-auto">
                <div class="image-wrapper-76x76 d-flex justify-content-center align-items-center mx-auto bg-light rounded">
                    <img src="{{ $upload_image_favicon ? $upload_image_favicon->temporaryUrl() : $src($website_favicon_76x76) }}" alt="76x76" style="width:76px;height:76px;" />
                </div>
                <small class="text-muted d-block mt-1">iPad sin Retina (76×76)</small>
            </div>
            <div class="text-center mx-auto">
                <div class="image-wrapper-120x120 d-flex justify-content-center align-items-center mx-auto bg-light rounded">
                    <img src="{{ $upload_image_favicon ? $upload_image_favicon->temporaryUrl() : $src($website_favicon_120x120) }}" alt="120x120" style="width:120px;height:120px;" />
                </div>
                <small class="text-muted d-block mt-1">iPhone (120×120)</small>
            </div>
            <div class="text-center mx-auto">
                <div class="image-wrapper-152x152 d-flex justify-content-center align-items-center mx-auto bg-light rounded">
                    <img src="{{ $upload_image_favicon ? $upload_image_favicon->temporaryUrl() : $src($website_favicon_152x152) }}" alt="152x152" style="width:152px;height:152px;" />
                </div>
                <small class="text-muted d-block mt-1">iPad (152×152)</small>
            </div>
            <div class="text-center mx-auto">
                <div class="image-wrapper-180x180 d-flex justify-content-center align-items-center mx-auto bg-light rounded">
                    <img src="{{ $upload_image_favicon ? $upload_image_favicon->temporaryUrl() : $src($website_favicon_180x180) }}" alt="180x180" style="width:180px;height:180px;" />
                </div>
                <small class="text-muted d-block mt-1">iPhone Retina HD (180×180)</small>
            </div>
            <div class="text-center mx-auto">
                <div class="image-wrapper-192x192 d-flex justify-content-center align-items-center mx-auto bg-light rounded">
                    <img src="{{ $upload_image_favicon ? $upload_image_favicon->temporaryUrl() : $src($website_favicon_192x192) }}" alt="192x192" style="width:192px;height:192px;" />
                </div>
                <small class="text-muted d-block mt-1">Android / PWA (192×192)</small>
            </div>
        </div>

        @slot('actions')
            <div class="row">
                <div class="col-12 text-end mb-4">
                    <x-vuexy-admin::button.basic type="submit" variant="primary" size="sm" icon="ti ti-device-floppy" class="btn-save mt-2 mr-2" no-waves
                        label="Guardar cambios"
                        disabled="{{ $upload_image_favicon === null }}" />
                    <x-vuexy-admin::button.basic variant="secondary" size="sm" icon="ti ti-rotate-2" class="btn-cancel mt-2 mr-2"
                        label="Cancelar"
                        wire:click="resetForm"
                        disabled="{{ $upload_image_favicon === null }}" />
                </div>
            </div>
        @endslot
    </x-vuexy-admin::form.card-form>
</div>
