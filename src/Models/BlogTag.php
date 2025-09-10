<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Koneko\KonekoVuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class BlogTag extends Model implements AuditableContract
{
    use HasVuexyModelMetadata;
    use Auditable;

    // ===================== METADATOS =====================

    public string $sortColumn        = 'name';
    public string $defaultSortOrder  = 'asc';
    public string $singularName      = 'etiqueta';
    public string $focusColumnOnOpen = 'name';

    // ===================== CONFIGURACIÓN =====================

    protected $fillable = [
        'name',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $auditInclude = [
        'name',
        'slug',
        'is_active',
    ];

    // ===================== GETTERS =====================

    public function getDisplayName(): string
    {
        return $this->name;
    }
}
