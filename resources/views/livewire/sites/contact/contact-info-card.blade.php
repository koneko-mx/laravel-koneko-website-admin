<div x-data="{}" wire:key="contact-info-card-{{ $site->id }}">
    <x-vuexy-admin::form.card-form
        id="website-contact-info-card"
        title="Información de contacto"
        subtitle="Teléfonos, extensiones, correo y nota de horario"
        showActions
        wire:submit.prevent="save"
    >
        <x-vuexy-admin::form.input model="email" id="email" type="email" label="Correo electrónico" icon="ti ti-mail" placeholder="contacto@tu-dominio.com" autocomplete="off" />

        <div class="row">
            <div class="col-md-7">
                <x-vuexy-admin::form.input model="phone_number" id="phone_number" label="Número telefónico" icon="ti ti-phone" placeholder="+525512345678" autocomplete="off" />
            </div>
            <div class="col-md-5">
                <x-vuexy-admin::form.input model="phone_number_ext" id="phone_number_ext" label="Extensión" icon="ti ti-phone-plus" placeholder="Ext." autocomplete="off" />
            </div>
        </div>

        <div class="row">
            <div class="col-md-7">
                <x-vuexy-admin::form.input model="phone_number_2" id="phone_number_2" label="Número alternativo" icon="ti ti-phone" placeholder="+525512345679" autocomplete="off" />
            </div>
            <div class="col-md-5">
                <x-vuexy-admin::form.input model="phone_number_2_ext" id="phone_number_2_ext" label="Extensión²" icon="ti ti-phone-plus" placeholder="Ext." autocomplete="off" />
            </div>
        </div>

        <x-vuexy-admin::form.input model="working_hours" id="working_hours" label="Horario (texto corto)" icon="ti ti-clock" placeholder="Lun–Vie 9:00–18:00" autocomplete="off" />
    </x-vuexy-admin::form.card-form>
</div>

@push('page-script')
<script>
  document.addEventListener('livewire:init', () => {
    /* ========= Utils ========= */
    const q = (sel) => document.querySelector(sel);
    const clean       = (v) => (v || '').trim().replace(/[\s().-]/g, '');
    const onlyDigits  = (v) => (v || '').replace(/\D+/g, '');
    const isE164      = (s) => /^\+[1-9]\d{6,14}$/.test(s);     // +código + 7..15 dígitos
    const is10        = (s) => /^\d{10}$/.test(s);              // MX/US/CA local 10 dígitos
    const is1plus10   = (s) => /^1\d{10}$/.test(s);             // US/CA 1+10 sin '+'
    const isValidPhone = (raw) => {
      const s = clean(raw);
      if (!s) return true;                 // opcional
      if (s.startsWith('+')) return isE164(s);
      return is10(s) || is1plus10(s);
    };
    const normalizePhone = (raw) => {
      if (!raw) return raw;
      let s = clean(raw);
      if (/^\+521\d{10}$/.test(s)) s = s.replace(/^\+521/, '+52'); // normaliza legado
      return s;
    };

    // Normaliza en blur (opcional)
    ['#phone_number', '#phone_number_2'].forEach((sel) => {
      const el = q(sel);
      el?.addEventListener('blur', () => { el.value = normalizePhone(el.value); });
    });

    // ====== Validador de extensión (incluye “requiere número”) ======
    const validateExtAgainst = (phoneSelector) => (inputEl) => {
      const ext = onlyDigits(inputEl.value);
      if (!ext) return true;                                // ext vacía = OK
      if (!/^\d{1,10}$/.test(ext)) {
        return { valid: false, message: 'Extensión inválida (1–10 dígitos).' };
      }
      const phoneHasValue = !!clean(q(phoneSelector)?.value);
      if (!phoneHasValue) {
        return { valid: false, message: 'Agrega un número para usar extensión.' };
      }
      return true;
    };

    // ====== Instancia del listener + reglas ======
    window.ContactInfoSettingsForm = new formCustomListener({
      formSelector: '#website-contact-info-card',
      buttonSelectors: ['.btn-save', '.btn-cancel'],
      dispatchOnSubmit: 'save',
      fieldsValidation: {
        email: {
          validators: {
            emailAddress: { message: 'Correo inválido.' },
            stringLength: { max: 254, message: 'Máx. 254 caracteres.' },
          }
        },
        phone_number: {
          validators: {
            callback: {
              message: 'Teléfono inválido. Usa 10 dígitos (MX/US/CA) o internacional con +.',
              callback: (i) => isValidPhone(i.value),
            }
          }
        },
        phone_number_ext: {
          validators: {
            callback: {
              // El mensaje final lo da validateExtAgainst si falla
              message: 'Extensión inválida.',
              callback: (i) => validateExtAgainst('#phone_number')(i),
            }
          }
        },
        phone_number_2: {
          validators: {
            callback: {
              message: 'Teléfono alterno inválido. Usa 10 dígitos o internacional con +.',
              callback: (i) => isValidPhone(i.value),
            }
          }
        },
        phone_number_2_ext: {
          validators: {
            callback: {
              message: 'Extensión inválida.',
              callback: (i) => validateExtAgainst('#phone_number_2')(i),
            }
          }
        },
        working_hours: {
          validators: {
            stringLength: { max: 120, message: 'Máx. 120 caracteres.' }
          }
        },
      }
    });

    // Revalidar la extensión cuando cambia su número asociado (para ambos pares)
    const bounce = (fn, ms=80) => { let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a),ms); }; };
    const revalidateExt = bounce(() => {
      // dispara 'input' sobre las ext para que Trigger revalide ese campo
      q('#phone_number_ext')?.dispatchEvent(new Event('input', { bubbles: true }));
    });
    const revalidateExt2 = bounce(() => {
      q('#phone_number_2_ext')?.dispatchEvent(new Event('input', { bubbles: true }));
    });

    q('#phone_number')?.addEventListener('input', revalidateExt);
    q('#phone_number_2')?.addEventListener('input', revalidateExt2);
  });
</script>
@endpush
