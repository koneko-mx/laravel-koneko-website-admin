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
        // Normaliza teléfonos (mantén + y dígitos) y extensión (solo dígitos)
        $this->phone_number      = $this->normalizePhone($this->phone_number);
        $this->phone_number_ext  = $this->digitsOnly($this->phone_number_ext);
        $this->phone_number_2    = $this->normalizePhone($this->phone_number_2);
        $this->phone_number_2_ext= $this->digitsOnly($this->phone_number_2_ext);
        $this->email             = strtolower(trim($this->email));
        $this->working_hours        = trim($this->working_hours);

        $this->validate([
            'phone_number'       => ['nullable','regex:/^[+]?[1-9][0-9]{4,19}$/'],
            'phone_number_ext'   => ['nullable','regex:/^\d{1,10}$/'],
            'phone_number_2'     => ['nullable','regex:/^[+]?[1-9][0-9]{4,19}$/'],
            'phone_number_2_ext' => ['nullable','regex:/^\d{1,10}$/'],
            'email'              => ['nullable','email:rfc','max:254'],
            'working_hours'         => ['nullable','string','max:120'],
        ], [
            'phone_number.regex'    => 'Teléfono inválido (E.164).',
            'phone_number_2.regex'  => 'Teléfono alterno inválido (E.164).',
            'phone_number_ext.regex'=> 'Extensión inválida (1-10 dígitos).',
            'phone_number_2_ext.regex'=> 'Extensión inválida (1-10 dígitos).',
        ]);

        $s = $this->settings();
        $s->set('phone_number', $this->phone_number);
        $s->set('phone_number_ext', $this->phone_number_ext);
        $s->set('phone_number_2', $this->phone_number_2);
        $s->set('phone_number_2_ext', $this->phone_number_2_ext);
        $s->set('email', $this->email);
        $s->set('working_hours', $this->working_hours);

        $this->dispatch('notification', target: $this->targetNotify, type: 'success', message: 'Se han guardado los cambios.');
        $this->dispatch('site-contact-info-updated', id: $this->site->id);
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
