<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Brand;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use Koneko\KonekoWebsiteAdmin\Application\Template\WebsiteImageHandler;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

final class LogoOnLightBgCard extends Component
{
    use WithFileUploads;

    public WebsiteSite $site;

    /**
     * Notificadores separados por card
     */
    public string $targetNotifyV = '#logo-on-light-bg-card .notification-container';
    public string $targetNotifyH = '#logo-h-on-light-bg-card .notification-container';

    /**
     * Uploads temporales (Livewire) + paths actuales
     */
    #[Rule(['nullable', 'image', 'mimes:jpeg,png,webp', 'max:20480'])]
    public $upload_logo = null; // vertical/regular

    #[Rule(['nullable', 'image', 'mimes:jpeg,png,webp', 'max:20480'])]
    public $upload_logo_h = null; // horizontal

    public string $logo_path = '';
    public string $logo_path_h = '';

    public function mount(WebsiteSite $site): void
    {
        $this->site = $site;
        $this->loadForm();
    }

    public function loadForm(): void
    {
        $handler = app(WebsiteImageHandler::class);
        $regular   = $handler->getImageLogoVars($this->site, 'default');
        $horizontal= $handler->getImageLogoVars($this->site, 'h_default'); // <- variante horizontal vía parámetro

        $this->upload_logo   = null;
        $this->upload_logo_h = null;
        $this->logo_path     = $regular['large'] ?? '';
        $this->logo_path_h   = $horizontal['large'] ?? '';
    }

    public function saveVertical(): void
    {
        $this->validateOnly('upload_logo');
        if ($this->upload_logo) {
            app(WebsiteImageHandler::class)->processAndSaveImageLogo($this->upload_logo, $this->site);
        }

        $this->loadForm();
        $this->dispatch('notification', target: $this->targetNotifyV, type: 'success', message: 'Logotipo guardado.');
    }

    public function saveHorizontal(): void
    {
        $this->validateOnly('upload_logo_h');
        if ($this->upload_logo_h) {
            app(WebsiteImageHandler::class)->processAndSaveImageLogo($this->upload_logo_h, $this->site, 'h_default');
        }

        $this->loadForm();
        $this->dispatch('notification', target: $this->targetNotifyH, type: 'success', message: 'Logotipo horizontal guardado.');
    }

    public function resetVertical(): void
    {
        $this->resetValidation('upload_logo');
        $this->upload_logo = null;
        $this->dispatch('notification', target: $this->targetNotifyV, type: 'info', message: 'Cambios descartados.');
    }

    public function resetHorizontal(): void
    {
        $this->resetValidation('upload_logo_h');
        $this->upload_logo_h = null;
        $this->dispatch('notification', target: $this->targetNotifyH, type: 'info', message: 'Cambios descartados.');
    }

    public function render()
    {
        return view('koneko-website-admin::livewire.sites.brand.logo-on-light-bg-card');
    }
}
