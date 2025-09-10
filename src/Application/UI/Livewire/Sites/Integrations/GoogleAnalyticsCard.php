<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Integrations;

use Livewire\Component;
use Livewire\Attributes\Rule;
use Koneko\KonekoVuexyAdmin\Application\Settings\Manager\KonekoSettingManager;
use Koneko\KonekoWebsiteAdmin\Application\LocalModule as WebsiteModule;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

final class GoogleAnalyticsCard extends Component
{
    public WebsiteSite $site;

    /** Notificador */
    public string $targetNotify = '#website-analytics-settings-card .notification-container';

    private const GROUP    = 'layout';
    private const SECTION  = 'api';
    private const SUBGROUP = 'google';

    #[Rule('boolean')]
    public bool $ga_enabled = false;

    // GA4 Measurement ID (p. ej. G-ABC123DEF4)
    #[Rule('nullable|string|min:3|max:40')]
    public string $ga_id = '';

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

        $this->ga_enabled = (bool)($data['ga_enabled'] ?? false);
        $this->ga_id      = (string)($data['ga_id'] ?? '');
    }

    public function save(): void
    {
        // Normaliza
        $this->ga_id = strtoupper(trim($this->ga_id));

        if ($this->ga_enabled) {
            $this->validate([
                'ga_id' => ['required','string','max:40','regex:/^G-[A-Z0-9]{8,16}$/'],
            ], [
                'ga_id.regex' => 'El ID debe tener formato GA4 válido, p. ej. G-ABC123DEF4.',
            ]);
        }

        $this->settings()->set('ga_enabled', $this->ga_enabled);
        $this->settings()->set('ga_id', trim($this->ga_id));

        $this->dispatch('notification', target: $this->targetNotify, type: 'success', message: 'Se han guardado los cambios.');
        //$this->dispatch('site-analytics-updated', id: $this->site->id);
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->loadForm();
        $this->dispatch('notification', target: $this->targetNotify, type: 'info', message: 'Cambios descartados.');
    }

    public function render()
    {
        return view('koneko-website-admin::livewire.sites.integrations.google-analytics-card');
    }
}
