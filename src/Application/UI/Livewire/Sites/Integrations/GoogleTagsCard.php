<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Integrations;

use Livewire\Component;
use Livewire\Attributes\Rule;
use Koneko\KonekoVuexyAdmin\Application\Settings\Manager\KonekoSettingManager;
use Koneko\KonekoWebsiteAdmin\Application\LocalModule as WebsiteModule;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

final class GoogleTagsCard extends Component
{
    public WebsiteSite $site;

    /** Notificador */
    public string $targetNotify = '#website-google-tags-settings-card .notification-container';

    private const GROUP    = 'layout';
    private const SECTION  = 'api';
    private const SUBGROUP = 'google';

    #[Rule('boolean')]
    public bool $gtm_enabled = false;

    #[Rule('nullable|string|min:3|max:40')]
    public string $gtm_container_id = '';

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

        $this->gtm_enabled      = (bool)($data['gtm_enabled'] ?? false);
        $this->gtm_container_id = (string)($data['gtm_container_id'] ?? '');
    }

    public function save(): void
    {
        $this->gtm_container_id = strtoupper(trim($this->gtm_container_id));

        if ($this->gtm_enabled) {
            $this->validate([
                'gtm_container_id' => ['required','string','max:40','regex:/^GTM-[A-Z0-9]{4,16}$/'],
            ], [
                'gtm_container_id.regex' => 'El ID debe tener formato válido: p. ej., GTM-ABC1234.',
            ]);
        }

        $this->settings()->set('gtm_enabled', $this->gtm_enabled);
        $this->settings()->set('gtm_container_id', trim($this->gtm_container_id));

        $this->dispatch('notification', target: $this->targetNotify, type: 'success', message: 'Se han guardado los cambios.');
        //$this->dispatch('site-gtm-updated', id: $this->site->id);
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->loadForm();
        $this->dispatch('notification', target: $this->targetNotify, type: 'info', message: 'Cambios descartados.');
    }

    public function render()
    {
        return view('koneko-website-admin::livewire.sites.integrations.google-tags-card');
    }
}
