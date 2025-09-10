<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Seo;

use Livewire\Attributes\Rule;
use Livewire\Component;
use Koneko\KonekoWebsiteAdmin\Models\{WebsiteSeoProfile, WebsiteSite, WebsiteContent};

final class LocaleCard extends Component
{
    public string $scope;   // 'site' | 'content'
    public int    $scopeId;
    public bool   $isSite = false;

    public ?WebsiteSeoProfile $profile = null;
    public ?WebsiteContent $content = null;

    #[Rule('nullable|string|in:site,content,disable')]
    public ?string $locale_mode = null;

    #[Rule('required|string|in:es-MX,es-ES,en-US,en-GB')]
    public string $locale = 'es-MX';

    public array $localeOptions = [
        'es-MX' => 'Español (MX)',
        'es-ES' => 'Español (ES)',
        'en-US' => 'English (US)'
    ];

    public string $targetNotify = '#website-seo-locale-card .notification-container';

    public function mount(string $scope, int $scopeId): void
    {
        $this->scope = $scope;
        $this->scopeId   = $scopeId;
        $this->isSite      = $scope === 'site';

        $owner = $this->isSite
            ? WebsiteSite::query()->findOrFail($scopeId)
            : WebsiteContent::query()->findOrFail($scopeId);

        $scope = $this->isSite ? 'site' : 'content';
        $this->profile = $owner->seoProfile()->firstOrCreate([], ['scope' => $scope]);
        $this->content = $this->isSite ? null : $owner;

        $this->loadForm();
    }

    public function loadForm(): void
    {
        $p = $this->profile;
        $c = $this->content;

        $this->locale_mode = $c ? $c->locale_mode->value : null;
        $this->locale      = $p->locale ?: $this->locale;
    }

    public function save(): void
    {
        $this->validate();

        $this->profile->fill([
            'locale' => $this->locale,
        ])->save();

        if ($this->content) {
            $this->content->fill([
                'locale' => $this->locale,
            ])->save();
        }

        $this->dispatch('notification', target: $this->targetNotify, type: 'success', message: 'SEO (Idioma/Geo) guardado.');
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->profile->refresh();
        if($this->content) $this->content->refresh();
        $this->loadForm();
        $this->dispatch('notification', target: $this->targetNotify, type: 'info', message: 'Cambios descartados.');
    }

    public function render()
    {
        return view('koneko-website-admin::livewire.sites.seo.locale-card');
    }
}
