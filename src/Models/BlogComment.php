<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Koneko\KonekoVuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class BlogComment extends Model implements AuditableContract
{
    use HasVuexyModelMetadata;
    use Auditable;

    // ===================== METADATOS =====================

    public string $sortColumn        = 'created_at';
    public string $defaultSortOrder  = 'desc';
    public string $singularName      = 'comentario';
    public string $focusColumnOnOpen = 'created_at';

    // ===================== CONFIGURACIÓN =====================

    protected $fillable = [
        'blog_article_id',
        'author_name',
        'author_email',
        'comment',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    protected $auditInclude = [
        'comment',
        'is_approved',
    ];

    public function article() : BelongsTo
    {
        return $this->belongsTo(BlogArticle::class);
    }

    // ===================== GETTERS =====================

    public function getDisplayName(): string
    {
        return $this->comment;
    }
}
