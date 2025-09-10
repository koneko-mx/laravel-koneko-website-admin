<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\General;

use Koneko\KonekoWebsiteAdmin\Application\Template\WebsiteImageHandler;
use Koneko\KonekoWebsiteAdmin\Models\{WebsiteContent, WebsiteSite, WebsiteSeoProfile};
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

final class FaviconCard extends Component
{
    use WithFileUploads;

    public string $seoableType;   // 'site' | 'content'
    public int    $seoableId;
    public bool   $isSite = false;

    public ?WebsiteSeoProfile $profile = null;
    public ?WebsiteContent $content = null;

    #[Rule('nullable|string|in:site,content,disable')]
    public ?string $favicon_mode = null;

    /** Notificador */
    public string $targetNotify = '#website-favicon-card .notification-container';

    // Previews
    public string $website_favicon_16x16 = '';
    public string $website_favicon_76x76 = '';
    public string $website_favicon_120x120 = '';
    public string $website_favicon_152x152 = '';
    public string $website_favicon_180x180 = '';
    public string $website_favicon_192x192 = '';

    /** Upload temporal (v3) */
    #[Rule(['nullable', 'image', 'mimes:jpeg,png,webp,gif', 'max:20480'])]
    public $upload_image_favicon = null;

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
        $fallback = '../vendor/koneko-vuexy-admin/img/logo/koneko-04.png';
        $basePath = config('koneko.media.paths.favicon', 'favicon-website/');
        $favicon  = $this->profile->favicon;

        $this->favicon_mode = $this->content ? $this->content->favicon_mode->value : null;
        $this->website_favicon_16x16   = isset($favicon['16x16'])   ? $basePath.$favicon['16x16']   : $fallback;
        $this->website_favicon_76x76   = isset($favicon['76x76'])   ? $basePath.$favicon['76x76']   : $fallback;
        $this->website_favicon_120x120 = isset($favicon['120x120']) ? $basePath.$favicon['120x120'] : $fallback;
        $this->website_favicon_152x152 = isset($favicon['152x152']) ? $basePath.$favicon['152x152'] : $fallback;
        $this->website_favicon_180x180 = isset($favicon['180x180']) ? $basePath.$favicon['180x180'] : $fallback;
        $this->website_favicon_192x192 = isset($favicon['192x192']) ? $basePath.$favicon['192x192'] : $fallback;
    }

    public function save(): void
    {
        $this->validate([
            'upload_image_favicon' => 'required|image|mimes:jpeg,png,webp|max:20480',
        ]);

        app(WebsiteImageHandler::class)
            ->processAndSaveFavicon($this->upload_image_favicon, $this->profile);

        if ($this->content) {
            $this->content->fill([
                'favicon_mode' => $this->favicon_mode,
            ])->save();
        }

        $this->upload_image_favicon = null;

        $this->profile->refresh();

        $this->loadForm();
        $this->dispatch('notification', target: $this->targetNotify, type: 'success', message: 'Favicon actualizado.');
    }

    public function resetForm(): void
    {
        $this->resetValidation();

        $this->upload_image_favicon->delete();
        $this->upload_image_favicon = null;

        $this->profile->refresh();
        if ($this->content) {
            $this->content->refresh();
        }

        $this->loadForm();
        $this->dispatch('notification', target: $this->targetNotify, type: 'info', message: 'Cambios descartados.');
    }

    public function render()
    {
        return view('koneko-website-admin::livewire.sites.general.favicon-card');
    }
}
