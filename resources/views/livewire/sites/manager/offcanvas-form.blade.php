<div>
    <x-vuexy-admin::offcanvas.basic :id="$offcanvasId" :tag-name="$tagName">
        <x-vuexy-admin::form :uid="$uniqueId" :id="$formId" :mode="$mode" wireSubmit="onSubmit">
            <x-slot name="actions">
                <x-vuexy-admin::button.offcanvas-buttons :mode="$mode" :tagName="$tagName" />
            </x-slot>
            {{-- Sitio web --}}
            <x-vuexy-admin::form.input :uid="$uniqueId" model="domain" label="Nombre de dominio" />
            <x-vuexy-admin::form.input :uid="$uniqueId" model="title" label="Titulo del sitio" />
            <hr>
            <x-vuexy-admin::form.checkbox :uid="$uniqueId" model="robots_mode" switch label="Evita que el sitio sea indexado" />
            <x-vuexy-admin::form.checkbox :uid="$uniqueId" model="www_redirect" switch label="Redirigir sitio sin www" />
            <x-vuexy-admin::form.checkbox :uid="$uniqueId" model="force_https" switch label="Forzar HTTPS en el sitio"/>
        </x-vuexy-admin::form>
    </x-vuexy-admin::offcanvas.basic>
</div>

@push('page-script')
    <script>
        // Evento para inicializar el formulario cuando se carga la página
        document.addEventListener("DOMContentLoaded", function () {
            const initializeWebsiteForm = () => {

            };

            var myOffcanvas = document.getElementById('{{ $offcanvasId }}');

            myOffcanvas.addEventListener('show.bs.offcanvas', function () {
                initializeWebsiteForm();
            });
        });
    </script>
@endpush
