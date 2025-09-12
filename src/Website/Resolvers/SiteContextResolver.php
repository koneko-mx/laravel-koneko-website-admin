<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Website\Resolvers;

use Illuminate\Http\Request;
use Koneko\KonekoWebsiteAdmin\Models\{WebsiteSite, WebsiteContent};

final class SiteContextResolver
{
    public function resolve(Request $request): ?array
    {
        $host = ltrim($request->getHost(), 'www.');
        $isPreview = $request->routeIs('website.preview');

        $site = WebsiteSite::query()
            ->select([
                'id','domain','title','brand_name','slogan',
                'robots_mode','www_redirect','force_https','status',
                'coming_soon_content_id','maintenance_content_id'
            ])
            ->with(['seoProfile' => function ($q) {
                $q->select(['id','seoable_type','seoable_id',
                    'scope','author_mode','author','copyright_mode','copyright',
                    'schema_mode','schema_org','favicon_mode','favicon',
                    'title_mode','title_format','template_mode','package','layout','theme_color',
                    'locale_mode','locale','og_mode','og_type','og_title','og_description','og_image','og_url','og_site_name',
                    'twitter_mode','twitter_card','twitter_title','twitter_description','twitter_image','twitter_site','twitter_creator'
                ]);
            }])
            ->where('domain', $host)
            ->first();

        if (!$site) return null;

        $slug = (string) $request->route('slug');

        // Si el sitio está en maintenance/coming_soon y NO es preview, intenta redirigir el contenido especial
        $forcedContentId = null;
        if (!$isPreview) {
            if ($site->status->value === 'maintenance' && $site->maintenance_content_id) {
                $forcedContentId = $site->maintenance_content_id;
            } elseif ($site->status->value === 'coming_soon' && $site->coming_soon_content_id) {
                $forcedContentId = $site->coming_soon_content_id;
            }
        }

        $contentQuery = WebsiteContent::query()
            ->select([
                'id','site_id','type','title','slug','description','keywords','canonical_url',
                'noindex','nofollow',
                'header_blocks','content_blocks','footer_blocks',
                'roles','permissions','hide_if_authenticated','hide_if_guest',
                'visible_from','visible_until','status',
                'enable_cache','cache_ttl'
            ])
            ->with(['seoProfile' => function ($q) {
                $q->select(['id','seoable_type','seoable_id',
                    'scope','author_mode','author','copyright_mode','copyright',
                    'schema_mode','schema_org','favicon_mode','favicon',
                    'title_mode','title_format','template_mode','package','layout','theme_color',
                    'locale_mode','locale','og_mode','og_type','og_title','og_description','og_image','og_url','og_site_name',
                    'twitter_mode','twitter_card','twitter_title','twitter_description','twitter_image','twitter_site','twitter_creator'
                ]);
            }])
            ->where('site_id', $site->id);

        if ($forcedContentId) {
            $contentQuery->where('id', $forcedContentId);
        } else {
            $contentQuery->where('slug', $slug);
        }

        $content = $contentQuery->first();
        //$content->status = 'published';

        return compact('site','content','isPreview','slug');
    }
}
