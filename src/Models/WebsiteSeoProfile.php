<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Koneko\KonekoWebsiteAdmin\Application\Enums\WebsiteSeoProfile\{WebsiteSeoProfileScope,WebsiteSeoProfileMetaMode};

class WebsiteSeoProfile extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'seoable_type','seoable_id','scope',
        'author_mode','author',
        'copyright_mode','copyright',
        'schema_mode','schema_org',
        'favicon_mode','favicon',
        'locale_mode','locale',
        'template_mode','package','layout','theme_color',
        'og_mode','og_type','og_title','og_description','og_image','og_url','og_site_name',
        'twitter_mode','twitter_card','twitter_title','twitter_description','twitter_image','twitter_site','twitter_creator',
    ];

    protected $casts = [
        'scope'          => WebsiteSeoProfileScope::class,
        'author_mode'    => WebsiteSeoProfileMetaMode::class,
        'copyright_mode' => WebsiteSeoProfileMetaMode::class,
        'template_mode'  => WebsiteSeoProfileMetaMode::class,
        'schema_mode'    => WebsiteSeoProfileMetaMode::class,
        'schema_org'     => 'array',
        'favicon_mode'   => WebsiteSeoProfileMetaMode::class,
        'favicon'        => 'array',
        'locale_mode'    => WebsiteSeoProfileMetaMode::class,
        'og_mode'        => WebsiteSeoProfileMetaMode::class,
        'twitter_mode'   => WebsiteSeoProfileMetaMode::class,
    ];

    public function seoable() {
        return $this->morphTo();
    }

}
