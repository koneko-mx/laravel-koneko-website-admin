<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Contact;

use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Koneko\KonekoVuexyAdmin\Application\Settings\Manager\KonekoSettingManager;
use Koneko\KonekoWebsiteAdmin\Application\LocalModule as WebsiteModule;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

final class ContactFormCard extends Component
{
    public WebsiteSite $site;

    public string $targetNotify = '#website-contact-form-card .notification-container';

    private const GROUP    = 'sendmail';
    private const SECTION  = 'contact';
    private const SUBGROUP = 'form';

    // ---- Campos ----
    #[Rule('required|string|max:254')]
    public string $to_email = '';

    // CC permite lista separada por coma/semicolon; se valida manualmente
    #[Rule('nullable|string|max:1000')]
    public string $to_email_cc = '';

    #[Rule('required|string|min:3|max:120')]
    public string $subject = '';

    #[Rule('required|string|min:3|max:1000')]
    public string $submit_message = '';

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

        $this->to_email        = (string) ($data['to_email'] ?? '');
        $this->to_email_cc     = (string) ($data['to_email_cc'] ?? '');
        $this->subject         = (string) ($data['subject'] ?? 'Mensaje desde {site}');
        $this->submit_message  = (string) ($data['submit_message'] ?? '¡Gracias! Hemos recibido tu mensaje.');
    }

    public function save(): void
    {
        // Normaliza
        $this->to_email    = trim(strtolower($this->to_email));
        $this->to_email_cc = $this->normalizeEmailList($this->to_email_cc);
        $this->subject     = trim($this->subject);
        $this->submit_message = trim($this->submit_message);

        // Validación básica
        $this->validate([
            'to_email'       => ['required','email:rfc','max:254'],
            'to_email_cc'    => ['nullable','string','max:1000'],
            'subject'        => ['required','string','min:3','max:120'],
            'submit_message' => ['required','string','min:3','max:1000'],
        ]);

        // Validación granular de CC (si hay)
        if ($this->to_email_cc !== '') {
            $invalid = $this->invalidEmails($this->to_email_cc);
            if (!empty($invalid)) {
                throw ValidationException::withMessages([
                    'to_email_cc' => 'Algunas direcciones no son válidas: ' . implode(', ', $invalid),
                ]);
            }
        }

        // Persistencia
        $s = $this->settings();
        $s->set('to_email', $this->to_email);
        $s->set('to_email_cc', $this->to_email_cc);
        $s->set('subject', $this->subject);
        $s->set('submit_message', $this->submit_message);

        $this->dispatch('notification', target: $this->targetNotify, type: 'success', message: 'Se han guardado los cambios.');
        $this->dispatch('site-contact-settings-updated', id: $this->site->id);
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->loadForm();
        $this->dispatch('notification', target: $this->targetNotify, type: 'info', message: 'Cambios descartados.');
    }

    public function render()
    {
        return view('koneko-website-admin::livewire.sites.contact.contact-form-card');
    }

    /* ---------------- Helpers ---------------- */

    private function normalizeEmailList(string $raw): string
    {
        if ($raw === '') return '';
        // separa por coma o punto y coma, limpia espacios y baja a lowercase
        $parts = preg_split('/[;,]+/', $raw) ?: [];
        $parts = array_values(array_filter(array_map(fn($e) => strtolower(trim($e)), $parts)));
        // dedup
        $parts = array_values(array_unique($parts));
        return implode(',', $parts);
    }

    private function invalidEmails(string $csv): array
    {
        $list = array_filter(array_map('trim', explode(',', $csv)));
        $bad  = [];
        foreach ($list as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $bad[] = $email;
            }
        }
        return $bad;
    }
}
