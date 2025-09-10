<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Integrations;

use Livewire\Component;
use Livewire\Attributes\Rule;
use Koneko\KonekoVuexyAdmin\Application\Settings\Manager\KonekoSettingManager;
use Koneko\KonekoWebsiteAdmin\Application\LocalModule as WebsiteModule;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

final class PixelMetaCard extends Component
{
    public WebsiteSite $site;

    public string $targetNotify = '#website-meta-pixel-settings-card .notification-container';

    private const GROUP    = 'layout';
    private const SECTION  = 'api';
    private const SUBGROUP = 'meta';

    #[Rule('boolean')]
    public bool $pixel_enabled = false;

    // Pixel ID: numérico (ej. 123456789012345) – aceptamos 5..20 dígitos
    #[Rule('nullable|string|min:5|max:20')]
    public string $pixel_id = '';

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
        $this->pixel_enabled = (bool)($data['pixel_enabled'] ?? false);
        $this->pixel_id      = (string)($data['pixel_id'] ?? '');
    }

    public function save(): void
    {
        $this->pixel_id = trim($this->pixel_id);

        if ($this->pixel_enabled) {
            $this->validate([
                'pixel_id' => ['required','string','min:5','max:20','regex:/^\d{5,20}$/'],
            ], [
                'pixel_id.regex' => 'El Pixel ID debe ser numérico (5 a 20 dígitos).',
            ]);
        }

        $this->settings()->set('pixel_enabled', $this->pixel_enabled);
        $this->settings()->set('pixel_id', $this->pixel_id);

        $this->dispatch('notification', target: $this->targetNotify, type: 'success', message: 'Se han guardado los cambios.');
        $this->dispatch('site-meta-pixel-updated', id: $this->site->id);
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->loadForm();
        $this->dispatch('notification', target: $this->targetNotify, type: 'info', message: 'Cambios descartados.');
    }

    public function render()
    {
        return view('koneko-website-admin::livewire.sites.integrations.pixel-meta-card');
    }
}
