<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, MorphTo};
use Koneko\KonekoVuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;
use Koneko\KonekoWebsiteAdmin\Application\Enums\WebsiteMenuItem\{WebsiteMenuItemTarget, WebsiteMenuItemType};
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class WebsiteMenuItem extends Model implements AuditableContract
{
    use HasVuexyModelMetadata;
    use Auditable;

    // ===================== METADATOS =====================

    public string $sortColumn        = 'order';
    public string $defaultSortOrder  = 'desc';
    public string $singularName      = 'Elemento de menú';
    public string $focusColumnOnOpen = 'title';

    // ===================== CONFIGURACIÓN =====================

    protected $fillable = [
        'menu_id',
        'parent_id',
        'title',
        'type',

        'linkable_id',
        'linkable_type',
        'laravel_route',
        'url',
        'method',
        'target',
        'js_event',

        'icon',
        'badge',
        'badge_color',

        'roles',
        'permissions',
        'hide_if_authenticated',
        'hide_if_guest',
        'visible_from',
        'visible_until',

        'order',
        'is_active',

        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'title'       => 'array',
        'type'        => WebsiteMenuItemType::class,
        'target'      => WebsiteMenuItemTarget::class,
        'roles'       => 'array',
        'permissions' => 'array',
        'hide_if_authenticated' => 'boolean',
        'hide_if_guest'         => 'boolean',
        'is_active'   => 'boolean',
    ];

    protected $auditInclude = [
        'menu_id',
        'parent_id',
        'title',
        'type',
        'linkable_id',
        'linkable_type',
        'laravel_route',
        'url',
        'method',
        'target',
        'js_event',
        'icon',
        'badge',
        'badge_color',
        'roles',
        'permissions',
        'hide_if_authenticated',
        'hide_if_guest',
        'visible_from',
        'visible_until',
        'order',
        'is_active',
    ];

    // ===================== RELACIONES =====================

    public function menu(): BelongsTo
    {
        return $this->belongsTo(WebsiteMenu::class, 'menu_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order');
    }

    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    // ===================== GETTERS =====================

    // Accesor para devolver título correcto según idioma actual
    public function getLocalizedTitleAttribute(): string
    {
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'es');

        return $this->title[$locale] ?? $this->title[$fallback] ?? '';
    }
}
