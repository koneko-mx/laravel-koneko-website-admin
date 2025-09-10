<?php

/**
 * COMPONENT: TemplateCard (refactor UI/UX)
 * - Mueve la carga de datos a mount()
 * - Elimina consultas duplicadas
 * - Valida template y theme_color
 * - Notificaciones y estados de carga
 * - Preparado para select mejorado (Choices/Select2) sin romper Livewire
 */

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Template;

use Koneko\KonekoWebsiteAdmin\Application\Template\TemplateRegistry;
use Koneko\KonekoWebsiteAdmin\Models\{WebsiteContent, WebsiteSite, WebsiteSeoProfile};
use Livewire\Attributes\Rule;
use Livewire\Component;

final class TemplateCard extends Component
{
    public string $seoableType;   // 'site' | 'content'
    public int    $seoableId;
    public bool   $isSite = false;

    public ?WebsiteSeoProfile $profile = null;
    public ?WebsiteContent $content = null;

    #[Rule('nullable|string|in:site,content,disable')]
    public ?string $template_mode = null;

    public array $template_list = [];

    #[Rule('required|string|max:64')]
    public string $template = '';

    #[Rule('required')]
    #[Rule('regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/')]
    public string $theme_color = '#0ea5e9';

    public string $targetNotify = '#website-template-card .notification-container';

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

        $this->template_list = TemplateRegistry::groupedOptions();

        $this->loadForm();
    }

    public function loadForm(): void
    {
        $p = $this->profile;
        $c = $this->content;

        $this->template_mode = $c ? $c->template_mode->value : null;
        $this->template      = ($p->package ?: '') . ':' . ($p->layout ?: '');
        $this->template      = trim($this->template, ':');
        $this->theme_color   = $p->theme_color ?: $this->theme_color;
    }

    public function save(): void
    {
        // Valida atributos
        $this->validate();

        // Validación estructural del template: "pkg:layout"
        if (!str_contains($this->template, ':')) {
            $this->addError('template', 'Formato inválido. Usa paquete:layout.');
            return;
        }

        [$package, $layout] = explode(':', $this->template, 2);
        $package = trim($package);
        $layout  = trim($layout);

        // Validar contra opciones disponibles (evita valores arbitrarios)
        $allowed = [];
        foreach ($this->template_list as $group) {
            foreach ($group['items'] as $key => $_label) {
                $allowed[$key] = true;
            }
        }
        if (!isset($allowed[$this->template])) {
            $this->addError('template', 'Plantilla no válida.');
            return;
        }

        try {
            $this->profile->fill([
                'package'     => $package,
                'layout'      => $layout,
                'theme_color' => $this->theme_color,
            ])->save();


            if ($this->content) {
                $this->content->fill([
                    'template_mode' => $this->template_mode,
                ])->save();
            }

            // Notificación UI
            $this->dispatch(
                'notification',
                target: $this->targetNotify,
                type: 'success',
                message: 'Configuración de plantilla guardada correctamente.'
            );

        } catch (\Throwable $e) {
            report($e);
            $this->dispatch(
                'notification',
                target: $this->targetNotify,
                type: 'danger',
                message: 'No se pudo guardar. Intenta de nuevo.'
            );
        }
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
        return view('koneko-website-admin::livewire.sites.template.template-card');
    }
}
