<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Koneko\KonekoVuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class BlogCategory extends Model implements AuditableContract
{
    use HasVuexyModelMetadata;
    use Auditable;

    // ===================== METADATOS =====================

    public string $sortColumn        = 'name';
    public string $defaultSortOrder  = 'asc';
    public string $singularName      = 'categoría';
    public string $focusColumnOnOpen = 'name';

    // ===================== CONFIGURACIÓN =====================

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $auditInclude = [
        'name',
        'slug',
        'parent_id',
        'description',
        'is_active',
    ];

    public function parent() : BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children() : HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    // ===================== GETTERS =====================

    public function getDisplayName(): string
    {
        return $this->name;
    }
}
