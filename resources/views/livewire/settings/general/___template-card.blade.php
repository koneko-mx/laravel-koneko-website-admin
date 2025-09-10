<div>
    <form id="website-template-settings-card" novalidate="novalidate">
        <div class="card">
            <div class="card-body">
                <h5>Porto Template 12.0.0</h5>
                <div class="mb-4">
                    <x-form.checkbox
                        name='website_tpl_style_switcher'
                        wire:model.defer='website_tpl_style_switcher'
                        parent_class='form-switch'>
                        Mostrar personalizador de estilos
                    </x-form.checkbox>
                </div>
                <div class="mb-4 fv-row">
                    <label for="website_tpl_footer_text" class="form-label">Titulo de pie de página</label>
                    <input type="text" id="website_tpl_footer_text" name="website_tpl_footer_text" wire:model='website_tpl_footer_text' class="form-control" placeholder="Titulo de pie de página">
                    @error("website_tpl_footer_text")
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        <div>
            {{-- Botones --}}
            <div class="row my-4">
                <div class="col-12 text-end mb-4">
                    <button
                    type="submit"
                        class="btn btn-primary btn-save btn-sm mt-2 mr-2 waves-effect waves-light"
                        disabled
                        data-loading-text="Guardando...">
                        <i class="ti ti-device-floppy mr-2"></i>
                        Guardar cambios
                    </button>
                    <button
                        type="button"
                        wire:click="loadSettings"
                        class="btn btn-secondary btn-cancel btn-sm mt-2 mr-2 waves-effect waves-light"
                        disabled>
                        <i class="ti ti-rotate-2 mr-2"></i>
                        Cancelar
                    </button>
                </div>
            </div>
            {{-- Notifications --}}
            <div class="notification-container" wire:ignore></div>
        </div>
    </form>
</div>
