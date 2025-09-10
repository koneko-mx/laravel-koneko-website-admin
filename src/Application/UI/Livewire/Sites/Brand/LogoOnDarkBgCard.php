<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Brand;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use Koneko\KonekoWebsiteAdmin\Application\Template\WebsiteImageHandler;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

final class LogoOnDarkBgCard extends Component
{
    use WithFileUploads;

    public WebsiteSite $site;

    /** Notificadores separados por card */
    public string $targetNotifyV = '#logo-on-dark-bg-card .notification-container';
    public string $targetNotifyH = '#logo-h-on-dark-bg-card .notification-container';

    /** Uploads temporales (Livewire) */
    #[Rule(['nullable', 'image', 'mimes:jpeg,png,webp', 'max:20480'])]
    public $upload_logo_dark = null;     // vertical/regular sobre fondo oscuro

    #[Rule(['nullable', 'image', 'mimes:jpeg,png,webp', 'max:20480'])]
    public $upload_logo_h_dark = null;   // horizontal sobre fondo oscuro

    /** Paths actuales */
    public string $logo_dark_path = '';
    public string $logo_h_dark_path = '';

    public function mount(WebsiteSite $site): void
    {
        $this->site = $site;
        $this->loadForm();
    }

    public function loadForm(): void
    {
        $handler = app(WebsiteImageHandler::class);
        $dark    = $handler->getImageLogoVars($this->site, 'dark');
        $hDark   = $handler->getImageLogoVars($this->site, 'h_dark');

        $this->upload_logo_dark   = null;
        $this->upload_logo_h_dark = null;
        $this->logo_dark_path     = $dark['large'] ?? '';
        $this->logo_h_dark_path   = $hDark['large'] ?? '';
    }

    public function saveVerticalDark(): void
    {
        $this->validateOnly('upload_logo_dark');
        if ($this->upload_logo_dark) {
            app(WebsiteImageHandler::class)->processAndSaveImageLogo($this->upload_logo_dark, $this->site, 'dark');
        }

        $this->loadForm();
        $this->dispatch('notification', target: $this->targetNotifyV, type: 'success', message: 'Logotipo (oscuro) guardado.');
    }

    public function saveHorizontalDark(): void
    {
        $this->validateOnly('upload_logo_h_dark');
        if ($this->upload_logo_h_dark) {
            app(WebsiteImageHandler::class)->processAndSaveImageLogo($this->upload_logo_h_dark, $this->site, 'h_dark');
        }

        $this->loadForm();
        $this->dispatch('notification', target: $this->targetNotifyH, type: 'success', message: 'Logotipo horizontal (oscuro) guardado.');
    }

    public function resetVerticalDark(): void
    {
        $this->resetValidation('upload_logo_dark');
        $this->upload_logo_dark = null;
        $this->dispatch('notification', target: $this->targetNotifyV, type: 'info', message: 'Cambios descartados.');
    }

    public function resetHorizontalDark(): void
    {
        $this->resetValidation('upload_logo_h_dark');
        $this->upload_logo_h_dark = null;
        $this->dispatch('notification', target: $this->targetNotifyH, type: 'info', message: 'Cambios descartados.');
    }

    public function render()
    {
        return view('koneko-website-admin::livewire.sites.brand.logo-on-dark-bg-card');
    }
}
