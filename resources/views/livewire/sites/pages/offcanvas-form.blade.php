@php
use Koneko\KonekoWebsiteAdmin\Application\Enums\WebsiteContents\{WebsiteContentType,WebsiteContentStatus};

$optionsType   = WebsiteContentType::options();
$optionsStatus = WebsiteContentStatus::optionsForForm();
@endphp

<div>
    <x-vuexy-admin::offcanvas.basic :id="$offcanvasId" :tag-name="$tagName">
      <x-vuexy-admin::form :uid="$uniqueId" :id="$formId" :mode="$mode" wireSubmit="onSubmit">
        <x-slot name="actions">
          <x-vuexy-admin::button.offcanvas-buttons :mode="$mode" :tagName="$tagName" />
        </x-slot>

        {{-- Metadatos / Campos principales --}}
        <div class="row">
          <div class="col-md-6">
            <x-vuexy-admin::form.select
              :uid="$uniqueId"
              model="type"
              label="Tipo de contenido"
              placeholder="Selecciona el tipo"
              :options="$optionsType"
            />
          </div>

          <div class="col-md-6">
            <x-vuexy-admin::form.select
              :uid="$uniqueId"
              model="status"
              label="Estatus"
              placeholder="Selecciona el estatus"
              :options="$optionsStatus"
            />
          </div>
        </div>

        <x-vuexy-admin::form.input
          :uid="$uniqueId"
          model="title"
          label="Título"
          placeholder="Ej. Política de Privacidad"
          maxlength="50"
          hint="Máx. 50 caracteres"
        />

        <x-vuexy-admin::form.input
          :uid="$uniqueId"
          model="slug"
          label="Slug"
          placeholder="ej. politica-de-privacidad"
          maxlength="64"
          hint="Sólo minúsculas, números y guiones"
        />

        <x-vuexy-admin::form.textarea
          :uid="$uniqueId"
          model="description"
          label="Descripción"
          placeholder="Resumen corto para SEO o listados…"
          rows="5"
          maxlength="160"
        >
          <small class="text-muted d-block mt-1">
            <span id="desc-count-{{ $uniqueId }}">0</span>/500
          </small>
        </x-vuexy-admin::form.textarea>

        <hr class="my-3">

        {{-- SEO flags --}}
        <div class="row">
          <div class="col-md-6">
            <x-vuexy-admin::form.checkbox
              :uid="$uniqueId"
              model="noindex"
              switch
              label="No index (excluir de motores de búsqueda)"
            />
          </div>
          <div class="col-md-6">
            <x-vuexy-admin::form.checkbox
              :uid="$uniqueId"
              model="nofollow"
              switch
              label="No follow (no seguir enlaces)"
            />
          </div>
        </div>
      </x-vuexy-admin::form>
    </x-vuexy-admin::offcanvas.basic>
  </div>

  @push('page-script')
  <script>
  document.addEventListener("DOMContentLoaded", function () {
    const formSel   = '#{{ $formId }}';
    const offcanvas = document.getElementById(@json($offcanvasId));

    // Livewire instance del componente dueño del form
    const lwRoot = document.querySelector(formSel)?.closest('[wire\\:id]');
    const lw     = lwRoot ? window.Livewire.find(lwRoot.getAttribute('wire:id')) : null;

    let slugManual = false;
    let debounceT  = null;

    const slugify = (str) => (str || '')
      .toString()
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
      .toLowerCase().trim()
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .substring(0, 64);

    // Delegación: sobrevive a re-renders
    const onInputDelegated = (e) => {
      const el = e.target;
      if (!el || !el.name) return;

      // Autogenerar slug desde title con debounce (si no es manual)
      if (el.name === 'title' && !slugManual) {
        clearTimeout(debounceT);
        debounceT = setTimeout(() => {
          const s = slugify(el.value);
          const slugEl = document.querySelector(`${formSel} input[name=slug]`);
          if (slugEl && slugEl.value !== s) {
            slugEl.value = s;        // <-- NO trigger('input')
            lw?.set('slug', s, false); // <-- sync Livewire sin loops
            @this.slug=s
          }
        }, 200);
      }

      // Si el usuario edita slug, normaliza y marca modo manual
      if (el.name === 'slug') {
        slugManual = true;
        const cur = el.selectionStart;
        const s = slugify(el.value);
        if (el.value !== s) {
          el.value = s;
          try { el.setSelectionRange(cur, cur); } catch (_) {}
        }
        lw?.set('slug', s, false); // <-- sync directo, sin trigger
      }

      // Contador de descripción (corrige tu /500 vs maxlength real)
      if (el.name === 'description') {
        const counter = document.getElementById('desc-count-{{ $uniqueId }}');
        if (counter) counter.textContent = (el.value || '').length;
      }
    };

    // Inicializa (una vez abierto el offcanvas)
    const boot = () => {
      // Si ya trae slug (modo edit), no lo sobreescribimos
      const slugEl = document.querySelector(`${formSel} input[name=slug]`);
      slugManual = !!(slugEl && slugEl.value);

      // Delegación global dentro del offcanvas (evita listeners duplicados)
      offcanvas.removeEventListener('input', onInputDelegated);
      offcanvas.addEventListener('input', onInputDelegated);

      // Contador inicial
      const desc = document.querySelector(`${formSel} textarea[name=description]`);
      const counter = document.getElementById('desc-count-{{ $uniqueId }}');
      if (desc && counter) counter.textContent = (desc.value || '').length;

      // Focus en title
      setTimeout(() => {
        document.querySelector(`${formSel} input[name=title]`)?.focus();
      }, 150);
    };

    offcanvas.addEventListener('show.bs.offcanvas', boot);

    // Si ya está visible (render directo)
    if (offcanvas.classList.contains('show')) boot();

    // Re-bind tras cada morph del componente (mantiene contador y delegación)
    window.Livewire?.hook?.('message.processed', (msg, comp) => {
      if (!lwRoot || comp.id !== lwRoot.getAttribute('wire:id')) return;
      if (offcanvas.classList.contains('show')) boot();
    });
  });
  </script>
  @endpush
