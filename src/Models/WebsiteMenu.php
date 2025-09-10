<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Koneko\KonekoVuexyAdmin\Support\Traits\Audit\{HasCreator, HasUpdater};
use Koneko\KonekoVuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class WebsiteMenu extends Model implements AuditableContract
{
    use HasVuexyModelMetadata;
    use Auditable;
    use HasCreator,
        HasUpdater;

    // ===================== METADATOS =====================

    public string $sortColumn        = 'title';
    public string $defaultSortOrder  = 'asc';
    public string $singularName      = 'menú';
    public string $focusColumnOnOpen = 'title';

    // ===================== CONFIGURACIÓN =====================

    protected $fillable = [
        'site_id',
        'title',
        'slug',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $auditInclude = [
        'site_id',
        'title',
        'slug',
        'description',
        'is_active',
    ];

    // ===================== GETTERS =====================

    public function items(): HasMany
    {
        return $this->hasMany(WebsiteMenuItem::class, 'menu_id')
            ->whereNull('parent_id')
            ->orderBy('order');
    }

    public function getDisplayName(): string
    {
        return $this->title;
    }

    // ===================== RELACIONES =====================

    public function site(): BelongsTo
    {
        return $this->belongsTo(WebsiteSite::class);
    }
}
