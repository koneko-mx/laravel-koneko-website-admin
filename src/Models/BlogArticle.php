<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Koneko\KonekoVuexyAdmin\Models\User;
use Koneko\KonekoVuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class BlogArticle extends Model implements AuditableContract
{
    use HasVuexyModelMetadata;
    use Auditable;

    // ===================== METADATOS =====================

    public string $sortColumn        = 'title';
    public string $defaultSortOrder  = 'asc';
    public string $singularName      = 'artículo';
    public string $focusColumnOnOpen = 'title';

    // ===================== CONFIGURACIÓN =====================

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'metadata',
        'is_published',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected $auditInclude = [
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'metadata',
        'is_published',
        'published_at',
        'updated_by',
    ];

    public function category() : BelongsTo
    {
        return $this->belongsTo(BlogCategory::class);
    }

    public function tags() : BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_article_tag');
    }

    public function comments() : HasMany
    {
        return $this->hasMany(BlogComment::class);
    }

    public function creator() : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater() : BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ===================== GETTERS =====================

    public function getDisplayName(): string
    {
        return $this->title;
    }
}
