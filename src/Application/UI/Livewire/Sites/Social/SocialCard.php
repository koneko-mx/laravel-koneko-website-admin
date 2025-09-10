<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Social;

use Livewire\Component;
use Livewire\Attributes\Rule;
use Koneko\KonekoVuexyAdmin\Application\Settings\Manager\KonekoSettingManager;
use Koneko\KonekoWebsiteAdmin\Application\LocalModule as WebsiteModule;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

final class SocialCard extends Component
{
    public WebsiteSite $site;

    public string $targetNotify = '#website-social-settings-card .notification-container';

    private const GROUP     = 'layout';
    private const SECTION   = 'social';
    private const SUBGROUP  = 'links';
    private const DEFAULT_CC= 'MX'; // País por defecto para 10 dígitos sin prefijo

    // WhatsApp
    #[Rule('nullable|string|max:30')] // permitimos separadores visibles
    public string $whatsapp_phone = '';

    #[Rule('nullable|string|max:500')]
    public string $whatsapp_message = '';

    // URL lista para templates (se genera en save)
    public string $whatsapp = '';

    // Otros links (URLs completas normalizadas)
    #[Rule('nullable|string|max:300')] public string $facebook  = '';
    #[Rule('nullable|string|max:300')] public string $instagram = '';
    #[Rule('nullable|string|max:300')] public string $linkedin  = '';
    #[Rule('nullable|string|max:300')] public string $tiktok    = '';
    #[Rule('nullable|string|max:300')] public string $x_twitter = '';
    #[Rule('nullable|string|max:300')] public string $google    = '';
    #[Rule('nullable|string|max:300')] public string $pinterest = '';
    #[Rule('nullable|string|max:300')] public string $youtube   = '';
    #[Rule('nullable|string|max:300')] public string $vimeo     = '';

    public function mount(WebsiteSite $site): void
    {
        $this->site = $site;
        $this->loadForm();
    }

    private function settings(): KonekoSettingManager
    {
        return settings('website-admin')
            ->context(self::GROUP, self::SECTION, self::SUBGROUP)
            ->scope($this->site);
    }

    public function loadForm(): void
    {
        $data = $this->settings()->asArray()->all();

        // WhatsApp
        $this->whatsapp          = (string)($data['whatsapp'] ?? '');
        $this->whatsapp_phone    = (string)($data['whatsapp_phone'] ?? '');
        $this->whatsapp_message  = (string)($data['whatsapp_message'] ?? '');

        // Otros
        $this->facebook  = (string)($data['facebook'] ?? '');
        $this->instagram = (string)($data['instagram'] ?? '');
        $this->linkedin  = (string)($data['linkedin'] ?? '');
        $this->tiktok    = (string)($data['tiktok'] ?? '');
        $this->x_twitter = (string)($data['x_twitter'] ?? '');
        $this->google    = (string)($data['google'] ?? '');
        $this->pinterest = (string)($data['pinterest'] ?? '');
        $this->youtube   = (string)($data['youtube'] ?? '');
        $this->vimeo     = (string)($data['vimeo'] ?? '');

        if ($this->whatsapp_message === '') {
            $this->whatsapp_message = 'Hola 👋, vengo de {site}. Estoy viendo “{title}”. ¿Podrías ayudarme?';
        }
    }

    public function save(): void
    {
        // Limpia espacios al guardar (no re-formateamos el teléfono, se guarda “como lo escribió”)
        $this->whatsapp_phone   = trim($this->whatsapp_phone);
        $this->whatsapp_message = trim($this->whatsapp_message);

        // Validación (acepta separadores, valida sobre número limpio)
        $this->validate([
            'whatsapp_phone' => [
                'nullable','string','max:30',
                function ($attr, $value, $fail) {
                    if (!$this->phoneIsValid((string)$value)) {
                        $fail('Teléfono inválido. Usa E.164 (+…), 10 dígitos (MX/US/CA) o 1 + 10 (US/CA). Se permiten espacios, paréntesis, guiones y puntos.');
                    }
                }
            ],
            'whatsapp_message' => ['nullable','string','max:500'],
            'whatsapp'         => ['nullable','url'],
            'facebook'         => ['nullable','url'],
            'instagram'        => ['nullable','url'],
            'linkedin'         => ['nullable','url'],
            'tiktok'           => ['nullable','url'],
            'x_twitter'        => ['nullable','url'],
            'google'           => ['nullable','url'],
            'pinterest'        => ['nullable','url'],
            'youtube'          => ['nullable','url'],
            'vimeo'            => ['nullable','url'],
        ]);

        // Enlace wa.me (si podemos convertir a E.164)
        $e164 = $this->phoneToE164($this->whatsapp_phone, self::DEFAULT_CC);
        $this->whatsapp = $e164 ? $this->buildWaLinkFromE164($e164, $this->whatsapp_message) : '';

        // Persistencia
        $s = $this->settings();
        $s->set('whatsapp_phone',   $this->whatsapp_phone); // guardamos tal cual lo escribió
        $s->set('whatsapp_message', $this->whatsapp_message);
        $s->set('whatsapp',         $this->whatsapp);
        $s->set('facebook',  trim($this->facebook));
        $s->set('instagram', trim($this->instagram));
        $s->set('linkedin',  trim($this->linkedin));
        $s->set('tiktok',    trim($this->tiktok));
        $s->set('x_twitter', trim($this->x_twitter));
        $s->set('google',    trim($this->google));
        $s->set('pinterest', trim($this->pinterest));
        $s->set('youtube',   trim($this->youtube));
        $s->set('vimeo',     trim($this->vimeo));

        $this->dispatch('notification', target: $this->targetNotify, type: 'success', message: 'Se han guardado los cambios.');
        $this->dispatch('site-social-updated', id: $this->site->id);
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->loadForm();
        $this->dispatch('notification', target: $this->targetNotify, type: 'info', message: 'Cambios descartados.');
    }

    public function render()
    {
        return view('koneko-website-admin::livewire.sites.social.social-card');
    }

    /* ---------------- Helpers ---------------- */

    /** Quita separadores visibles, conserva + si existe, normaliza 00→+ y corrige +521…→+52… */
    private function phoneClean(string $raw): string
    {
        $v = trim($raw);
        // normaliza 00 prefijo internacional
        if (str_starts_with($v, '00')) {
            $v = '+' . substr($v, 2);
        }
        // elimina separadores visuales
        $v = preg_replace('/[\s().-]+/', '', $v);
        // asegura un solo '+' al inicio si el usuario lo repitió
        if (str_contains($v, '+')) {
            $v = '+' . ltrim($v, '+');
        }
        // corrige MX legados +521xxxxxxxxxx → +52xxxxxxxxxx
        $v = preg_replace('/^\+521(\d{10})$/', '+52$1', $v);
        return $v;
    }

    /** Valida E.164 o formatos locales MX/US/CA (10 dígitos / 1+10) sobre el número limpio */
    private function phoneIsValid(string $raw): bool
    {
        $s = $this->phoneClean($raw);
        if ($s === '' || $s === '+') return true;             // vacío es opcional
        if (preg_match('/^\+[1-9]\d{7,14}$/', $s)) return true; // E.164 global
        if (preg_match('/^\d{10}$/', $s)) return true;          // 10 dígitos (MX/US/CA)
        if (preg_match('/^1\d{10}$/', $s)) return true;         // US/CA con 1
        return false;
    }

    /** Convierte a E.164 si es posible; si son 10 dígitos, aplica país por defecto (MX/US/CA) */
    private function phoneToE164(string $raw, string $default = 'MX'): ?string
    {
        $s = $this->phoneClean($raw);
        if ($s === '' || $s === '+') return null;

        if (str_starts_with($s, '+')) {
            // ya viene con CC
            return $s;
        }

        if (preg_match('/^\d{10}$/', $s)) {
            return match (strtoupper($default)) {
                'US','CA' => '+1'  . $s,
                default   => '+52' . $s, // MX por defecto
            };
        }

        if (preg_match('/^1\d{10}$/', $s)) {
            return '+' . $s; // US/CA
        }

        // no convertible con nuestras reglas
        return null;
    }

    /** wa.me exige dígitos sin +; el mensaje va URL-encoded (con macros tal cual) */
    private function buildWaLinkFromE164(string $e164, string $message): string
    {
        $digits = ltrim($e164, '+');
        $msg    = rawurlencode($message ?: '');
        return $digits ? ('https://wa.me/' . $digits . ($msg ? ('?text=' . $msg) : '')) : '';
    }
}
