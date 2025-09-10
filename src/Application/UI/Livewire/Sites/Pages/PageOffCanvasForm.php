<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Sites\Pages;

use Koneko\KonekoVuexyAdmin\Support\Livewire\Components\Form\AbstractFormOffCanvasComponent;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum as EnumRule;
use Koneko\KonekoWebsiteAdmin\Application\Enums\WebsiteContents\{WebsiteContentStatus,WebsiteContentType};
use Koneko\KonekoWebsiteAdmin\Models\WebsiteContent;

/**
 * Class PageOffCanvasForm
 *
 * Componente Livewire para gestionar permisos.
 * Extiende AbstractFormOffCanvasComponent para proporcionar una interfaz
 * eficiente para la gestión de permisos en el ERP.
 *
 * @package App\Http\Livewire\Forms
 */
class PageOffCanvasForm extends AbstractFormOffCanvasComponent
{
    /**
     * Propiedades del formulario.
     */
    public $type,
        $title,
        $slug,
        $description,
        $noindex,
        $nofollow,
        $status;

    /**
     * Define el modelo Eloquent asociado con el formulario.
     */
    protected function model(): string
    {
        return WebsiteContent::class;
    }

    /**
     * Valores por defecto para el formulario.
     */
    protected function defaults(): array
    {
        return [
            'noindex'  => false,
            'nofollow' => false,
            'status'   => 'draft',
        ];
    }

    /**
     * Campo que se debe enfocar cuando se abra el formulario.
     */
    protected function focusColumnOnOpen(): string
    {
        return 'domain';
    }

    protected function beforeSave(array &$data): void {
        $data['site_id'] = 1;
    }



    /**
     * Define reglas de validación dinámicas basadas en el modo actual.
     */
    protected function dynamicRules(string $mode): array
    {
        // Reglas compartidas entre create/edit
        $shared = [
            'type'        => ['required', new EnumRule(WebsiteContentType::class)],
            // Excluimos Deleted en UI:
            'status'      => ['required', 'string', Rule::in(WebsiteContentStatus::formValues())],

            'title'       => ['required', 'string', 'max:50'],
            'slug'        => [
                'required', 'string', 'max:64', 'alpha_dash:ascii',
                Rule::unique('website_contents', 'slug')
                    ->when(isset($this->site_id), fn($r) => $r->where('site_id', $this->site_id))
                    ->ignore($this->id),
            ],
            'description' => ['nullable', 'string', 'max:500'],

            'noindex'     => ['sometimes', 'boolean'],
            'nofollow'    => ['sometimes', 'boolean'],
        ];

        return match ($mode) {
            'create', 'edit' => $shared,
            'delete'         => ['confirmDeletion' => ['accepted']],
            default          => [],
        };
    }

    /**
     * Define los atributos personalizados para los errores de validación.
     */
    protected function attributes(): array
    {
        return [
            'title'       => 'título',
            'slug'        => 'slug',
            'type'        => 'tipo de contenido',
            'status'      => 'estatus',
            'description' => 'descripción',
            'noindex'     => 'no index',
            'nofollow'    => 'no follow',
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'slug.required'  => 'El slug es obligatorio.',
            'slug.alpha_dash' => 'El slug solo permite letras, números y guiones.',
            'status.in'      => 'El estatus seleccionado no es válido para este formulario.',
        ];
    }


    /**
     * Ruta de la vista asociada con este formulario.
     */
    protected function viewPath(): string
    {
        return 'koneko-website-admin::livewire.sites.pages.offcanvas-form';
    }
}
