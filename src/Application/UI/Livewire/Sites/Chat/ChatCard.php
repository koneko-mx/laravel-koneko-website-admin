<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Chat;

use Livewire\Component;
use Livewire\Attributes\Rule;
use Koneko\KonekoVuexyAdmin\Application\Settings\Manager\KonekoSettingManager;
use Koneko\KonekoWebsiteAdmin\Application\LocalModule as WebsiteModule;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

final class ChatCard extends Component
{
    public WebsiteSite $site;

    public string $targetNotify = '#website-chat-settings-card .notification-container';

    private const GROUP   = 'layout';
    private const SECTION = 'chat';

    // --- Estado unificado ---
    #[Rule('required|in:none,whatsapp,crisp,tawkto,tidio,livechat')]
    public string $chat_provider = 'none';

    // --- WhatsApp ---
    #[Rule('nullable|string|min:8|max:20')]
    public string $wa_phone = '';

    #[Rule('nullable|string|min:3|max:200')]
    public string $wa_greeting = '';

    #[Rule('nullable|string|max:32')]
    public string $wa_button_text = '';

    #[Rule('nullable|string|in:left,right')]
    public string $wa_position = 'right';

    #[Rule('nullable|string|max:7')]
    public string $wa_theme = '#25D366';

    // --- Crisp ---
    #[Rule('nullable|string|min:10|max:60')]
    public string $crisp_website_id = '';

    // --- Tawk.to ---
    #[Rule('nullable|string|min:10|max:64')]
    public string $tawk_property_id = '';

    #[Rule('nullable|string|min:4|max:32')]
    public string $tawk_widget_id = 'default';

    // --- Tidio ---
    #[Rule('nullable|string|min:8|max:64')]
    public string $tidio_public_key = '';

    // --- LiveChat ---
    #[Rule('nullable|string|min:4|max:12')]
    public string $livechat_license = '';

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
        // default
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
    }

    private function providerMeta(string $p): ?array
    {
        return match ($p) {
            'whatsapp' => ['href' => 'https://wa.me/', 'text' => 'Formato de enlace wa.me'],
            'crisp'    => ['href' => 'https://app.crisp.chat/', 'text' => 'Abrir Crisp'],
            'tawkto'   => ['href' => 'https://dashboard.tawk.to/', 'text' => 'Abrir Tawk.to'],
            'tidio'    => ['href' => 'https://www.tidio.com/panel/', 'text' => 'Abrir Tidio'],
            'livechat' => ['href' => 'https://my.livechatinc.com/', 'text' => 'Abrir LiveChat'],
            default    => null,
        };
    }

    public function getProviderLinkProperty(): ?array
    {
        return $this->providerMeta($this->chat_provider);
    }

    public function save(): void
    {
        // Normaliza comunes
        $this->chat_provider   = in_array($this->chat_provider, ['whatsapp','crisp','tawkto','tidio','livechat','none'], true) ? $this->chat_provider : 'none';
        $this->wa_phone        = str_replace([' ', '(', ')', '-', '.'], '', trim($this->wa_phone));
        $this->wa_greeting     = trim($this->wa_greeting);
        $this->wa_button_text  = trim($this->wa_button_text);
        $this->wa_position     = $this->wa_position === 'left' ? 'left' : 'right';
        $this->wa_theme        = strtoupper(trim($this->wa_theme));
        $this->crisp_website_id = trim($this->crisp_website_id);
        $this->tawk_property_id  = trim($this->tawk_property_id);
        $this->tawk_widget_id    = trim($this->tawk_widget_id);
        $this->tidio_public_key  = trim($this->tidio_public_key);
        $this->livechat_license  = trim($this->livechat_license);

        // Reglas condicionales
        $rules = [ 'chat_provider' => ['required','in:none,whatsapp,crisp,tawkto,tidio,livechat'] ];
        $messages = [];

        switch ($this->chat_provider) {
            case 'whatsapp':
                $rules += [
                    'wa_phone'       => ['required','string','regex:/^[+]?[1-9][0-9]{7,14}$/'],
                    'wa_greeting'    => ['required','string','min:3','max:200'],
                    'wa_button_text' => ['nullable','string','max:32'],
                    'wa_position'    => ['required','in:left,right'],
                    'wa_theme'       => ['nullable','string','regex:/^#(?:[A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
                ];
                $messages += [
                    'wa_phone.regex' => 'Usa formato internacional E.164, ej. +525512345678.',
                    'wa_theme.regex' => 'Color inválido (#RGB o #RRGGBB).',
                ];
                break;

            case 'crisp':
                $rules += [
                    // UUID v4 típico
                    'crisp_website_id' => ['required','string','regex:/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/'],
                ];
                $messages += [
                    'crisp_website_id.regex' => 'Website ID inválido (UUID).',
                ];
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
                $messages += [
                    'tidio_public_key.regex' => 'Public Key inválida (8-64 alfanum).',
                ];
                break;

            case 'livechat':
                $rules += [
                    'livechat_license' => ['required','string','regex:/^\d{4,12}$/'],
                ];
                $messages += [
                    'livechat_license.regex' => 'License ID inválido (4-12 dígitos).',
                ];
                break;
        }

        $this->validate($rules, $messages);

        // Persistencia
        $this->settings('default')->set('chat_provider', $this->chat_provider);

        if ($this->chat_provider === 'whatsapp') {
            $s = $this->settings('whatsapp');
            $s->set('wa_phone', $this->wa_phone);
            $s->set('wa_greeting', $this->wa_greeting);
            $s->set('wa_button_text', $this->wa_button_text);
            $s->set('wa_position', $this->wa_position);
            $s->set('wa_theme', $this->wa_theme);
        } elseif ($this->chat_provider === 'crisp') {
            $s = $this->settings('crisp');
            $s->set('crisp_website_id', $this->crisp_website_id);
        } elseif ($this->chat_provider === 'tawkto') {
            $s = $this->settings('tawkto');
            $s->set('tawk_property_id', $this->tawk_property_id);
            $s->set('tawk_widget_id', $this->tawk_widget_id);
        } elseif ($this->chat_provider === 'tidio') {
            $s = $this->settings('tidio');
            $s->set('tidio_public_key', $this->tidio_public_key);
        } elseif ($this->chat_provider === 'livechat') {
            $s = $this->settings('livechat');
            $s->set('livechat_license', $this->livechat_license);
        }

        $this->dispatch('notification', target: $this->targetNotify, type: 'success', message: 'Se han guardado los cambios.');
        //$this->dispatch('site-chat-updated', id: $this->site->id, provider: $this->chat_provider);
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
}
