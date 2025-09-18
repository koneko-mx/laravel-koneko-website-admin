<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Chat;

use Livewire\Component;
use Livewire\Attributes\Rule;
use Koneko\KonekoVuexyAdmin\Application\Settings\Manager\KonekoSettingManager;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

final class ChatCard extends Component
{
    public WebsiteSite $site;

    public string $targetNotify = '#website-chat-settings-card .notification-container';

    private const GROUP   = 'layout';
    private const SECTION = 'chat';

    // -----------------------------------------
    // Proveedor activo
    // -----------------------------------------
    #[Rule('required|in:none,whatsapp,crisp,tawkto,tidio,livechat,intercom,chatwoot,rocketchat,papercups,livehelperchat')]
    public string $chat_provider = 'none';

    // -----------------------------------------
    // WhatsApp
    // -----------------------------------------
    #[Rule('nullable|string|max:30')]
    public string $wa_phone = '';

    #[Rule('nullable|string|min:3|max:200')]
    public string $wa_greeting = '';

    #[Rule('nullable|string|max:32')]
    public string $wa_button_text = '';

    #[Rule('nullable|string|in:left,right')]
    public string $wa_position = 'right';

    #[Rule('nullable|string|max:7')]
    public string $wa_theme = '#25D366';

    // -----------------------------------------
    // Crisp (Freemium)
    // -----------------------------------------
    #[Rule('nullable|string|min:10|max:60')]
    public string $crisp_website_id = '';

    // -----------------------------------------
    // Tawk.to (Freemium)
    // -----------------------------------------
    #[Rule('nullable|string|min:10|max:64')]
    public string $tawk_property_id = '';

    #[Rule('nullable|string|min:4|max:32')]
    public string $tawk_widget_id = 'default';

    // -----------------------------------------
    // Tidio (Freemium)
    // -----------------------------------------
    #[Rule('nullable|string|min:8|max:64')]
    public string $tidio_public_key = '';

    // -----------------------------------------
    // LiveChat (SaaS pago)
    // -----------------------------------------
    #[Rule('nullable|string|min:4|max:12')]
    public string $livechat_license = '';

    // -----------------------------------------
    // Intercom (SaaS pago)
    // -----------------------------------------
    #[Rule('nullable|string|min:5|max:32')]
    public string $intercom_app_id = '';

    #[Rule('nullable|string|in:left,right')]
    public string $intercom_alignment = 'right';

    // -----------------------------------------
    // Chatwoot (Self-Hosted)
    // -----------------------------------------
    #[Rule('nullable|url:http,https|max:200')]
    public string $chatwoot_base_url = '';

    #[Rule('nullable|string|min:10|max:64')]
    public string $chatwoot_website_token = '';

    #[Rule('nullable|string|in:left,right')]
    public string $chatwoot_position = 'right';

    #[Rule('nullable|string|max:7')]
    public string $chatwoot_brand_color = '#1F93FF';

    // -----------------------------------------
    // Rocket.Chat (Self-Hosted)
    // -----------------------------------------
    #[Rule('nullable|url:http,https|max:200')]
    public string $rocketchat_base_url = ''; // ej. https://chat.example.com

    #[Rule('nullable|string|min:0|max:64')]
    public string $rocketchat_department = '';

    // -----------------------------------------
    // Papercups (Self-Hosted)
    // -----------------------------------------
    #[Rule('nullable|url:http,https|max:200')]
    public string $papercups_base_url = 'https://app.papercups.io';

    #[Rule('nullable|string|min:10|max:60')]
    public string $papercups_account_id = '';

    // -----------------------------------------
    // Live Helper Chat (Self-Hosted)
    // -----------------------------------------
    #[Rule('nullable|url:http,https|max:300')]
    public string $lhc_embed_script_url = ''; // URL completa al JS generado por LHC

    public function mount(WebsiteSite $site): void
    {
        $this->site = $site;
        $this->loadForm();
    }

    private function settings(string $subgroup): KonekoSettingManager
    {
        return settings('website-admin')
            ->context(self::GROUP, self::SECTION, $subgroup)
            ->scope($this->site);
    }

    public function loadForm(): void
    {
        // Default
        $default = $this->settings('default')->asArray()->all();
        $this->chat_provider = (string)($default['chat_provider'] ?? 'none');

        // WhatsApp
        $wa = $this->settings('whatsapp')->asArray()->all();
        $this->wa_phone       = (string)($wa['wa_phone'] ?? '');
        $this->wa_greeting    = (string)($wa['wa_greeting'] ?? '');
        $this->wa_button_text = (string)($wa['wa_button_text'] ?? '');
        $this->wa_position    = (string)($wa['wa_position'] ?? 'right');
        $this->wa_theme       = (string)($wa['wa_theme'] ?? '#25D366');
        if ($this->wa_greeting === '') {
            $this->wa_greeting = 'Hola 👋, vengo de {site}. Estoy viendo “{title}”. ¿Podrías ayudarme?';
        }

        // Crisp
        $cr = $this->settings('crisp')->asArray()->all();
        $this->crisp_website_id = (string)($cr['crisp_website_id'] ?? '');

        // Tawk.to
        $tw = $this->settings('tawkto')->asArray()->all();
        $this->tawk_property_id = (string)($tw['tawk_property_id'] ?? '');
        $this->tawk_widget_id   = (string)($tw['tawk_widget_id'] ?? 'default');

        // Tidio
        $td = $this->settings('tidio')->asArray()->all();
        $this->tidio_public_key = (string)($td['tidio_public_key'] ?? '');

        // LiveChat
        $lc = $this->settings('livechat')->asArray()->all();
        $this->livechat_license = (string)($lc['livechat_license'] ?? '');

        // Intercom
        $ic = $this->settings('intercom')->asArray()->all();
        $this->intercom_app_id   = (string)($ic['intercom_app_id'] ?? '');
        $this->intercom_alignment = (string)($ic['intercom_alignment'] ?? 'right');

        // Chatwoot
        $cw = $this->settings('chatwoot')->asArray()->all();
        $this->chatwoot_base_url     = (string)($cw['chatwoot_base_url'] ?? '');
        $this->chatwoot_website_token= (string)($cw['chatwoot_website_token'] ?? '');
        $this->chatwoot_position     = (string)($cw['chatwoot_position'] ?? 'right');
        $this->chatwoot_brand_color  = (string)($cw['chatwoot_brand_color'] ?? '#1F93FF');

        // Rocket.Chat
        $rc = $this->settings('rocketchat')->asArray()->all();
        $this->rocketchat_base_url  = (string)($rc['rocketchat_base_url'] ?? '');
        $this->rocketchat_department= (string)($rc['rocketchat_department'] ?? '');

        // Papercups
        $pc = $this->settings('papercups')->asArray()->all();
        $this->papercups_base_url  = (string)($pc['papercups_base_url'] ?? 'https://app.papercups.io');
        $this->papercups_account_id= (string)($pc['papercups_account_id'] ?? '');

        // Live Helper Chat
        $lhc = $this->settings('livehelperchat')->asArray()->all();
        $this->lhc_embed_script_url = (string)($lhc['lhc_embed_script_url'] ?? '');
    }

    private function providerMeta(string $p): ?array
    {
        return match ($p) {
            'whatsapp'      => ['href' => 'https://wa.me/',                         'text' => 'Formato wa.me'],
            'crisp'         => ['href' => 'https://crisp.chat/',                    'text' => 'Abrir Crisp'],
            'tawkto'        => ['href' => 'https://www.tawk.to/',                   'text' => 'Abrir Tawk.to'],
            'tidio'         => ['href' => 'https://www.tidio.com/',                 'text' => 'Abrir Tidio'],
            'livechat'      => ['href' => 'https://www.livechat.com/',              'text' => 'Abrir LiveChat'],
            'intercom'      => ['href' => 'https://www.intercom.com/',              'text' => 'Abrir Intercom'],
            'chatwoot'      => ['href' => 'https://www.chatwoot.com/',              'text' => 'Abrir Chatwoot'],
            'rocketchat'    => ['href' => 'https://www.rocket.chat/',               'text' => 'Abrir Rocket.Chat'],
            'papercups'     => ['href' => 'https://papercups.io/',                  'text' => 'Abrir Papercups'],
            'livehelperchat'=> ['href' => 'https://livehelperchat.com/',            'text' => 'Abrir Live Helper Chat'],
            default         => null,
        };
    }

    public function getProviderLinkProperty(): ?array
    {
        return $this->providerMeta($this->chat_provider);
    }

    public function save(): void
    {
        // -----------------------------------------
        // 1) Normalización
        // -----------------------------------------
        $allowed = ['none','whatsapp','crisp','tawkto','tidio','livechat','intercom','chatwoot','rocketchat','papercups','livehelperchat'];

        if(in_array($this->chat_provider, $allowed) == false)
            $this->chat_provider = 'none';

        // Trim / sanitize
        $this->wa_phone        = trim((string)$this->wa_phone);
        $this->wa_greeting     = trim((string)$this->wa_greeting);
        $this->wa_button_text  = trim((string)$this->wa_button_text);
        $this->wa_position     = $this->wa_position === 'left' ? 'left' : 'right';
        $this->wa_theme        = $this->hexOrNull($this->wa_theme);            // '' -> null, upper

        $this->crisp_website_id = trim($this->crisp_website_id);

        $this->tawk_property_id = trim($this->tawk_property_id);
        $this->tawk_widget_id   = trim($this->tawk_widget_id ?: 'default');

        $this->tidio_public_key = trim($this->tidio_public_key);

        $this->livechat_license = trim($this->livechat_license);

        $this->intercom_app_id    = strtolower(trim($this->intercom_app_id));
        $this->intercom_alignment = $this->intercom_alignment === 'left' ? 'left' : 'right';

        $this->chatwoot_base_url      = trim($this->chatwoot_base_url);
        $this->chatwoot_website_token = trim($this->chatwoot_website_token);
        $this->chatwoot_position      = $this->chatwoot_position === 'left' ? 'left' : 'right';
        $this->chatwoot_brand_color   = $this->hexOrNull($this->chatwoot_brand_color); // '' -> null, upper

        $this->rocketchat_base_url   = trim($this->rocketchat_base_url);
        $this->rocketchat_department = trim($this->rocketchat_department);

        $this->papercups_base_url   = trim($this->papercups_base_url ?: 'https://app.papercups.io');
        $this->papercups_account_id = trim($this->papercups_account_id);

        $this->lhc_embed_script_url = trim($this->lhc_embed_script_url);

        // -----------------------------------------
        // 2) Validación (atributos + condicional)
        // -----------------------------------------
        // Reglas base definidas por atributos #[Rule(...)]
        $this->validate();

        // Reglas condicionales por proveedor
        $rules = [ 'chat_provider' => ['required','in:'.implode(',', $allowed)] ];
        $messages = [];

        switch ($this->chat_provider) {
            case 'whatsapp':
                $rules += [
                    'wa_phone'       => ['required','string','max:30', function($attr,$value,$fail){
                        if (!$this->waPhoneIsValid((string)$value)) {
                            $fail('Teléfono inválido. Usa E.164 (+…), 10 dígitos (MX/US/CA) o 1+10 (US/CA). Se permiten espacios, paréntesis, guiones y puntos.');
                        }
                    }],
                    'wa_greeting'    => ['required','string','min:3','max:200'],
                    'wa_button_text' => ['nullable','string','max:32'],
                    'wa_position'    => ['required','in:left,right'],
                    // OJO: ya convertimos ''→null, por lo que nullable funciona bien
                    'wa_theme'       => ['nullable','string','regex:/^#(?:[A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
                ];
                $messages += ['wa_theme.regex' => 'Color inválido (#RGB o #RRGGBB).'];
                break;

            case 'crisp':
                $rules += [
                    'crisp_website_id' => ['required','string','regex:/^[0-9a-fA-F-]{36,}$/'],
                ];
                $messages += ['crisp_website_id.regex' => 'Website ID inválido (UUID).'];
                break;

            case 'tawkto':
                $rules += [
                    'tawk_property_id' => ['required','string','regex:/^[A-Za-z0-9-]{10,64}$/'],
                    'tawk_widget_id'   => ['required','string','regex:/^(default|[A-Za-z0-9-]{4,32})$/'],
                ];
                $messages += [
                    'tawk_property_id.regex' => 'Property ID inválido (10-64, letras/números/guion).',
                    'tawk_widget_id.regex'   => 'Widget ID inválido ("default" o 4-32).',
                ];
                break;

            case 'tidio':
                $rules += [
                    'tidio_public_key' => ['required','string','regex:/^[A-Za-z0-9]{8,64}$/'],
                ];
                $messages += ['tidio_public_key.regex' => 'Public Key inválida (8-64 alfanum).'];
                break;

            case 'livechat':
                $rules += [
                    'livechat_license' => ['required','string','regex:/^\d{4,12}$/'],
                ];
                $messages += ['livechat_license.regex' => 'License ID inválido (4-12 dígitos).'];
                break;

            case 'intercom':
                $rules += [
                    'intercom_app_id'    => ['required','string','regex:/^[a-z0-9_-]{5,32}$/'],
                    'intercom_alignment' => ['required','in:left,right'],
                ];
                $messages += ['intercom_app_id.regex' => 'app_id inválido (min 5, letras/números/guion-bajo).'];
                break;

            case 'chatwoot':
                $rules += [
                    'chatwoot_base_url'      => ['required','url:http,https','max:200'],
                    'chatwoot_website_token' => ['required','string','regex:/^[A-Za-z0-9_-]{10,64}$/'],
                    'chatwoot_position'      => ['required','in:left,right'],
                    'chatwoot_brand_color'   => ['nullable','string','regex:/^#(?:[A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
                ];
                $messages += [
                    'chatwoot_website_token.regex' => 'Website token inválido (10-64 alfanum, -/_).',
                    'chatwoot_brand_color.regex'   => 'Color inválido (#RGB o #RRGGBB).',
                ];
                break;

            case 'rocketchat':
                $rules += [
                    'rocketchat_base_url'   => ['required','url:http,https','max:200'],
                    'rocketchat_department' => ['nullable','string','regex:/^[A-Za-z0-9_-]{0,64}$/'],
                ];
                $messages += ['rocketchat_department.regex' => 'Departamento inválido (letras/números/guion/guion bajo).'];
                break;

            case 'papercups':
                $rules += [
                    'papercups_base_url'   => ['required','url:http,https','max:200'],
                    'papercups_account_id' => ['required','string','regex:/^[0-9a-fA-F-]{36,}$/'],
                ];
                $messages += ['papercups_account_id.regex' => 'accountId inválido (UUID).'];
                break;

            case 'livehelperchat':
                $rules += [
                    'lhc_embed_script_url' => ['required','url:http,https','max:300'],
                ];
                break;
        }

        $this->validate($rules, $messages);

        // -----------------------------------------
        // 3) Persistencia
        // -----------------------------------------
        // Proveedor activo (si deshabilitas, guarda 'none')
        $this->settings('default')->set('chat_provider', $this->chat_provider);

        // Persistencia por proveedor (conserva las otras configs, no las borres)
        match ($this->chat_provider) {
            'whatsapp' => (function () {
                $s = $this->settings('whatsapp');
                $s->set('wa_phone', $this->waPhoneClean($this->wa_phone));        // normalizado
                $s->set('wa_greeting', $this->wa_greeting);
                $s->set('wa_button_text', $this->wa_button_text ?: null);
                $s->set('wa_position', $this->wa_position);
                $s->set('wa_theme', $this->wa_theme ?? '#25D366');                 // si null, default
            })(),
            'crisp' => (function () {
                $this->settings('crisp')->set('crisp_website_id', $this->crisp_website_id);
            })(),
            'tawkto' => (function () {
                $s = $this->settings('tawkto');
                $s->set('tawk_property_id', $this->tawk_property_id);
                $s->set('tawk_widget_id', $this->tawk_widget_id ?: 'default');
            })(),
            'tidio' => (function () {
                $this->settings('tidio')->set('tidio_public_key', $this->tidio_public_key);
            })(),
            'livechat' => (function () {
                $this->settings('livechat')->set('livechat_license', $this->livechat_license);
            })(),
            'intercom' => (function () {
                $s = $this->settings('intercom');
                $s->set('intercom_app_id', $this->intercom_app_id);
                $s->set('intercom_alignment', $this->intercom_alignment);
            })(),
            'chatwoot' => (function () {
                $s = $this->settings('chatwoot');
                $s->set('chatwoot_base_url', $this->chatwoot_base_url);
                $s->set('chatwoot_website_token', $this->chatwoot_website_token);
                $s->set('chatwoot_position', $this->chatwoot_position);
                $s->set('chatwoot_brand_color', $this->chatwoot_brand_color ?? '#1F93FF');
            })(),
            'rocketchat' => (function () {
                $s = $this->settings('rocketchat');
                $s->set('rocketchat_base_url', $this->rocketchat_base_url);
                $s->set('rocketchat_department', $this->rocketchat_department ?: null);
            })(),
            'papercups' => (function () {
                $s = $this->settings('papercups');
                $s->set('papercups_base_url', $this->papercups_base_url);
                $s->set('papercups_account_id', $this->papercups_account_id);
            })(),
            'livehelperchat' => (function () {
                $this->settings('livehelperchat')->set('lhc_embed_script_url', $this->lhc_embed_script_url);
            })(),
            default => null, // 'none'
        };

        // -----------------------------------------
        // 4) Fin: notificación
        // -----------------------------------------
        $this->dispatch('notification', target: $this->targetNotify, type: 'success', message: 'Se han guardado los cambios.');
    }


    public function resetForm(): void
    {
        $this->resetValidation();
        $this->loadForm();
        $this->dispatch('notification', target: $this->targetNotify, type: 'info', message: 'Cambios descartados.');
    }

    public function render()
    {
        return view('koneko-website-admin::livewire.sites.chat.chat-card');
    }

    private function hexOrNull(?string $v): ?string
    {
        $v = strtoupper(trim((string)$v));
        return $v === '' ? null : $v; // permite nullable + regex sin fallar por string vacío
    }

    private function waPhoneClean(string $raw): string
    {
        $v = trim($raw);
        if (str_starts_with($v, '00')) {
            $v = '+' . substr($v, 2);
        }
        $v = preg_replace('/[\s().-]+/', '', $v);
        if (str_contains($v, '+')) {
            $v = '+' . ltrim($v, '+');
        }
        // corrige MX legado
        $v = preg_replace('/^\+521(\d{10})$/', '+52$1', $v);
        return $v;
    }

    private function waPhoneIsValid(string $raw): bool
    {
        $s = $this->waPhoneClean($raw);
        if ($s === '' || $s === '+') return false;
        if (preg_match('/^\+[1-9]\d{7,14}$/', $s)) return true; // E.164
        if (preg_match('/^\d{10}$/', $s)) return true;          // MX/US/CA
        if (preg_match('/^1\d{10}$/', $s)) return true;         // US/CA con 1
        return false;
    }
}
