<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Koneko\KonekoVuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Faq extends Model implements AuditableContract
{
    use HasVuexyModelMetadata;
    use Auditable;

    // ===================== METADATOS =====================

    public string $sortColumn        = 'name';
    public string $defaultSortOrder  = 'asc';
    public string $singularName      = 'faq';
    public string $focusColumnOnOpen = 'name';

    // ===================== CONFIGURACIÓN =====================

    protected $fillable = [
        'category_id',
        'question',
        'answer',
        'order',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $auditInclude = [
        'category_id',
        'question',
        'answer',
        'order',
        'is_active',
    ];

    // ===================== RELACIONES =====================

    public function category(): BelongsTo
    {
        return $this->belongsTo(FaqCategory::class, 'category_id');
    }

    // ===================== GETTERS =====================

    public function getDisplayName(): string
    {
        return (string) $this->question;
    }
}
