<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Brand;

use Livewire\Component;
use Livewire\Attributes\Rule;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

final class BrandCard extends Component
{
    public string $targetNotify = '#website-brand-card .notification-container';

    public WebsiteSite $site;

    /** Campos (reglas con Attributes v3) */
    #[Rule('required|string|max:64')]
    public string $brand_name = '';

    #[Rule('nullable|string|max:254')]
    public ?string $slogan = null;

    public function mount(WebsiteSite $site): void
    {
        $this->site = $site;
        $this->loadForm();
    }

    public function loadForm(): void
    {
        $this->brand_name = $this->site->brand_name ?? '';
        $this->slogan     = $this->site->slogan;
    }

    public function save(): void
    {
        // Valida usando #[Rule]
        $this->validate();

        // Carga fresca
        $this->site = WebsiteSite::query()->findOrFail($this->site->id);
        $this->site->update([
            'brand_name' => trim($this->brand_name),
            'slogan'  => $this->slogan ? trim($this->slogan) : null,
        ]);

        // Notificación
        $this->dispatch(
            'notification',
            target: $this->targetNotify,
            type: 'success',
            message: 'Se han guardado los cambios en las configuraciones.'
        );
    }

    public function resetForm(): void
    {
        $this->site = WebsiteSite::query()->findOrFail($this->site->id);
        $this->resetValidation();
        $this->loadForm();
    }

    public function render()
    {
        return view('koneko-website-admin::livewire.sites.brand.brand-card');
    }
}
