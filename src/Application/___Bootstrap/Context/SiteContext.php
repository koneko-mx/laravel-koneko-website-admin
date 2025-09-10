<?php

namespace Koneko\KonekoWebsiteAdmin\Application\Bootstrap\Context;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Koneko\KonekoWebsiteAdmin\Models\{WebsiteContent, WebsiteSite, WebsiteSeoProfile};

/**
 * 🧠 Resolve contexto de sitio activo en modo multisite
 */
class SiteContext_legacy
{
    public ?WebsiteSite $website = null;
    public ?WebsiteContent $content = null;

    public function __construct(Request $request)
    {
        $host = ltrim($request->getHost(), 'www.');
        $this->website = WebsiteSite::where('domain', $host)->first();

        if (!$this->website) {
            return;
        }

        $slug = (string) $request->route('slug');

        $this->content = WebsiteContent::where('site_id', $this->website->id)
            ->bySlug($slug)
            ->first();
    }

    /*
    public function getTemplate(): ?WebsiteTemplate
    {
        return $this->template;
    }

    public function getContent(): ?WebsiteContent
    {
        return $this->content;
    }

    public function getWebsite(): ?WebsiteSite
    {
        return $this->website;
    }
    */

    public function getLayout(): array
    {
        return [
            'package'     => $this->website->package,
            'template'    => $this->website->layout,
            'theme-color' => $this->getThemeColor(),
        ];
    }

    public function getSeo(): array
    {
        return [
            'title'       => $this->getTitle(),
            'description' => $this->getDescription(),
            'keywords'    => $this->getKeywords(),
            'author'      => $this->getAuthor(),
            'copyright'   => $this->getCopyright(),
            'robots'      => $this->getRobots(),
            'language'    => $this->getLanguage(),
            'canonical'   => $this->getCanonicalUrl(),
            'hreflangs'   => $this->getHreflangs(),
            'og'          => $this->getOg(),
            'twitter'     => $this->getTwitter(),
            'favicon'     => $this->getFavicon(),
        ];
    }

    public function getSocial(): array
    {
        return [];
    }

    public function getContact(): array
    {
        return [];
    }

    public function getContent(): string
    {
        return '';
    }

    public function getHeaderBlocks(): array
    {
        return $this->content->header_blocks ?? [];
    }

    public function getContentBlocks(): array
    {
        return $this->content->content_blocks ?? [];
    }

    public function getFooterBlocks(): array
    {
        return $this->content->footer_blocks ?? [];
    }

    private function getDescription(): string
    {
        return $this->content->description ?? $this->website->description;
    }

    private function getKeywords(): array
    {
        return $this->content->keywords;
    }

    private function getLanguage(): string
    {
        return $this->content->seo_profile && $this->content->seo_profile->overwrite_locale
            ? $this->content->seo_profile->locale
            : $this->website->seo_profile->locale ?? 'Es';
    }

    private function getCanonicalUrl(): ?string
    {
        return $this->content->canonical_url ?? null;
    }

    private function getHreflangs(): array
    {
        return [];
    }

    private function getFavicon(): array
    {
        return [];
    }

    private function getThemeColor(): string
    {
        return $this-> $this->website->theme_color;
    }

    private function getManifest(): array
    {
        return [];
    }

    private function getOg(): array
    {
        return [];
    }

    private function getTwitter(): array
    {
        return [];
    }
    private function getLdJson(): array
    {
        return [];
    }

    private function getRobots(): string
    {
        $index  = !$this->website->noindex;
        $follow = !$this->website->nofollow;

        if ($this->website->allow_overwrite_robots) {
            $index  = !$this->content->noindex;
            $follow = !$this->content->nofollow;
        }

        return $index ? 'index' : 'noindex' . ', ' . ($follow ? 'follow' : 'nofollow');
    }

    private function getTitle(): string
    {
        $title = trim($this->content->title);

        return $this->website->title . ($title ? ' | ' . $title : '');
    }

    private function getAuthor(): string
    {
        return $this->content->author ?? $this->website->author;
    }

    private function getCopyright(): string
    {
        return $this->content->copyright ?? $this->website->copyright;
    }

    /**
     * Devuelve el sitio actual (usando cache interna si ya fue resuelto)
     */
    /*
    public function resolve(): ?WebsiteContent
    {
        return static::$resolved;
    }
    */

    /**
     * Resuelve el sitio activo desde el request actual (dominio o path)
     */
    /*
    public function resolveFromRequest(Request $request): bool
    {
        $host = ltrim($request->getHost(), 'www.');
        $site = WebsiteSite::where('domain', $host)->first();

        if (!$site) {
            return false;
        }

        $slug    = (string) $request->route('slug');
        $content = WebsiteContent::where('site_id', $site->id)
            ->bySlug($slug)
            ->first();

        if (!$content) {
            return false;
        }

        // Establece contexto (null si no hay match)
        $this->resolved = $content;

        return true;
    }
    */


    function robotsDirectives(WebsiteSite $site, ?WebsiteContent $content): string {
        // suspended => bloqueo total
        if ($site->robots_mode === 'suspended') return 'noindex,nofollow';

        if ($site->robots_mode === 'site') {
            return ($site->site_noindex ? 'noindex' : 'index') . ',' .
                   ($site->site_nofollow ? 'nofollow' : 'follow');
        }

        // 'content' (por defecto): cae al contenido si existe, si no al site
        $noindex  = $content?->noindex ?? $site->site_noindex;
        $nofollow = $content?->nofollow ?? $site->site_nofollow;

        return ($noindex ? 'noindex' : 'index') . ',' . ($nofollow ? 'nofollow' : 'follow');
    }

    function resolveRobots(WebsiteSite $site, ?WebsiteContent $content): string {
        if ($site->robots_mode === 'suspended') {
          return 'noindex, nofollow';
        }
        if ($site->robots_mode === 'site') {
          return ($site->site_noindex ? 'noindex' : 'index')
               . ', ' . ($site->site_nofollow ? 'nofollow' : 'follow');
        }
        // mode === 'content'
        $index  = $content?->noindex  ?? false;
        $follow = $content?->nofollow ?? false;
        return ($index ? 'noindex' : 'index') . ', ' . ($follow ? 'nofollow' : 'follow');
    }

}
