<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Koneko\KonekoVuexyAdmin\Support\Traits\Audit\{HasCreator, HasUpdater};
use Koneko\KonekoVuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;

class WebsiteContentBlock extends Model
{
    use HasVuexyModelMetadata;
    use HasCreator,
        HasUpdater;

    // ===================== METADATOS =====================

    public string $sortColumn        = 'slug';
    public string $defaultSortOrder  = 'asc';
    public string $singularName      = 'bloque de contenido';
    public string $focusColumnOnOpen = 'slug';

    // ===================== CONFIGURACIÓN =====================

    protected $fillable = [
        'slug',
        'description',
        'keywords',
        'data',
        'settings',
        'enable_cache',
        'cache_ttl',
    ];

    protected $casts = [
        'data'         => 'array',
        'settings'     => 'array',
        'keywords'       => 'array',
        'enable_cache' => 'boolean',
    ];

    public function versions() : HasMany
    {
        return $this->hasMany(WebsiteContentVersion::class);
    }

    // ===================== GETTERS =====================

    public function getDisplayName(): string
    {
        return $this->slug;
    }

}
