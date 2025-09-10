<div>
    <x-vuexy-admin::table.bootstrap.manager :tagName="$tagName" :datatableConfig="$bt_datatable" noFilterButtons>
        <x-slot name="tools">
            <div class="mb-4 pr-2">
                <x-vuexy-admin::button.index-offcanvas :label="$singularName" :tagName="$tagName" />
                <input type="hidden" name="site_id" value="{{ $site->id }}">
            </div>

            <div class="col-md-3 pr-2" style="width: 135px">
                <x-vuexy-admin::form.select name="status" :options="$statusOptions" placeholder="[Estatus]" class="form-select select2" />
            </div>

        </x-slot>
    </x-vuexy-admin::table.bootstrap.manager>
</div>

@push('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            $('#bt-website-contents .bt-btn-refresh').on('click', () => {
                $("#bt-website-contents .bootstrap-table").bootstrapTable("refresh");
            });

            /*
            $('.select2').select2({
                placeholder: 'Estatus',
            });
            */
        });
    </script>
@endpush
