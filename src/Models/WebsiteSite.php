<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Koneko\KonekoVuexyAdmin\Support\Traits\Audit\{HasCreator,HasUpdater};
use Koneko\KonekoVuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;
use Koneko\KonekoWebsiteAdmin\Application\Enums\Websites\{WebsiteRobotsMode, WebsiteSiteStatus};

class WebsiteSite extends Model
{
    use HasVuexyModelMetadata;
    use HasCreator,
        HasUpdater;

    // ===================== METADATOS =====================

    public string $sortColumn        = 'domain';
    public string $defaultSortOrder  = 'asc';
    public string $singularName      = 'sitio web';
    public string $focusColumnOnOpen = 'domain';

    // ===================== CONFIGURACIÓN =====================

    protected $fillable = [
        'domain','title',
        'brand_name','slogan',
        'robots_mode','www_redirect','force_https',
        'status',
        'coming_soon_content_id','maintenance_content_id',
        'created_by','updated_by'
    ];

    protected $casts = [
        'robots_mode'    => WebsiteRobotsMode::class,
        'www_redirect'   => 'boolean',
        'force_https'    => 'boolean',
        'status'         => WebsiteSiteStatus::class,
    ];

    // ===================== RELACIONES =====================

    public function menus(): HasMany
    {
        return $this->hasMany(WebsiteMenu::class, 'site_id');
    }

    public function contents(): HasMany
    {
        return $this->hasMany(WebsiteContent::class, 'site_id');
    }
    public function comingSoon(): BelongsTo {
        return $this->belongsTo(WebsiteContent::class, 'coming_soon_content_id');
    }

    public function maintenance(): BelongsTo {
        return $this->belongsTo(WebsiteContent::class, 'maintenance_content_id');
    }

    public function seoProfile() { return $this->morphOne(WebsiteSeoProfile::class, 'seoable'); }


    // ===================== GETTERS =====================

    public function getDisplayName(): string
    {
        return (string) $this->domain;
    }

    /**
     * Get the full URL for the site's domain with the correct protocol
     *
     * @return string
     */
    public function getFullDomainUrl(): string
    {
        $protocol = $this->force_https ? 'https' : (request()->secure() ? 'https' : 'http');
        return "{$protocol}://{$this->domain}";
    }
}
