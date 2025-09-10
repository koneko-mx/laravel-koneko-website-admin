<div x-data="{}" wire:key="social-card-{{ $site->id }}">
    <x-vuexy-admin::form.card-form
      id="website-social-settings-card"
      title="Redes sociales"
      subtitle="Enlaces listos para tus templates (Navbar/Footer/Cards)"
      showActions
      wire:submit.prevent="save"
    >
      <div class="row">
        <div class="col-md-6">
          {{-- WhatsApp --}}
          <x-vuexy-admin::form.input model="whatsapp_phone" id="whatsapp_phone" label="WhatsApp (teléfono E.164)" icon="ti ti-brand-whatsapp" placeholder="+525512345678" autocomplete="off" />
          <x-vuexy-admin::form.input model="whatsapp_message" id="whatsapp_message" label="Saludo WhatsApp (opcional)" placeholder="Hola 👋, vengo de {site}…" autocomplete="off" />
          <div class="form-text">Macros: <code>{site}</code>, <code>{title}</code>, <code>{url}</code>. Se codifican en el enlace.</div>

          {{-- Facebook / Instagram / LinkedIn --}}
          <x-vuexy-admin::form.input model="facebook"  id="facebook"  label="Facebook"  icon="ti ti-brand-facebook"  placeholder="https://facebook.com/tu-pagina o @tu-pagina" />
          <x-vuexy-admin::form.input model="instagram" id="instagram" label="Instagram" icon="ti ti-brand-instagram" placeholder="https://instagram.com/tu-usuario o @tu-usuario" />
          <x-vuexy-admin::form.input model="linkedin"  id="linkedin"  label="LinkedIn"  icon="ti ti-brand-linkedin"  placeholder="https://linkedin.com/in/tu-usuario o @tu-usuario" />
        </div>

        <div class="col-md-6">
          {{-- X(Twitter) / TikTok --}}
          <x-vuexy-admin::form.input model="x_twitter" id="x_twitter" label="X (Twitter)" icon="ti ti-brand-twitter" placeholder="https://x.com/tu-usuario o @tu-usuario" />
          <x-vuexy-admin::form.input model="tiktok"    id="tiktok"    label="TikTok"     icon="ti ti-brand-tiktok"   placeholder="https://tiktok.com/@tu-usuario o @tu-usuario" />

          {{-- Google Business / Pinterest --}}
          <x-vuexy-admin::form.input model="google"    id="google"    label="Google"     icon="ti ti-brand-google"    placeholder="https://maps.app.goo.gl/... o perfil" />
          <x-vuexy-admin::form.input model="pinterest" id="pinterest" label="Pinterest"  icon="ti ti-brand-pinterest" placeholder="https://pinterest.com/tu-usuario o @tu-usuario" />

          {{-- YouTube / Vimeo --}}
          <x-vuexy-admin::form.input model="youtube"   id="youtube"   label="YouTube"    icon="ti ti-brand-youtube"   placeholder="https://youtube.com/@tu-usuario" />
          <x-vuexy-admin::form.input model="vimeo"     id="vimeo"     label="Vimeo"      icon="ti ti-brand-vimeo"     placeholder="https://vimeo.com/tu-usuario" />
        </div>
      </div>

      <div class="mt-2">
        <small class="text-muted">La tarjeta normaliza handles a URLs (ej. <code>@miusuario</code> → <code>https://instagram.com/miusuario</code>) y genera el enlace <code>wa.me</code> con tu saludo.</small>
      </div>
    </x-vuexy-admin::form.card-form>
</div>

@push('page-script')
<script>
document.addEventListener('livewire:init', () => {
  /* ============== Utils comunes ============== */
  const q = (sel) => document.querySelector(sel);
  const trim = (v) => (v||'').trim();

  // Teléfono: permite separadores ()-. y espacios; valida E.164 o MX/US/CA 10 dígitos / 1+10
  const cleanPhone = (v) => trim(v).replace(/[\s().-]+/g, '');
  const isE164     = (s) => /^\+[1-9]\d{7,14}$/.test(s);
  const is10       = (s) => /^\d{10}$/.test(s);       // MX/US/CA local
  const is1plus10  = (s) => /^1\d{10}$/.test(s);      // US/CA con 1 al inicio
  const isValidWhatsapp = (raw) => {
    const s = cleanPhone(raw);
    if (!s) return true;               // vacío = ok (opcional)
    if (s.startsWith('+')) return isE164(s);
    return is10(s) || is1plus10(s);    // local sin + (MX/US/CA)
  };

  // Acepta URL (con o sin esquema) contra host permitido, o @handle según regex
  const acceptUrlOrHandle = (raw, { host, handle }) => {
    const v = trim(raw);
    if (!v) return true;
    if (handle && handle.test(v)) return true;
    // Permitir URL con o sin esquema
    const urlOk = new RegExp('^(https?:\\/\\/)?([\\w.-]+)\\.' + host + '(\\/.*)?$', 'i');
    if (urlOk.test(v)) return true;
    // Permitir exactamente el host sin subdominio + path (p.ej. instagram.com/user)
    const bareOk = new RegExp('^(https?:\\/\\/)?(www\\.)?' + host + '(\\/.*)?$', 'i');
    if (bareOk.test(v)) return true;
    return false;
  };

  // Normaliza a URL canónica en blur (si es handle o dominio sin esquema)
  const normalizeTo = (input, fn) => {
    if (!input) return;
    input.addEventListener('blur', () => {
      const v = trim(input.value);
      if (!v) return;
      input.value = fn(v);
      // dispara eventos para habilitar botones
      input.dispatchEvent(new Event('input',  { bubbles: true }));
      input.dispatchEvent(new Event('change', { bubbles: true }));
      const form = input.closest('form');
      form?.dispatchEvent(new Event('change', { bubbles: true }));
    });
  };

  // Generadores de URL por red (si entra @handle o dominio sin esquema)
  const toHttps = (v) => /^https?:\/\//i.test(v) ? v : 'https://' + v.replace(/^\/+/, '');
  const normMaps = {
    facebook:  (v) => v.startsWith('@') ? `https://facebook.com/${v.slice(1)}`  : toHttps(v),
    instagram: (v) => v.startsWith('@') ? `https://instagram.com/${v.slice(1)}` : toHttps(v),
    linkedin:  (v) => v.startsWith('@') ? `https://linkedin.com/in/${v.slice(1)}` : toHttps(v),
    x_twitter: (v) => v.startsWith('@') ? `https://x.com/${v.slice(1)}`         : v.replace(/https?:\/\/twitter\.com/i,'https://x.com').replace(/^http:\/\//i,'https://'),
    tiktok:    (v) => v.startsWith('@') ? `https://tiktok.com/@${v.slice(1)}`   : toHttps(v),
    pinterest: (v) => v.startsWith('@') ? `https://pinterest.com/${v.slice(1)}` : toHttps(v),
    youtube:   (v) => {
      if (v.startsWith('@')) return `https://youtube.com/${v}`;
      // youtu.be → https://youtube.com/watch?v=...
      if (/^https?:\/\/youtu\.be\//i.test(v)) return v.replace(/^https?:\/\/youtu\.be\//i, 'https://youtube.com/watch?v=');
      return toHttps(v);
    },
    vimeo:     (v) => v.startsWith('@') ? `https://vimeo.com/${v.slice(1)}`     : toHttps(v),
    google:    (v) => toHttps(v),
  };

  // Reglas de cada red (host base y regex de handle)
  const rules = {
    facebook:  { host: '(facebook\\.com|m\\.facebook\\.com|fb\\.me)', handle: /^@?[A-Za-z0-9.\-]{5,50}$/ },
    instagram: { host: 'instagram\\.com',  handle: /^@?[A-Za-z0-9._]{1,30}$/ },
    linkedin:  { host: 'linkedin\\.com',   handle: /^@?[A-Za-z0-9\-]{3,100}$/ },
    x_twitter: { host: '(x\\.com|twitter\\.com)', handle: /^@?[A-Za-z0-9_]{1,15}$/ },
    tiktok:    { host: 'tiktok\\.com',     handle: /^@?[A-Za-z0-9._]{2,24}$/ },
    pinterest: { host: 'pinterest\\.com',  handle: /^@?[A-Za-z0-9._-]{3,50}$/ },
    youtube:   { host: '(youtube\\.com|youtu\\.be)', handle: /^@[\w.]{3,30}$/ },
    vimeo:     { host: 'vimeo\\.com',      handle: /^@?[A-Za-z0-9._-]{3,100}$/ },
    google:    { host: '(google\\.[\\w.]+|maps\\.app\\.goo\\.gl|goo\\.gl|share\\.google)', handle: null }, // solo URL
  };

  // ====== Instancia listener + validación ======
  window.SocialSettingsForm = new formCustomListener({
    formSelector:    '#website-social-settings-card',
    buttonSelectors: ['.btn-save', '.btn-cancel'],
    dispatchOnSubmit: 'save',
    fieldsValidation: {
      // WhatsApp
      whatsapp_phone: {
        validators: {
          callback: {
            message: 'Teléfono inválido. Usa E.164 (+52…) o 10 dígitos (MX/US/CA). Se permiten (), -, . y espacios.',
            callback: (i) => isValidWhatsapp(i.value),
          }
        }
      },
      whatsapp_message: {
        validators: {
          stringLength: { max: 500, message: 'Máx. 500 caracteres.' }
        }
      },

      // Redes: URL del dominio correcto O @handle
      facebook:  { validators: { callback: { message: 'URL de Facebook o @handle.',  callback: (i)=> acceptUrlOrHandle(i.value, rules.facebook)  } } },
      instagram: { validators: { callback: { message: 'URL de Instagram o @handle.', callback: (i)=> acceptUrlOrHandle(i.value, rules.instagram) } } },
      linkedin:  { validators: { callback: { message: 'URL de LinkedIn o @usuario.', callback: (i)=> acceptUrlOrHandle(i.value, rules.linkedin)  } } },
      x_twitter: { validators: { callback: { message: 'URL de X/Twitter o @handle.', callback: (i)=> acceptUrlOrHandle(i.value, rules.x_twitter) } } },
      tiktok:    { validators: { callback: { message: 'URL de TikTok o @handle.',    callback: (i)=> acceptUrlOrHandle(i.value, rules.tiktok)    } } },
      pinterest: { validators: { callback: { message: 'URL de Pinterest o @handle.', callback: (i)=> acceptUrlOrHandle(i.value, rules.pinterest) } } },
      youtube:   { validators: { callback: { message: 'URL de YouTube o @handle.',   callback: (i)=> acceptUrlOrHandle(i.value, rules.youtube)   } } },
      vimeo:     { validators: { callback: { message: 'URL de Vimeo o @handle.',     callback: (i)=> acceptUrlOrHandle(i.value, rules.vimeo)     } } },
      google:    { validators: { callback: { message: 'URL de Google/Maps válida.',  callback: (i)=> acceptUrlOrHandle(i.value, rules.google)    } } },
    }
  });

  // ====== Normalización en blur a URL canónica ======
  normalizeTo(q('#facebook'),  (v)=> normMaps.facebook(v));
  normalizeTo(q('#instagram'), (v)=> normMaps.instagram(v));
  normalizeTo(q('#linkedin'),  (v)=> normMaps.linkedin(v));
  normalizeTo(q('#x_twitter'), (v)=> normMaps.x_twitter(v));
  normalizeTo(q('#tiktok'),    (v)=> normMaps.tiktok(v));
  normalizeTo(q('#pinterest'), (v)=> normMaps.pinterest(v));
  normalizeTo(q('#youtube'),   (v)=> normMaps.youtube(v));
  normalizeTo(q('#vimeo'),     (v)=> normMaps.vimeo(v));
  normalizeTo(q('#google'),    (v)=> normMaps.google(v));

  const wa = document.getElementById('whatsapp_phone');
  wa?.addEventListener('blur', () => {
    let s = cleanPhone(wa.value);
    if (!s) return;
    if (/^\+521\d{10}$/.test(s)) s = s.replace(/^\+521/, '+52'); // MX legado
    // NO imponemos + ni formateamos más: solo validamos; guardas tal cual lo escribió
    wa.dispatchEvent(new Event('input',  { bubbles:true }));
    wa.dispatchEvent(new Event('change', { bubbles:true }));
    wa.closest('form')?.dispatchEvent(new Event('change', { bubbles:true }));
  });
});
</script>
@endpush
