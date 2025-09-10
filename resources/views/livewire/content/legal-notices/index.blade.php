<div>
    <x-vuexy-admin::form.form id="website-legal-notices-settings-card" class="form-custom-listener mb-4" wire:ignore.self>
        <x-vuexy-admin::card.basic title="Avisos Legales" class="mb-2">
            {{-- Selector de sección --}}
            <ul class="nav nav-pills" role="tablist">
                @foreach($legalVars as $key => $section)
                    <li class="nav-item">
                        <button
                            type="button"
                            class="nav-link @if($currentSection === $key) active @endif"
                            onclick="@this.currentSection = '{{ $key }}';"
                            role="tab"
                            data-bs-toggle="tab"
                            data-bs-target="#{{ $key }}-nav"
                            aria-controls="{{ $key }}-nav"
                            aria-selected="@if($currentSection === $key) true @else false @endif">
                            {{ $section['title'] }}
                        </button>
                    </li>
                @endforeach
            </ul>
            <div class="tab-content">
                @foreach($legalVars as $key => $section)
                    <div class="tab-pane fade @if($currentSection === $key) show active @endif" id="{{ $key }}-nav" role="tabpanel">
                        {{-- Habilitar sección --}}
                        <x-vuexy-admin::form.checkbox
                            model="legalVars.{{ $key }}.enabled"
                            label="Habilitar sección"
                            switch />

                        {{-- Editor de contenido --}}
                        <x-vuexy-admin::form.textarea
                            model="legalVars.{{ $key }}.content"
                            label="Contenido"
                            switch
                            required />
                    </div>
                @endforeach
            </div>
        </x-vuexy-admin::card.basic>

        {{-- Botones de acción --}}
        <div class="row">
            <div class="col-12 text-end mb-4">
                <x-vuexy-admin::button.basic
                    type="submit"
                    variant="primary"
                    size="sm"
                    icon="ti ti-device-floppy"
                    label="Guardar cambios"
                    disabled
                    class="btn-save mt-2 mr-2"
                    waves
                    data-loading-text="Guardando..." />

                <x-vuexy-admin::button.basic
                    variant="secondary"
                    size="sm"
                    icon="ti ti-rotate-2"
                    label="Cancelar"
                    wire:click="loadSettings"
                    class="btn-cancel mt-2 mr-2"
                    waves />
            </div>
        </div>

        {{-- Contenedor para notificaciones --}}
        <div class="notification-container mb-4" wire:ignore></div>
    </x-vuexy-admin::form.form>
</div>
