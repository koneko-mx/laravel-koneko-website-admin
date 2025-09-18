<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Contact;

use Livewire\Component;
use Livewire\Attributes\Rule;
use Koneko\KonekoVuexyAdmin\Application\Settings\Manager\KonekoSettingManager;
use Koneko\KonekoWebsiteAdmin\Application\LocalModule as WebsiteModule;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

final class ContactInfoCard extends Component
{
    public WebsiteSite $site;

    public string $targetNotify = '#website-contact-info-card .notification-container';

    private const GROUP    = 'layout';
    private const SECTION  = 'contact';
    private const SUBGROUP = 'info';

    // Teléfonos (E.164 opcional) + extensión numérica opcional
    #[Rule('nullable|string|min:5|max:20')]
    public string $phone_number = '';

    #[Rule('nullable|string|min:1|max:10')]
    public string $phone_number_ext = '';

    #[Rule('nullable|string|min:5|max:20')]
    public string $phone_number_2 = '';

    #[Rule('nullable|string|min:1|max:10')]
    public string $phone_number_2_ext = '';

    // Email de contacto (opcional)
    #[Rule('nullable|string|max:254')]
    public string $email = '';

    // Texto de horario libre (opcional) — para horarios estructurados usa BusinessHoursCard
    #[Rule('nullable|string|max:120')]
    public string $working_hours = '';

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
        $this->phone_number      = (string)($data['phone_number'] ?? '');
        $this->phone_number_ext  = (string)($data['phone_number_ext'] ?? '');
        $this->phone_number_2    = (string)($data['phone_number_2'] ?? '');
        $this->phone_number_2_ext= (string)($data['phone_number_2_ext'] ?? '');
        $this->email             = (string)($data['email'] ?? '');
        $this->working_hours        = (string)($data['working_hours'] ?? '');
    }

    public function save(): void
    {
        // Mantén lo escrito para UI
        $display1 = trim($this->phone_number);
        $display2 = trim($this->phone_number_2);

        // Versión limpia para validar y construir tel:
        $clean1 = $this->normalizePhone($display1); // deja solo + y dígitos
        $clean2 = $this->normalizePhone($display2);
        $ext1   = $this->digitsOnly($this->phone_number_ext);
        $ext2   = $this->digitsOnly($this->phone_number_2_ext);

        $this->email        = strtolower(trim($this->email));
        $this->working_hours= trim($this->working_hours);

        $this->validate([
            // Acepta UI con espacios/guiones/paréntesis, y además valida semántica con callback
            'phone_number' => [
                'nullable','string','min:5','max:30','regex:/^[0-9+()\s\.-]+$/',
                function ($attr, $value, $fail) use ($clean1) {
                    if ($clean1 === '') return;
                    // válido si E.164 (+7..15), o 10 dígitos (MX/US/CA), o 1+10 (US/CA)
                    if (!preg_match('/^\+[1-9]\d{6,14}$/', $clean1) &&
                        !preg_match('/^\d{10}$/', $clean1) &&
                        !preg_match('/^1\d{10}$/', $clean1)) {
                        $fail('Teléfono inválido. Usa internacional con + o 10 dígitos (MX/US/CA) / 1+10 (US/CA).');
                    }
                },
            ],
            'phone_number_ext'   => ['nullable','regex:/^\d{1,10}$/'],
            'phone_number_2'     => [
                'nullable','string','min:5','max:30','regex:/^[0-9+()\s\.-]+$/',
                function ($attr, $value, $fail) use ($clean2) {
                    if ($clean2 === '') return;
                    if (!preg_match('/^\+[1-9]\d{6,14}$/', $clean2) &&
                        !preg_match('/^\d{10}$/', $clean2) &&
                        !preg_match('/^1\d{10}$/', $clean2)) {
                        $fail('Teléfono alterno inválido. Usa internacional con + o 10 dígitos / 1+10.');
                    }
                },
            ],
            'phone_number_2_ext' => ['nullable','regex:/^\d{1,10}$/'],
            'email'              => ['nullable','email:rfc','max:254'],
            'working_hours'      => ['nullable','string','max:120'],
        ], [
            'phone_number.regex'       => 'Caracteres inválidos. Usa dígitos, +, espacios, guiones, paréntesis o puntos.',
            'phone_number_2.regex'     => 'Caracteres inválidos. Usa dígitos, +, espacios, guiones, paréntesis o puntos.',
            'phone_number_ext.regex'   => 'Extensión inválida (1–10 dígitos).',
            'phone_number_2_ext.regex' => 'Extensión inválida (1–10 dígitos).',
        ]);

        // Construye href (tel:) sin tocar lo visible
        $href1 = $this->buildTelHref($clean1, $ext1);
        $href2 = $this->buildTelHref($clean2, $ext2);

        // Persistencia: guarda UI y href por separado
        $s = $this->settings();
        $s->set('phone_number',         $display1);
        $s->set('phone_number_ext',     $ext1);
        $s->set('phone_number_href',    $href1);

        $s->set('phone_number_2',       $display2);
        $s->set('phone_number_2_ext',   $ext2);
        $s->set('phone_number_2_href',  $href2);

        $s->set('email',                $this->email);
        $s->set('working_hours',        $this->working_hours);

        $this->dispatch('notification', target: $this->targetNotify, type: 'success', message: 'Se han guardado los cambios.');
        $this->dispatch('site-contact-info-updated', id: $this->site->id);
    }

    /** Construye tel: limpio; si hay extensión, la agrega como ;ext=123 */
    private function buildTelHref(?string $clean, ?string $ext): string
    {
        $n = trim((string)$clean);
        if ($n === '') return '';

        // Si es 10 dígitos, puedes prefijar +52 (MX) o dejar local:
        // Aquí no imponemos; usamos lo que venga (E.164 o local) según tu normalización.
        $tel = 'tel:' . $n;

        $e = trim((string)$ext);
        if ($e !== '') {
            // RFC3966 recomienda ;ext=, algunos discan con ,, (pausa). Dejamos ;ext=
            $tel .= ';ext=' . $e;
        }
        return $tel;
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->loadForm();
        $this->dispatch('notification', target: $this->targetNotify, type: 'info', message: 'Cambios descartados.');
    }

    public function render()
    {
        return view('koneko-website-admin::livewire.sites.contact.contact-info-card');
    }

    /* Helpers */
    private function normalizePhone(string $raw): string
    {
        $v = preg_replace('/[^0-9+]/', '', trim($raw));
        if (str_starts_with($v, '00')) $v = '+' . substr($v, 2);
        return $v;
    }
    private function digitsOnly(string $raw): string
    {
        return preg_replace('/\D+/', '', trim($raw));
    }
}
