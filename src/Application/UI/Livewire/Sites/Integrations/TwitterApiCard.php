<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Integrations;

use Livewire\Component;
use Livewire\Attributes\Rule;
use Koneko\KonekoVuexyAdmin\Application\Settings\Manager\KonekoSettingManager;
use Koneko\KonekoWebsiteAdmin\Application\LocalModule as WebsiteModule;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

final class TwitterApiCard extends Component
{
    public WebsiteSite $site;

    public string $targetNotify = '#website-twitter-api-settings-card .notification-container';

    private const GROUP    = 'layout';
    private const SECTION  = 'api';
    private const SUBGROUP = 'twitter';

    #[Rule('boolean')]
    public bool $tw_enabled = false;

    // Claves típicas (longitudes aproximadas; relajamos con rangos razonables)
    #[Rule('nullable|string|min:10|max:80')]
    public string $tw_api_key = '';

    #[Rule('nullable|string|min:10|max:120')]
    public string $tw_api_secret = '';

    #[Rule('nullable|string|min:20|max:200')]
    public string $tw_bearer_token = '';

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
        $this->tw_enabled      = (bool)($data['tw_enabled'] ?? false);
        $this->tw_api_key      = (string)($data['tw_api_key'] ?? '');
        $this->tw_api_secret   = (string)($data['tw_api_secret'] ?? '');
        $this->tw_bearer_token = (string)($data['tw_bearer_token'] ?? '');
    }

    public function save(): void
    {
        $this->tw_api_key      = trim($this->tw_api_key);
        $this->tw_api_secret   = trim($this->tw_api_secret);
        $this->tw_bearer_token = trim($this->tw_bearer_token);

        if ($this->tw_enabled) {
            $this->validate([
                'tw_api_key'      => ['required','string','min:10','max:80','regex:/^[A-Za-z0-9-_]{10,80}$/'],
                'tw_api_secret'   => ['required','string','min:10','max:120','regex:/^[A-Za-z0-9-_]{10,120}$/'],
                'tw_bearer_token' => ['required','string','min:20','max:200','regex:/^[A-Za-z0-9._-]{20,200}$/'],
            ], [
                'tw_api_key.regex'      => 'Solo letras, números y -_. (10-80).',
                'tw_api_secret.regex'   => 'Solo letras, números y -_. (10-120).',
                'tw_bearer_token.regex' => 'Formato inválido (20-200; letras, números y -_.)',
            ]);
        }

        $this->settings()->set('tw_enabled', $this->tw_enabled);
        $this->settings()->set('tw_api_key', $this->tw_api_key);
        $this->settings()->set('tw_api_secret', $this->tw_api_secret);
        $this->settings()->set('tw_bearer_token', $this->tw_bearer_token);

        $this->dispatch('notification', target: $this->targetNotify, type: 'success', message: 'Se han guardado los cambios.');
        //$this->dispatch('site-twitter-api-updated', id: $this->site->id);
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->loadForm();
        $this->dispatch('notification', target: $this->targetNotify, type: 'info', message: 'Cambios descartados.');
    }

    public function render()
    {
        return view('koneko-website-admin::livewire.sites.integrations.twitter-api-card');
    }
}
