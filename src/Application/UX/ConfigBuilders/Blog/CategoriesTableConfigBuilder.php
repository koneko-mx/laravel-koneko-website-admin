<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\ConfigBuilders\Blog;

use Illuminate\Support\Facades\DB;
use Koneko\KonekoVuexyAdmin\Support\Builders\AbstractTableConfigBuilder;
use Koneko\KonekoWebsiteAdmin\Models\Faq;

class CategoriesTableConfigBuilder extends AbstractTableConfigBuilder
{
    /**
     * Devuelve el modelo de datos.
     */
    public function getModelClass(): string
    {
        return Faq::class;
    }

    /**
     * Devuelve las columnas seleccionadas en la consulta SQL.
     */
    public static function getIndexColumns(): array
    {
        return [
            'faqs.id',
            'faqs.category_id',
            'faq_categories.name AS category_name',
            'faq_categories.icon AS category_icon',
            'faqs.question',
            'faqs.answer',
            'faqs.order',
            'faqs.is_active',
            'faqs.created_at',
            'faqs.updated_at',
        ];
    }

    /**
     * Devuelve las etiquetas (labels) para las columnas.
     */
    public static function getIndexLabels(): array
    {
        return [
            'action'             => 'Acciones',
            'category_name'      => 'Categoría',
            'question'           => 'Pregunta',
            'answer'             => 'Respuesta',
            'order'              => 'Orden',
            'is_active'          => 'Activo',
            'created_at'         => 'Creado el',
            'updated_at'         => 'Actualizado el',
            'deleted_at'         => 'Eliminado el',
        ];
    }

    /**
     * Devuelve los JOINs requeridos por la consulta.
     */
    public static function getIndexJoins(): array
    {
        return [
            ['faq_categories', 'faqs.category_id', '=', 'faq_categories.id']
        ];
    }

    /**
     * Devuelve los filtros aplicables en la tabla (como búsqueda global).
     */
    public static function getIndexFilters(): array
    {
        return [
            'search' => [
                'faqs.question',
                'faqs.answer',
            ]
        ];
    }

    /**
     * Devuelve la configuración de formatos y visibilidad para cada columna.
     */
    public static function getIndexFormatters(): array
    {
        return [
            /*
            'action' => [
                'formatter' => 'VehicleAssignmentsAactionFormatter',
            ],
            */
            /*
            'carrier_id' => [
                'formatter' => 'profilePhotoFormatter',
            ],
            */
            'vehicle_type_id' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'brand_id' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'current_insurer_id' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'plate_number' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'model' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'year_manufacture' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'vin' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'fuel_type' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'axles' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'capacity_kg' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'start_date' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'end_date' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'max_km_allowed' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'assignment_type' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'status' => [
                'formatter' => 'dynamicBadgeFormatter',
            ],
            'created_at' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'updated_at' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'deleted_at' => [
                'formatter' => 'textNowrapFormatter',
            ],
        ];
    }

    /**
     * Devuelve las rutas del CRUD para cada fila.
     */
    public static function getIndexRoutes(): array
    {
        return [
            'admin.user.show' => route('admin.core.users.users.show', ['user' => ':id']),
        ];
    }

    /**
     * Devuelve configuraciones adicionales del Bootstrap Table.
     */
    public static function getIndexTableConfig(): array
    {
        return [
            'search' => true,
            'pagination' => true,
            'showExport' => true,
        ];
    }
}
