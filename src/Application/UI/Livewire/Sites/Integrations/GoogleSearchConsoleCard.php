<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Integrations;

use Livewire\Component;
use Livewire\Attributes\Rule;
use Koneko\KonekoVuexyAdmin\Application\Settings\Manager\KonekoSettingManager;
use Koneko\KonekoWebsiteAdmin\Application\LocalModule as WebsiteModule;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

final class GoogleSearchConsoleCard extends Component
{
    public WebsiteSite $site;

    /** Notificador */
    public string $targetNotify = '#website-gsc-settings-card .notification-container';

    private const GROUP    = 'layout';
    private const SECTION  = 'api';
    private const SUBGROUP = 'google';

    #[Rule('boolean')]
    public bool $gsc_enabled = false;

    // token típico del meta-tag: letras/números, guion y guion bajo (normalmente ~43 chars, permitimos 10..128)
    #[Rule('nullable|string|min:10|max:128')]
    public string $gsc_verification_token = '';

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
        $this->gsc_enabled             = (bool)($data['gsc_enabled'] ?? false);
        $this->gsc_verification_token  = (string)($data['gsc_verification_token'] ?? '');
    }

    public function save(): void
    {
        $this->gsc_verification_token = trim($this->gsc_verification_token);

        if ($this->gsc_enabled) {
            $this->validate([
                'gsc_verification_token' => ['required','string','min:10','max:128','regex:/^[A-Za-z0-9_-]{10,128}$/'],
            ], [
                'gsc_verification_token.regex' => 'Usa solo letras, números, guiones y guiones bajos (10-128).',
            ]);
        }

        $this->settings()->set('gsc_enabled', $this->gsc_enabled);
        $this->settings()->set('gsc_verification_token', $this->gsc_verification_token);

        $this->dispatch('notification', target: $this->targetNotify, type: 'success', message: 'Se han guardado los cambios.');
        //$this->dispatch('site-gsc-updated', id: $this->site->id);
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->loadForm();
        $this->dispatch('notification', target: $this->targetNotify, type: 'info', message: 'Cambios descartados.');
    }

    public function render()
    {
        return view('koneko-website-admin::livewire.sites.integrations.google-search-console-card');
    }
}
