<div>
    <x-vuexy-admin::form.card-form
        id="website-template-card"
        title="Plantilla del sitio"
        subtitle="Elige la plantilla y el color de tema"
        showActions
        wire:submit.prevent="save"
    >
        <div class="row">
            <div class="col-12 col-md-5">
                <x-vuexy-admin::form.input
                    model="theme_color"
                    type="color"
                    size="lg"
                    icon="ti ti-color-swatch"
                    label="Color del sitio"
                />
                <small class="text-muted block">Usa un color adecuado para la barra de navegador.</small>
                @error('theme_color')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-md-7">
                <label for="template" class="form-label">Plantilla</label>
                <select id="template" name="template" wire:model="template" class="form-select" data-choices data-choices-search-true>
                    <option value="">Seleccione una plantilla</option>
                    @foreach ($template_list as $group => $templates)
                        <optgroup label="{{ $group }}">
                            @foreach ($templates['items'] as $key => $tpl)
                                <option value="{{ $key }}">{{ $tpl }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('template')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Estado actual / resumen --}}
        <div class="mt-3 d-flex align-items-center gap-2">
            <span class="badge bg-label-primary">Actual: {{ $template ?: '—' }}</span>
            <span class="badge" style="background-color: {{ $theme_color }};">&nbsp;</span>
            <span>{{ $theme_color }}</span>
        </div>
    </x-vuexy-admin::form.card-form>
</div>

@push('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Inicializar formularios de Visibilidad y seguridad
            window.websiteTemplateForm = new formCustomListener({
                formSelector: '#website-template-card',
                buttonSelectors: ['.btn-save', '.btn-cancel'],
            });
        });
    </script>
@endpush
