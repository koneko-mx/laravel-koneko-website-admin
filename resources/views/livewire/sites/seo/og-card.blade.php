<div
  x-data="{
    isSite: {{ $isSite ? 'true' : 'false' }},
    mode: @entangle('og_mode').live,
    hasUpload: {{ $upload_og_image ? 'true' : 'false' }},

    // Derivados de UI
    get isEnabled(){ return this.mode === 'override' },
    get isInherit(){ return !this.isSite && this.mode === 'inherit' },
    get showEditor(){ return this.isEnabled },

    init(){
      // Sincroniza hasUpload con el <input type=file>
      const input = this.$refs.ogUpload;
      if (input) {
        this.hasUpload = !!(input.files && input.files.length);
        input.addEventListener('change', () => {
          this.hasUpload = !!(input.files && input.files.length);
        });
      }
      // Eventos globales Livewire (cuando termina/errores/cancelación del tmp upload)
      document.addEventListener('livewire-upload-finish', () => { this.hasUpload = true; });
      document.addEventListener('livewire-upload-error',  () => { this.hasUpload = false; });
      document.addEventListener('livewire-upload-cancel', () => { this.hasUpload = false; });
    }
  }"
>
    <x-vuexy-admin::form.card-form
        id="website-og-card"
        class="form-custom-listener"
        title="Open Graph"
        subtitle="Metadatos sociales para Facebook/WhatsApp/LinkedIn"
        linkHref="https://developers.facebook.com/tools/debug/"
        linkText="Abrir Sharing Debugger"
    >
        {{-- Selector de modo --}}
        <x-koneko-website-admin::form.mode-toggle
            model="og_mode"
            group="og"
            :scope="$scope"
            :value="$og_mode"
            {{-- el contenedor padre define dónde togglear --}}
            data-scope="#website-og-card"
            data-show-when-enable=".display-enabled"
            data-show-when-inherit=".display-inherited"
        />

        {{-- Mensaje modo "heredar" (solo Content) --}}
        <div class="display-inherited">
            <div class="alert alert-secondary py-2 mb-3 small" x-show="isInherit" x-cloak>
                Esta tarjeta hereda la configuración del sitio. No hay campos editables aquí.
            </div>
        </div>

        {{-- Editor completo solo cuando está habilitado/override --}}
        <div class="display-enabled">
            <fieldset>
                {{-- Básicos --}}
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-4">
                    <x-vuexy-admin::form.select
                        model="og_type"
                        label="og:type"
                        :options="[
                        'website' => 'website',
                        'article' => 'article',
                        'profile' => 'profile',
                        'product' => 'product'
                        ]"
                        placeholder="(opcional)" />
                    </div>
                    <div class="col-12 col-md-8">
                    <x-vuexy-admin::form.input model="og_url" label="URL (absoluta, opcional)" placeholder="https://…" />
                    </div>
                </div>

                {{-- Imagen (URL o Upload) --}}
                <div class="row g-3 mt-0">
                    <div class="col-md-6">
                    <x-vuexy-admin::form.input model="og_image" label="Imagen (URL absoluta)" placeholder="https://…" />
                    <div class="text-muted small mt-1">Recomendado: <strong>1200×630</strong>, ≤2&nbsp;MB, JPG/PNG/WebP.</div>
                    </div>
                    <div class="col-md-6">
                    <x-vuexy-admin::form.input
                        type="file"
                        x-ref="ogUpload"
                        model="upload_og_image"
                        accept="image/png,image/jpeg,image/webp"
                        label="o subir imagen" />
                    @error('upload_og_image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Preview --}}
                <div class="row">
                    <div class="col-12">
                    @php
                        $preview = $upload_og_image ? $upload_og_image->temporaryUrl() : ($og_image ?: null);
                    @endphp
                    @if($preview)
                        <div class="mt-2 p-3 rounded border d-flex justify-content-center" style="background:#0b1220;">
                        <img src="{{ $preview }}" alt="Vista previa OG" style="max-width:100%; max-height:280px; object-fit:contain;">
                        </div>
                    @endif
                    </div>
                </div>

                {{-- Procesamiento de imagen:
                    Solo cuando está habilitado y hay archivo seleccionado (no solo URL) --}}
                @if ($og_mode === 'override' && $upload_og_image)
                    <x-vuexy-admin::media.image-processor
                    mode-prop="og_mode"
                    fit-prop="image_fit"
                    target-aspect-prop="target_aspect"
                    pixel-area-prop="pixel_area"
                    format-prop="image_format"
                    quality-prop="image_quality"
                    source-aspect-prop="source_aspect" />
                @endif

                <div class="alert alert-info py-2 mt-2 small">
                    Sugerido <strong>1200×630</strong> (≈1.91:1). En <em>Conservar aspecto</em> el campo “Personalizada” se bloquea y refleja el aspecto del archivo.
                </div>
            </fieldset>
        </div>

        @slot('actions')
            <div class="row">
                <div class="col-12 text-end mb-4">
                    <x-vuexy-admin::button.basic type="submit" variant="primary" size="sm" icon="ti ti-device-floppy" class="btn-save mt-2 mr-2" no-waves
                        label="Guardar cambios"
                        disabled="{{ $upload_og_image === null }}" />
                    <x-vuexy-admin::button.basic variant="secondary" size="sm" icon="ti ti-rotate-2" class="btn-cancel mt-2 mr-2"
                        label="Cancelar"
                        wire:click="resetForm"
                        disabled="{{ $upload_og_image === null }}" />
                </div>
            </div>
        @endslot
    </x-vuexy-admin::form.card-form>
</div>

@push('page-script')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    new formCustomListener({
      formSelector: '#website-og-card',
      dispatchOnSubmit: 'save',
      fieldsValidation: {
        og_image: { validators: { callback: {
          message: 'Debe ser URL absoluta',
          callback: i => !i.value || /^https?:\/\/\S+$/i.test((i.value||'').trim())
        }}},
        og_url:   { validators: { callback: {
          message: 'Debe ser URL absoluta',
          callback: i => !i.value || /^https?:\/\/\S+$/i.test((i.value||'').trim())
        }}},
      }
    });
  });
</script>
@endpush
