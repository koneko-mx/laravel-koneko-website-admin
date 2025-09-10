<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Brand;

use Livewire\Attributes\Rule;
use Livewire\Component;
use Koneko\KonekoWebsiteAdmin\Models\{WebsiteContent, WebsiteSite, WebsiteSeoProfile};

final class AuthorCopyrightCard extends Component
{
    public string $seoableType;   // 'site' | 'content'
    public int    $seoableId;
    public bool   $isSite = false;

    public ?WebsiteSeoProfile $profile = null;
    public ?WebsiteContent $content = null;

    #[Rule('nullable|string|in:site,content,disable')]
    public ?string $author_mode = null;

    #[Rule('nullable|string|max:70')]
    public ?string $author = null;

    #[Rule('nullable|string|in:site,content,disable')]
    public ?string $copyright_mode = null;

    #[Rule('nullable|string|max:160')]
    public ?string $copyright = null;

    public string $targetNotify = '#author-copyright-card .notification-container';

    public function mount(string $seoableType, int $seoableId): void
    {
        $this->seoableType = $seoableType;
        $this->seoableId   = $seoableId;
        $this->isSite      = $seoableType === 'site';

        $owner = $this->isSite
            ? WebsiteSite::query()->findOrFail($seoableId)
            : WebsiteContent::query()->findOrFail($seoableId);

        $scope = $this->isSite ? 'site' : 'content';

        $this->profile = $owner->seoProfile()->firstOrCreate([], ['scope' => $scope]);
        $this->content = $this->isSite ? null : $owner;

        $this->loadForm();
    }

    public function loadForm(): void
    {
        $p = $this->profile;
        $c = $this->content;

        $this->author_mode    = $c ? $c->author_mode->value : null;
        $this->author         = $p->author;
        $this->copyright_mode = $c ? $c->copyright_mode->value : null;
        $this->copyright      = $p->copyright;
    }

    public function save(): void
    {
        $this->validate();

        $this->profile->fill([
            'author'         => $this->author,
            'copyright'      => $this->copyright,
        ])->save();

        if ($this->content) {
            $this->content->fill([
                'author_mode'    => $this->author_mode,
                'copyright_mode' => $this->copyright_mode,
            ])->save();
        }

        $this->dispatch('notification', target: $this->targetNotify, type: 'success', message: 'Autor y Copyright guardado.');
    }

    public function resetForm(): void
    {
        $this->profile->refresh();
        $this->content->refresh();
        $this->loadForm();
        $this->resetValidation();
        $this->dispatch('notification', target: $this->targetNotify, type: 'info', message: 'Cambios descartados.');
    }

    public function render()
    {
        return view('koneko-website-admin::livewire.sites.brand.author-copyright-card');
    }
}
