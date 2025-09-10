<?php

namespace Koneko\KonekoWebsiteAdmin\Application\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Koneko\KonekoWebsiteAdmin\Application\Bootstrap\Context\SiteContext;
use Koneko\KonekoWebsiteAdmin\Application\Cache\Builders\WebsiteLayoutVarsBuilder;
use Koneko\KonekoWebsiteAdmin\Application\Cache\Builders\WebsiteSeoVarsBuilder;
use Koneko\KonekoWebsiteAdmin\Application\Cache\Builders\WebsiteSocialVarsBuilder;

/**
 * 🌐 Middleware para detectar el sitio activo y compartir variables públicas (multisite).
 */
class WebsiteContextMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!str_contains($request->header('Accept'), 'text/html')) {
            return $next($request);
        }

        // Detecta el sitio activo desde dominio/subdominio/path
        $site = SiteContext::resolveFromRequest($request);

        $layout = [
            'template' => $site->template,
            'status' => $site->status,
            'is_indexable' => $site->is_indexable,
        ];

        $seo = [
            'canonical' => $site->canonical ?? '',
            'viewport' => $site->viewport ?? '',
            'title' => $site->title ?? '',
            'description' => $site->description ?? '',
            'robots' => $site->robots ?? '',
            'language' => $site->language ?? '',
            'author' => $site->author ?? '',
            'og:title' => $site->og_title ?? '',
            'og:site_name' => $site->og_site_name ?? '',
            'og:url' => $site->og_url ?? '',
            'og:description' => $site->og_description ?? '',
            'og:type' => $site->og_type ?? '',
            'og:image' => $site->og_image ?? '',
            'twitter:card' => $site->twitter_card ?? '',
            'twitter:site' => $site->twitter_site ?? '',
            'twitter:creator' => $site->twitter_creator ?? '',
            'favicon' => [
                '180x180' => $site->favicon['180x180'] ?? '',
                '152x152' => $site->favicon['152x152'] ?? '',
                '120x120' => $site->favicon['120x120'] ?? '',
                '76x76' => $site->favicon['76x76'] ?? '',
                '192x192' => $site->favicon['192x192'] ?? '',
                '16x16' => $site->favicon['16x16'] ?? '',
            ],
            'theme-color' => $site->theme_color ?? '',
            'ld+json' => $site->ld_json ?? '',
        ];

        $social = [
            'twitter:card' => $site->twitter_card ?? '',
            'twitter:site' => $site->twitter_site ?? '',
            'twitter:creator' => $site->twitter_creator ?? '',
        ];

        $contact = [
            'email' => $site->contact_email ?? '',
            'phone' => $site->contact_phone ?? '',
            'address' => $site->contact_address ?? '',
        ];

        /*
        // Variables visuales generales (favicon, logo, layout, etc.)
        $layoutVars = app(WebsiteLayoutVarsBuilder::class)->forSite($site)->get();

        // Variables SEO (title, meta, index, canonical, etc.)
        $seoVars = app(WebsiteSeoVarsBuilder::class)->forSite($site)->get();

        // Variables sociales (twitter, opengraph, etc.)
        $socialVars = app(WebsiteSocialVarsBuilder::class)->forSite($site)->get();
        */
        // Compartir a todas las vistas públicas
        View::share([
            '_layout'  => $layout,
            '_seo'     => $seo,
            '_social'  => $social,
            '_contact' => $contact,
        ]);

        return $next($request);
    }
}
