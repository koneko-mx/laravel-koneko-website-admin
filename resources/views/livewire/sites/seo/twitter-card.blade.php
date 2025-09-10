<div
  x-data="{
    isSite: {{ $isSite ? 'true' : 'false' }},
    mode: @entangle('twitter_mode').live,
    hasUpload: {{ $upload_twitter_image ? 'true' : 'false' }},
    get isEnabled(){ return this.mode === 'override' },
    get isInherit(){ return !this.isSite && this.mode === 'inherit' },
    get showEditor(){ return this.isEnabled },
    get showProcessor(){ return this.showEditor && this.hasUpload },
    init(){
      const input = this.$refs.twUpload;
      if (input) {
        this.hasUpload = !!(input.files && input.files.length);
        input.addEventListener('change', () => {
          this.hasUpload = !!(input.files && input.files.length);
        });
      }
      // Eventos de Livewire (se emiten en document)
      document.addEventListener('livewire-upload-finish', () => { this.hasUpload = true; });
      document.addEventListener('livewire-upload-error',  () => { this.hasUpload = false; });
      document.addEventListener('livewire-upload-cancel', () => { this.hasUpload = false; });
    }
  }"
>
    <x-vuexy-admin::form.card-form
        id="website-twitter-card"
        class="form-custom-listener"
        title="Twitter Card (X)"
        subtitle="Metadatos para previsualización en Twitter/X"
        linkHref="https://cards-dev.twitter.com/validator"
        linkText="Abrir Card Validator"
    >
        {{-- Selector de modo --}}
        <x-koneko-website-admin::form.mode-toggle
            model="twitter_mode"
            group="tw"
            :scope="$scope"
            :value="$twitter_mode"
            {{-- el contenedor padre define dónde togglear --}}
            data-scope="#website-twitter-card"
            data-show-when-enable=".display-enabled"
            data-show-when-inherit=".display-inherited"
        />

        {{-- Mensaje modo "heredar" (solo Content). Solo JS, no Blade --}}
        <div class="alert alert-secondary py-2 mb-3 small" x-show="isInherit" x-cloak>
            Esta tarjeta hereda la configuración del sitio. No hay campos editables aquí.
        </div>

        {{-- Editor completo (solo cuando está habilitado/override).
            Además, lo envolvemos en <fieldset> para deshabilitar inputs si hiciera falta. --}}
        <fieldset :disabled="!showEditor" x-show="showEditor" x-cloak>
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-6">
                <x-vuexy-admin::form.select
                    model="twitter_card"
                    label="Tipo de card"
                    :options="$cardOptions"
                    no-placeholder />
                </div>

                <div class="col-md-6">
                <x-vuexy-admin::form.input model="twitter_url" label="URL canónica (opcional)" placeholder="https://…" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                <x-vuexy-admin::form.input model="twitter_site" label="Cuenta del sitio (@opcional)" maxlength="30" placeholder="@sitio" />
                </div>

                @unless($isSite)
                <div class="col-md-4">
                    <x-vuexy-admin::form.input model="twitter_creator" label="Creador (autor) (@opcional)" maxlength="30" placeholder="@autor" />
                </div>
                @endunless
            </div>

            {{-- Imagen (URL o Upload) --}}
            <div class="row">
                <div class="col-md-6">
                    <x-vuexy-admin::form.input model="twitter_image" label="Imagen (URL absoluta)" placeholder="https://…" />
                    <div class="text-muted small mt-1">
                    Recomendado: 800×418 para <code>summary_large_image</code> (≤2 MB).
                    </div>
                </div>
                <div class="col-md-6">
                    <x-vuexy-admin::form.input
                    type="file"
                    model="upload_twitter_image"
                    accept="image/png,image/jpeg,image/webp"
                    label="o subir imagen" />
                    @error('upload_twitter_image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Preview (si hay URL o archivo) --}}
            <div class="row">
                <div class="col-12">
                @php
                    $preview = $upload_twitter_image ? $upload_twitter_image->temporaryUrl() : ($twitter_image ?: null);
                @endphp
                @if($preview)
                    <div class="mt-2 p-3 rounded border d-flex justify-content-center" style="background:#0b1220;">
                    <img src="{{ $preview }}" alt="Vista previa Twitter" style="max-width:100%; max-height:240px; object-fit:contain;">
                    </div>
                @endif
                </div>
            </div>

            {{-- Procesador de imagen: SOLO visible si está habilitado y hay archivo seleccionado (no solo URL) --}}
            @if ($twitter_mode === 'override' && $upload_twitter_image)
                <x-vuexy-admin::media.image-processor
                    mode-prop="twitter_mode"
                    fit-prop="image_fit"
                    target-aspect-prop="target_aspect"
                    pixel-area-prop="pixel_area"
                    format-prop="image_format"
                    quality-prop="image_quality"
                    source-aspect-prop="source_aspect" />
            @endif

            <div class="alert alert-info py-2 mt-2 small">
                Sugerido <strong>summary_large_image</strong> (800×418).
                En <em>Conservar aspecto</em>, el campo “Personalizada” se bloquea y refleja el aspecto del archivo.
            </div>
        </fieldset>

        @slot('actions')
            <div class="row">
                <div class="col-12 text-end mb-4">
                    <x-vuexy-admin::button.basic type="submit" variant="primary" size="sm" icon="ti ti-device-floppy" class="btn-save mt-2 mr-2" no-waves
                        label="Guardar cambios"
                        disabled="{{ $upload_twitter_image === null }}" />
                    <x-vuexy-admin::button.basic variant="secondary" size="sm" icon="ti ti-rotate-2" class="btn-cancel mt-2 mr-2"
                        label="Cancelar"
                        wire:click="resetForm"
                        disabled="{{ $upload_twitter_image === null }}" />
                </div>
            </div>
        @endslot
    </x-vuexy-admin::form.card-form>
</div>

@push('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new formCustomListener({
                formSelector: '#website-twitter-card',
                dispatchOnSubmit: 'save',
                fieldsValidation: {
                    twitter_image: { validators: { callback: {
                        message: 'Debe ser URL absoluta',
                        callback: i => !i.value || /^https?:\/\/\S+$/i.test((i.value||'').trim())
                    }}},
                    twitter_url:   { validators: { callback: {
                        message: 'Debe ser URL absoluta',
                        callback: i => !i.value || /^https?:\/\/\S+$/i.test((i.value||'').trim())
                    }}},
                    twitter_site:   { validators: { stringLength: { max: 30, message: 'Máximo 30 caracteres' } } },
                    twitter_creator:{ validators: { stringLength: { max: 30, message: 'Máximo 30 caracteres' } } },
                }
            });
        });
    </script>
@endpush
