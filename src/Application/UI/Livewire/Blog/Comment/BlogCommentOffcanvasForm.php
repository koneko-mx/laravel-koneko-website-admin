<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UI\Livewire\Blog\Comment;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Koneko\KonekoVuexyAdmin\Support\Livewire\Components\Form\AbstractFormOffCanvasComponent;
use Koneko\VuexyContacts\Application\Services\ContactCatalogService;
use Koneko\VuexyStoreManager\Services\StoreCatalogService;
use Koneko\VuexyWarehouse\Models\Warehouse;

/**
 * Class BlogCommentOffcanvasForm
 *
 * Componente Livewire para gestionar almacenes.
 * Extiende la clase AbstractFormOffCanvasComponent e implementa validaciones dinámicas,
 * manejo de formularios, eventos y actualizaciones en tiempo real.
 *
 * @package Koneko\VuexyWarehouse\Livewire\Warehouses
 */
class BlogCommentOffcanvasForm extends AbstractFormOffCanvasComponent
{
    /**
     * Propiedades del formulario relacionadas con el almacén.
     */
    public $id, $store_id, $work_center_id, $code, $name, $description,
        $manager_id, $tel, $tel2, $priority, $status, $confirmDeletion;

    /**
     * Listas de opciones para selects en el formulario.
     */
    public $store_options = [],
        $work_center_options = [],
        $manager_options = [];

    /**
     * Eventos de escucha de Livewire.
     *
     * @var array
     */
    protected $listeners = [
        'editWarehouse' => 'loadFormModel',
        'confirmDeletionWarehouse' => 'loadFormModelForDeletion',
    ];

    /**
     * Definición de tipos de datos que se deben castear.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Define el modelo Eloquent asociado con el formulario.
     *
     * @return string
     */
    protected function model(): string
    {
        return Warehouse::class;
    }

    /**
     * Define los campos del formulario.
     *
     * @return array<string, mixed>
     */
    protected function fields(): array
    {
        return (new Warehouse())->getFillable();
    }

    /**
     * Valores por defecto para el formulario.
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            'priority' => 0,
            'status' => true,
        ];
    }

    /**
     * Campo que se debe enfocar cuando se abra el formulario.
     *
     * @return string
     */
    protected function focusColumnOnOpen(): string
    {
        return 'code';
    }

    /**
     * Define reglas de validación dinámicas basadas en el modo actual.
     *
     * @param string $mode El modo actual del formulario ('create', 'edit', 'delete').
     * @return array
     */
    protected function dynamicRules(string $mode): array
    {
        switch ($mode) {
            case 'create':
            case 'edit':
                return [
                    'store_id'       => ['required', 'integer', 'exists:stores,id'],
                    'work_center_id' => ['nullable', 'integer', 'exists:store_work_centers,id'],
                    'code'           => ['required', 'string', 'max:16', Rule::unique('warehouses', 'code')->ignore($this->id)],
                    'name'           => ['required', 'string', 'max:96'],
                    'description'    => ['nullable', 'string', 'max:1024'],
                    'manager_id'     => ['nullable', 'integer', 'exists:users,id'],
                    'tel'            => ['nullable', 'regex:/^[0-9+\-\s]+$/', 'max:20'],
                    'tel2'           => ['nullable', 'regex:/^[0-9+\-\s]+$/', 'max:20'],
                    'priority'       => ['nullable', 'numeric', 'between:0,99'],
                    'status'         => ['nullable', 'boolean'],
                ];

            case 'delete':
                return [
                    'confirmDeletion' => 'accepted', // Asegura que el usuario confirme la eliminación
                ];

            default:
                return [];
        }
    }

    // ===================== VALIDACIONES =====================

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    protected function attributes(): array
    {
        return [
            'code' => 'código de almacén',
            'name' => 'nombre del almacén',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'store_id.required' => 'El almacén debe estar asociado a un negocio.',
            'code.required' => 'El código del almacén es obligatorio.',
            'code.unique' => 'Este código ya está en uso por otro almacén.',
            'name.required' => 'El nombre del almacén es obligatorio.',
        ];
    }

    /**
     * Carga el formulario con datos del almacén y actualiza las opciones dinámicas.
     *
     * @param int $id
     */
    public function loadFormModel($id): void
    {
        parent::loadFormModel($id);

        $this->work_center_options = DB::table('store_work_centers')
            ->where('store_id', $this->store_id)
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Carga el formulario para eliminar un almacén, actualizando las opciones necesarias.
     *
     * @param int $id
     */
    public function loadFormModelForDeletion($id): void
    {
        parent::loadFormModelForDeletion($id);

        $this->work_center_options = DB::table('store_work_centers')
            ->where('store_id', $this->store_id)
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Define las opciones de los selectores desplegables.
     *
     * @return array
     */
    protected function options(): array
    {
        $storeCatalogService = app(StoreCatalogService::class);
        $contactCatalogService = app(ContactCatalogService::class);

        return [
            'store_options' => $storeCatalogService->getCatalog('stores', '', ['limit' => -1]),
            'manager_options' => $contactCatalogService->getCatalog('users', '', ['limit' => -1]),
        ];
    }

    /**
     * Ruta de la vista asociada con este formulario.
     *
     * @return string
     */
    protected function viewPath(): string
    {
        return 'koneko-website-admin::livewire.faq.offcanvas-form';
    }
}
