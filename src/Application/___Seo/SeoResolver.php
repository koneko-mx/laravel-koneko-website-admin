<?php

namespace Koneko\KonekoWebsiteAdmin\Application\Seo;

use Koneko\KonekoWebsiteAdmin\Application\Enums\WebsiteSeoProfile\MetaMode;
use Koneko\KonekoWebsiteAdmin\Models\{WebsiteSite, WebsiteContent, WebsiteSeoProfile};

final class SeoResolver {
    public static function effectiveOg(
        ?WebsiteSeoProfile $site, ?WebsiteSeoProfile $content,
        ?string $computedPageUrl, ?string $computedSiteUrl
    ): array {
        // 1) Decide fuente según modo del content
        if ($content) {
            $mode = $content->og_mode ?? MetaMode::Inherit;
            if ($mode === MetaMode::Disable) return [];               // nada
            if ($mode === MetaMode::Override) $src = $content; else $src = $site;
        } else {
            // Página sin content (home/blog listing…)
            $src = ($site && ($site->og_mode ?? MetaMode::Override) !== MetaMode::Disable) ? $site : null;
        }
        if (!$src) return [];

        // 2) Construye OG con fallback de URL calculada si está vacía
        $og = [
            'og:type'        => $src->og_type        ?: 'website',
            'og:title'       => $src->og_title       ?: null,
            'og:description' => $src->og_description ?: null,
            'og:image'       => $src->og_image       ?: null,
            'og:url'         => $src->og_url         ?: ($computedPageUrl ?? $computedSiteUrl),
            'og:site_name'   => $src->og_site_name   ?: null,
        ];

        // Limpia nulls
        return array_filter($og, fn($v) => !is_null($v));
    }
}
