<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\UX\ConfigBuilders\Pages;

use Illuminate\Support\Facades\DB;
use Koneko\KonekoVuexyAdmin\Support\Builders\Table\AbstractTableConfigBuilder;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteContent;

/**
 * Configuración de la vista indexada para parámetros y configuraciones ('WebsiteContents') del sistema.
 * Soporta múltiples tipos de valores, usuario asignado y metadatos del archivo adjunto (si aplica).
 */
class PagesTableConfigBuilder extends AbstractTableConfigBuilder
{
    /**
     * Devuelve la clase del module principal.
     */
    public function getModelClass(): string
    {
        return WebsiteContent::class;
    }

    /**
     * Devuelve las columnas seleccionadas en la consulta SQL.
     */
    public static function getIndexColumns(): array
    {
        return [

            'website_contents.id',
            'website_contents.site_id',
            'website_contents.type',
            'website_contents.title',
            'website_contents.slug',
            'website_contents.description',
            'website_contents.keywords',
            'website_contents.canonical_url',
            'website_contents.noindex',
            'website_contents.nofollow',
            'website_contents.roles',

            DB::raw("IF(sp_content.author_mode = 'site', sp_site.author_mode, sp_content.author_mode) AS author_mode"),
            DB::raw("IF(sp_content.author_mode = 'site', sp_site.author, sp_content.author) AS author"),

            DB::raw("IF(sp_content.copyright_mode = 'site', sp_site.copyright_mode, sp_content.copyright_mode) AS copyright_mode"),
            DB::raw("IF(sp_content.copyright_mode = 'site', sp_site.copyright, sp_content.copyright) AS copyright"),

            DB::raw("IF(sp_content.schema_mode = 'site', sp_site.schema_mode, sp_content.schema_mode) AS schema_mode"),

            DB::raw("IF(sp_content.favicon_mode = 'site', sp_site.favicon_mode, sp_content.favicon_mode) AS favicon_mode"),

            DB::raw("IF(sp_content.title_mode = 'site', sp_site.title_mode, sp_content.title_mode) AS title_mode"),
            DB::raw("IF(sp_content.title_format = 'site', sp_site.title_format, sp_content.title_format) AS title_format"),

            DB::raw("IF(sp_content.template_mode = 'site', sp_site.template_mode, sp_content.template_mode) AS template_mode"),
            DB::raw("IF(sp_content.package = 'site', sp_site.package, sp_content.package) AS package"),
            DB::raw("IF(sp_content.layout = 'site', sp_site.layout, sp_content.layout) AS layout"),
            DB::raw("IF(sp_content.theme_color = 'site', sp_site.theme_color, sp_content.theme_color) AS theme_color"),

            DB::raw("IF(sp_content.locale_mode = 'site', sp_site.locale_mode, sp_content.locale_mode) AS locale_mode"),
            DB::raw("IF(sp_content.locale_mode = 'site', sp_site.locale, sp_content.locale) AS locale"),

            DB::raw("IF(sp_content.og_mode = 'site', sp_site.og_mode, sp_content.og_mode) AS og_mode"),
            DB::raw("IF(sp_content.twitter_mode = 'site', sp_site.twitter_mode, sp_content.twitter_mode) AS twitter_mode"),

            'website_contents.permissions',
            'website_contents.hide_if_authenticated',
            'website_contents.hide_if_guest',
            'website_contents.visible_from',
            'website_contents.visible_until',
            'website_contents.status',
            'website_contents.enable_cache',
            'website_contents.cache_ttl',

            'website_contents.created_at',
            'creator.id AS creator_id',
            DB::raw('CONCAT_WS(" ", creator.name, creator.last_name) AS creator_name'),
            'creator.email AS creator_email',
            'creator.profile_photo_path AS creator_profile_photo_path',
            'website_contents.updated_at',
            'updater.id AS updater_id',
            DB::raw('CONCAT_WS(" ", updater.name, updater.last_name) AS updater_name'),
            'updater.email AS updater_email',
            'updater.profile_photo_path AS updater_profile_photo_path',
        ];
    }

    /**
     * Devuelve las etiquetas legibles (labels) para las columnas.
     * Cortas, claras y adaptadas a México.
     */
    public static function getIndexLabels(): array
    {
        return [
            'action'         => 'Acciones',

            'site_id' => 'site id',

            'type'           => 'Tipo',
            'title'          => 'Título',
            'slug'           => 'Slug',
            'description'    => 'Descripción',
            'keywords'       => 'Keywords',
            'canonical_url'  => 'Canonical URL',
            'noindex'        => 'Noindex',
            'nofollow'       => 'Nofollow',
            'roles'          => 'Roles',

            'author_mode'    => 'Author Mode',
            'author'         => 'Author',

            'copyright_mode' => 'Copyright Mode',
            'copyright'      => 'Copyright',

            'schema_mode'    => 'Schema Mode',

            'favicon_mode'   => 'Favicon Mode',

            'title_mode'     => 'Title Mode',
            'title_format'   => 'Title Format',

            'template_mode'  => 'Template Mode',
            'package'        => 'Package',
            'layout'         => 'Layout',
            'theme_color'    => 'Theme Color',

            'locale_mode'    => 'Locale Mode',
            'locale'         => 'Locale',

            'og_mode'        => 'OG Mode',
            'twitter_mode'   => 'Twitter Mode',

            'permissions'    => 'Permissions',
            'hide_if_authenticated' => 'Hide If Authenticated',
            'hide_if_guest'  => 'Hide If Guest',
            'visible_from'   => 'Visible From',
            'visible_until'  => 'Visible Until',
            'status'         => 'Status',
            'enable_cache'   => 'Enable Cache',
            'cache_ttl'      => 'Cache TTL',

            'created_at'     => 'Creado',
            'creator_id'     => 'Creado por',
            'updated_at'     => 'Actualizado',
            'updater_id'     => 'Modificado por',
        ];
    }

    /**
     * Devuelve los JOINs requeridos para la consulta.
     */
    public static function getIndexJoins(): array
    {
        return [
            ["website_sites", "website_contents.site_id", "=", "website_sites.id", ["type" => "join"]],

            ["website_seo_profiles AS sp_content", "website_contents.id", "=", "sp_content.seoable_id", ["type" => "leftJoin", "and" => "sp_content.scope = 'content'"]],
            ["website_seo_profiles AS sp_site", "website_sites.id", "=", "sp_site.seoable_id", ["type" => "leftJoin", "and" => "sp_site.scope = 'site'"]],

            ["users AS creator", "website_contents.created_by", "=", "creator.id", ["type" => "leftJoin"]],
            ["users AS updater", "website_contents.updated_by", "=", "updater.id", ["type" => "leftJoin"]],
        ];
    }

    /**
     * Devuelve los filtros aplicables en la búsqueda.
     */
    public static function getIndexFilters(): array
    {
        return [
            // 1) Fijo por sitio: site_id = 1
            'site_id' => [
                'column' => 'website_contents.site_id',
                'op'     => '=',          // =, <>, >, >=, <, <=, like
                'type'   => 'int',
            ],

            // 2) Búsqueda global (alias param=search) sobre varias columnas
            'q' => [
                'param'   => 'search',    // nombre que llega en el request
                'columns' => [
                    'website_contents.title',
                    'website_contents.slug',
                ],
                'op' => 'like_any',       // OR sobre columns con LIKE
            ],

            // 3) Estado múltiple: ?status=draft,published (IN)
            'status' => [
                'column' => 'website_contents.status',
                'op'     => 'in',         // array o CSV
                'type'   => 'string',
            ],

            // 4) Rango de fechas: ?visible_between_from=2025-08-01&visible_between_to=2025-09-01
            'visible_between' => [
                'column' => 'website_contents.visible_from',
                'op'     => 'between',    // usa from/to
                'type'   => 'date',       // date|datetime|int
                // por defecto busca <param>_from y <param>_to, puedes mapear
                // 'param' => ['from' => 'from', 'to' => 'to'],
            ],

            // 5) Booleano exacto: ?noindex=1|0
            'noindex' => [
                'column' => 'website_contents.noindex',
                'op'     => '=',
                'type'   => 'bool',       // '1','true','on' → true
            ],

            // 6) Columna calculada: locale efectivo (content vs site)
            'locale' => [
                'column' => DB::raw("IF(sp_content.locale_mode='site', sp_site.locale, sp_content.locale)"),
                'op'     => '=',
                'type'   => 'string',
            ],

            // 7) JSON: “roles” contiene alguno de los valores (array o CSV)
            'roles' => [
                'column' => 'website_contents.roles',
                'op'     => 'json_contains_any',
                'type'   => 'array',
            ],

            // 8) Filtro custom con closure (cuando nada más alcanza)
            'author_like' => [
                'closure' => function ($q, $value) {
                    $q->where(DB::raw("IF(sp_content.author_mode='site', sp_site.author, sp_content.author)"), 'like', "%{$value}%");
                },
                // opcional: 'param' => 'author'
            ],
        ];
    }

    public static function getIndexGrouping(): array
    {
        if (!request()->boolean('group_by_status')) return [];

        return [
            'by' => ['website_contents.status'],
            'aggregates' => [
                'total' => DB::raw('COUNT(*)'),
                'last_updated' => DB::raw('MAX(website_contents.updated_at)'),
            ],
            'having' => [['column' => 'total', 'op' => '>', 'value' => 0]],
        ];
    }

    public static function getIndexAllowedSort(): array
    {
        $grouped = request()->boolean('group_by_status'); // o el flag que uses

        if ($grouped) {
            return [
                'status'       => 'website_contents.status',
                'total'        => 'total',
                'last_updated' => 'last_updated',
            ];
        }

        return [
            'title'      => 'website_contents.title',
            'status'     => 'website_contents.status',
            'updated_at' => 'website_contents.updated_at',
        ];
    }


    public function getDefaultLimit(): int { return 25; }
    public function getMaxLimit(): int { return 1000; }
    public function getSortColumn(): string { return 'updated_at'; }
    public function getDefaultSortOrder(): string { return 'desc'; }

    /**
     * Devuelve los formatters por columna.
     * Aquí se define visibilidad, estilos y formateo visual.
     */
    public static function getIndexFormatters(): array
    {
        return [
            'action' => [
                'formatter' => 'websiteContentActionFormatter',
                'onlyFormatter' => true,
            ],
            'type'           => [
                'align'=> 'center',
                'visible'   => false,
            ],
            'title'          => [
                'formatter' => 'websiteContentTitleFormatter',
            ],
            'slug'           => [
                //'formatter' => 'websiteContent_Formatter',
                'visible'   => false,
            ],
            'description'    => [
                //'formatter' => 'websiteContent_Formatter',
                'visible'   => false,
                'sortable' => false,
            ],
            'keywords'       => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
                'sortable' => false,
            ],
            'canonical_url'  => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
            ],
            'noindex'        => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
            ],
            'nofollow'       => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
            ],
            'roles'          => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
                'sortable' => false,
            ],
            'author_mode'    => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
            ],
            'author'         => [
                'align'=> 'center',
                'visible'   => false,
            ],
            'copyright_mode' => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
            ],
            'copyright'      => [
                'align'=> 'center',
                'visible'   => false,
            ],
            'schema_mode'    => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
            ],
            'favicon_mode'   => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
            ],
            'title_mode'     => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
            ],
            'title_format'   => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
            ],
            'template_mode'  => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
            ],
            'package'        => [
                'align'=> 'center',
            ],
            'layout'         => [
                'align'=> 'center',
            ],
            'theme_color'    => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
            ],
            'locale_mode'    => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
            ],
            'locale'         => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
            ],
            'og_mode'        => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
            ],
            'twitter_mode'   => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
            ],
            'permissions'    => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
                'sortable' => false,
            ],
            'hide_if_authenticated' => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
            ],
            'hide_if_guest'  => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
            ],
            'visible_from'   => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
            ],
            'visible_until'  => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
                'visible'   => false,
            ],
            'status'         => [
                //'formatter' => 'websiteContent_Formatter',
                'align'=> 'center',
            ],
            'enable_cache'   => [
                'align'=> 'center',
                'visible'   => false,
            ],
            'cache_ttl'      => [
                'align'=> 'center',
                'visible'   => false,
            ],
            'created_at' => [
                'formatter' => 'dateClassicFormatter',
                'align'=> 'center',
                'visible'   => false,
            ],
            'creator_id' => [
                'formatter' => 'creatorProfileFormatter',
                'visible'   => false,
            ],
            'updated_at' => [
                'formatter' => 'dateClassicFormatter',
                'align'=> 'center',
            ],
            'updater_id' => [
                'formatter' => 'updaterProfileFormatter',
                'visible'   => false,
            ],
        ];
    }

    /**
     * Devuelve las rutas CRUD utilizadas en los botones de acción.
     * En este caso no se usan, pero se deja estructura base.
     */
    public static function getIndexRoutes(): array
    {
        return [
            'websites-admin.pages.edit' => route('admin.website-admin.websites.pages.edit', ['site' => ':site_id', 'page' => ':id']),
        ];
    }

    /**
     * Configuración avanzada del componente Bootstrap Table.
     * Enfocada en visibilidad técnica con buena UX.
     */
    public static function getIndexTableConfig(): array
    {
        return [
            'search' => true,
            'showRefresh' => false,
            'showFullscreen' => false,
            'fixedNumber' => 2,
            'showExport' => false

        ];
    }
}
