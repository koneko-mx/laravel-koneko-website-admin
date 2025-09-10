<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\General;

use Livewire\Component;
use Livewire\Attributes\Rule;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

final class DescriptionCard extends Component
{
    public string $targetNotify = '#website-description-card .notification-container';

    public WebsiteSite $site;

    public function boot(): void
    {
        $this->loadForm();
    }

    /** Campos (reglas con Attributes v3) */
    #[Rule('required|string|max:255')]
    public string $domain = '';

    #[Rule('required|string|max:96')]
    public string $title = '';

    public function mount(WebsiteSite $site): void
    {
        $this->site = $site;
        $this->loadForm();
    }

    public function loadForm(): void
    {
        $this->domain = $this->site->domain;
        $this->title  = $this->site->title;
    }

    public function save(): void
    {
        // Valida usando #[Rule]
        $this->validate([
            'domain' => [
                'required','string','max:255',
                // valida dominio ascii/punycode sin protocolo/WWW
                'regex:/^(?!https?:\/\/)(?!www\.)(?=.{1,253}$)(?!-)[A-Za-z0-9-]{1,63}(?<!-)(?:\.(?!-)[A-Za-z0-9-]{1,63}(?<!-))+$/',
                \Illuminate\Validation\Rule::unique('website_sites','domain')->ignore($this->site->id),
            ],
            'title'  => ['required','string','max:96'],
        ]);

        $domain = strtolower(trim($this->domain));
        $domain = preg_replace('#^(https?://)?(www\.)?#i','',$domain);

        $this->site->update([
            'domain' => $domain,
            'title'  => trim($this->title),
        ]);

        // Limpiamos Cache


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
        return view('koneko-website-admin::livewire.sites.general.description-card');
    }
}
