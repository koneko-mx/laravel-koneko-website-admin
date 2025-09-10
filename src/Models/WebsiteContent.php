<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Models;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Support\Facades\{Auth, URL};
use Koneko\KonekoVuexyAdmin\Support\Traits\Audit\{HasCreator, HasUpdater};
use Koneko\KonekoVuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;
use Koneko\KonekoWebsiteAdmin\Application\Enums\WebsiteContents\{WebsiteContentStatus, WebsiteContentType};

class WebsiteContent extends Model
{
    use HasVuexyModelMetadata;
    use HasCreator,
        HasUpdater;

    // ===================== METADATOS =====================

    public string $sortColumn        = 'title';
    public string $defaultSortOrder  = 'asc';
    public string $singularName      = 'página';
    public string $focusColumnOnOpen = 'title';

    // ===================== CONFIGURACIÓN =====================

    protected $fillable = [
        'site_id','type',
        'title','slug','description','keywords',
        'canonical_url',
        'noindex','nofollow',
        'header_blocks','content_blocks','footer_blocks',
        'roles','permissions','hide_if_authenticated','hide_if_guest',
        'visible_from','visible_until',
        'status',
        'enable_cache','cache_ttl',
        'created_by','updated_by'
      ];

      protected $casts = [
        'type'              => WebsiteContentType::class,
        'keywords'          => 'array',
        'noindex'           => 'boolean',
        'nofollow'          => 'boolean',
        'header_blocks'     => 'array',
        'content_blocks'    => 'array',
        'footer_blocks'     => 'array',
        'roles'             => 'array',
        'permissions'       => 'array',
        'hide_if_authenticated' => 'boolean',
        'hide_if_guest'     => 'boolean',
        'visible_from'      => 'datetime',
        'visible_until'     => 'datetime',
        'status'            => WebsiteContentStatus::class,
        'enable_cache'      => 'boolean',
        'cache_ttl'         => 'integer',
      ];

    protected $auditInclude = [
    ];

    // ===================== RELACIONES =====================

    public function site() : BelongsTo
    {
        return $this->belongsTo(WebsiteSite::class);
    }

    public function versions() : HasMany
    {
        return $this->hasMany(WebsiteContentVersion::class);
    }

    public function seoProfile() { return $this->morphOne(WebsiteSeoProfile::class, 'seoable'); }



    // ===================== GETTERS =====================

    public function getDisplayName(): string
    {
        return (string) $this->title;
    }

    public function getEffectiveTitle(?WebsiteSite $site = null): string
    {
        $titleDomain = $site?->title?? $this->site?->title?? config('app.name');

        return $titleDomain . ($this->title ? ' | ' . $this->title : '');
    }

    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Str::slug((string) $value, '-')
        );
    }

    /*
    public function toHtml(): string
    {
        return view('website::templates.' . ($this->template ?? 'default'), [
            'content' => $this,
        ])->render();
    }
    */

    // ===================== SCOPES =====================

    // Scopes coherentes con enum
    public function scopePublished($query): Builder {
        return $query
        ->where('status', WebsiteContentStatus::Published->value)
        ->where(fn($q)=>$q->whereNull('visible_from')->orWhere('visible_from','<=',now()))
        ->where(fn($q)=>$q->whereNull('visible_until')->orWhere('visible_until','>=',now()));
    }

    public function scopeBySlug($query, string $slug) : Builder
    {
        return $query->where('slug', $slug);
    }

    public function scopeDraft($q): Builder {
        return $q->where('status', WebsiteContentStatus::Draft->value);
    }


    // ===================== PREVIEW =====================

    public function previewUrl(?int $userId = null, int $ttl = 30): string
    {
        return URL::temporarySignedRoute(
            'website.preview',
            now()->addMinutes($ttl),
            [
                'slug' => $this->slug,
                'user_id' => $userId ?? Auth::id()
            ]
        );
    }

}
