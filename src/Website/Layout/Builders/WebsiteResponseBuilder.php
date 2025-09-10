<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Website\Layout\Builders;

use Illuminate\Support\Facades\Storage;
use Koneko\KonekoWebsiteAdmin\Application\Enums\Websites\{Social, WebsiteRobotsMode};
use Koneko\KonekoWebsiteAdmin\Application\Enums\WebsiteSeoProfile\WebsiteSeoProfileMetaMode;
use Koneko\KonekoWebsiteAdmin\Models\{WebsiteSite, WebsiteContent, WebsiteSeoProfile};

/**
 * Construye el payload final que el middleware comparte con las vistas públicas.
 * - Aplica prioridades de meta (site/content/disable) cuando existan WebsiteSeoProfile
 * - Inserta settings agregados (WebsiteSettingsLoader)
 * - Evita nulls y mantiene estructura simple y predecible
 */
final class WebsiteResponseBuilder
{
    private WebsiteSite $site;
    private ?WebsiteContent $content;
    private array $settings; // bloque agregado de WebsiteSettingsLoader

    private ?WebsiteSeoProfile $siteSeo;
    private ?WebsiteSeoProfile $contentSeo;

    private function __construct(WebsiteSite $site, ?WebsiteContent $content, array $settings)
    {
        $this->site       = $site;
        $this->content    = $content;
        $this->settings   = $settings;
        $this->siteSeo    = $site->seoProfile;
        $this->contentSeo = $content?->seoProfile;
    }

    public static function make(WebsiteSite $site, ?WebsiteContent $content, array $settings): self
    {
        return new self($site, $content, $settings);
    }

    /**
     * Payload listo para View::share([...])
     */
    public function build(): array
    {
        return [
            '_layout'  => $this->layout(),
            '_seo'     => $this->seo(),
            '_social'  => $this->social(),
            '_contact' => $this->contact(),
            '_chat'    => $this->chat(),
            '_img'     => $this->img(),
            '_brand'   => $this->brand(),
            '_blocks'  => [
                'header'  => $this->content?->header_blocks ?? [],
                'content' => $this->content?->content_blocks ?? [],
                'footer'  => $this->content?->footer_blocks ?? [],
            ],
        ];
    }

    // ===================== Layout =====================
    private function layout(): array
    {
        // Template desde SEO profile (template_mode) o defaults del sitio
        $tplMode = $this->contentSeo?->template_mode ?? $this->siteSeo?->template_mode;
        $package = $this->pickMeta('package', $tplMode);
        $layout  = $this->pickMeta('layout',  $tplMode);
        $theme   = $this->pickMeta('theme_color', $tplMode) ?? null;

        return $this->clean([
            'package'     => $package,
            'template'    => $layout,
            'theme_color' => $theme,
        ]);
    }

    // ===================== SEO =====================
    private function seo(): array
    {
        $title = $this->content ? $this->content->getEffectiveTitle($this->site) : $this->site->title;

        $author = $this->pickMeta('author', $this->contentSeo?->author_mode ?? $this->siteSeo?->author_mode);
        $copyright = $this->pickMeta('copyright', $this->contentSeo?->copyright_mode ?? $this->siteSeo?->copyright_mode);

        $locale  = $this->pickMeta('locale',  $this->contentSeo?->locale_mode  ?? $this->siteSeo?->locale_mode);
        $favicon = $this->favicon();

        $robots = $this->robots();

        $og = $this->clean([
            'type'        => $this->pickMeta('og_type',        $this->contentSeo?->og_mode ?? $this->siteSeo?->og_mode),
            'title'       => $this->pickMeta('og_title',       $this->contentSeo?->og_mode ?? $this->siteSeo?->og_mode) ?? $title,
            'description' => $this->pickMeta('og_description', $this->contentSeo?->og_mode ?? $this->siteSeo?->og_mode) ?? ($this->content->description ?? null),
            'image'       => $this->pickMeta('og_image',       $this->contentSeo?->og_mode ?? $this->siteSeo?->og_mode),
            'url'         => $this->pickMeta('og_url',         $this->contentSeo?->og_mode ?? $this->siteSeo?->og_mode),
            'site_name'   => $this->pickMeta('og_site_name',   $this->contentSeo?->og_mode ?? $this->siteSeo?->og_mode) ?? $this->site->brand_name,
        ]);

        $twitter = $this->clean([
            'card'        => $this->pickMeta('twitter_card',        $this->contentSeo?->twitter_mode ?? $this->siteSeo?->twitter_mode),
            'title'       => $this->pickMeta('twitter_title',       $this->contentSeo?->twitter_mode ?? $this->siteSeo?->twitter_mode) ?? $title,
            'description' => $this->pickMeta('twitter_description', $this->contentSeo?->twitter_mode ?? $this->siteSeo?->twitter_mode) ?? ($this->content->description ?? null),
            'image'       => $this->pickMeta('twitter_image',       $this->contentSeo?->twitter_mode ?? $this->siteSeo?->twitter_mode),
            'site'        => $this->pickMeta('twitter_site',        $this->contentSeo?->twitter_mode ?? $this->siteSeo?->twitter_mode),
            'creator'     => $this->pickMeta('twitter_creator',     $this->contentSeo?->twitter_mode ?? $this->siteSeo?->twitter_mode),
        ]);

        return $this->clean([
            'title'       => $title,
            'description' => $this->content->description ?? null,
            'keywords'    => $this->content->keywords ?? [],
            'author'      => $author,
            'copyright'   => $copyright,
            'robots'      => $robots,
            'canonical'   => $this->content->canonical_url ?? null,
            'language'    => $locale,
            'favicon'     => $favicon,
            'og'          => $og,
            'twitter'     => $twitter,
        ]);
    }

    // ===================== Social / Contact / Chat (desde settings) =====================
    private function social(): array
    {
        $links = $this->settings['social']['links'] ?? [];

        return collect(Social::cases())
            ->map(function ($case) use ($links) {
                $value = $links[$case->value] ?? null;   // p.ej. 'whatsapp', 'facebook', ...
                if (!filled($value)) {
                    return null;
                }

                return [
                    'key'      => $case->value,
                    'url'      => $value,
                    'label'    => $case->label(),
                    'icon_ti'  => $case->icon(),     // Tabler
                    'icon_fa'  => method_exists($case, 'iconFA') ? $case->iconFA() : null, // si agregas FA
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function contact(): array
    {
        return $this->clean([
            'info'     => $this->settings['contact']['info']     ?? [],
            'form'     => $this->settings['contact']['form']     ?? [],
            'location' => $this->settings['contact']['location'] ?? [],
            'branches' => $this->settings['contact']['branches'] ?? ['items' => []],
        ]);
    }

    private function chat(): array
    {
        $chat = $this->settings['chat'] ?? [];
        $provider = $chat['default']['chat_provider'] ?? 'none';
        return $this->clean([
            'provider' => $provider,
            'config'   => $chat[$provider] ?? [],
        ]);
    }

    private function img(): array
    {
        $img = $this->settings['img'] ?? [];

        // Fallback de marca si faltan logos:
        $img['brand'] = $img['brand']
            ? $img['brand']
            : [
            'logo' => [
                'small'  => vendor_or_url(config('koneko.branding.app_logo')),
                'medium' => vendor_or_url(config('koneko.branding.app_logo')),
                'large'  => vendor_or_url(config('koneko.branding.app_logo')),
            ],
            'logo_h' => [
                'small'  => vendor_or_url(config('koneko.branding.app_logo_h')),
                'medium' => vendor_or_url(config('koneko.branding.app_logo_h')),
                'large'  => vendor_or_url(config('koneko.branding.app_logo_h')),
            ],
            'logo_dark' => [
                'small'  => vendor_or_url(config('koneko.branding.app_logo_dark')),
                'medium' => vendor_or_url(config('koneko.branding.app_logo_dark')),
                'large'  => vendor_or_url(config('koneko.branding.app_logo_dark')),
            ],
            'logo_h_dark' => [
                'small'  => vendor_or_url(config('koneko.branding.app_logo_h_dark')),
                'medium' => vendor_or_url(config('koneko.branding.app_logo_h_dark')),
                'large'  => vendor_or_url(config('koneko.branding.app_logo_h_dark')),
            ],
        ];

        return $this->clean($img);
    }

    private function brand(): array
    {
        return $this->clean([
            'name'   => $this->site->brand_name,
            'slogan' => $this->site->slogan,
        ]);
    }

    // ===================== Helpers =====================
    private function robots(): string
    {
        $mode = $this->site->robots_mode?->value ?? WebsiteRobotsMode::Content->value;
        if ($mode === WebsiteRobotsMode::Suspended->value) {
            return 'noindex, nofollow';
        }
        if ($mode === WebsiteRobotsMode::Site->value) {
            // No existen banderas site_noindex/nofollow en el schema actual → default amistoso
            return 'index, follow';
        }
        // Content mode
        $noindex  = (bool) ($this->content?->noindex ?? false);
        $nofollow = (bool) ($this->content?->nofollow ?? false);
        return ($noindex ? 'noindex' : 'index') . ', ' . ($nofollow ? 'nofollow' : 'follow');
    }

    private function favicon(): array
    {
        $data = (array) $this->pickMeta('favicon', $this->contentSeo?->favicon_mode ?? $this->siteSeo?->favicon_mode);

        $disk = config('koneko.media.default_disk','public');
        $base = config('koneko.media.paths.favicon','favicon-website/');
        $favicon_sizes = config('koneko.media.favicon_sizes');

        $out = [];
        foreach ($favicon_sizes as $size => $_) {
            $out[$size] = !empty($data[$size])
                ? Storage::disk($disk)->url($base.$data[$size])
                : vendor_or_url(config('koneko.branding.favicon','favicon.ico'));
        }
        return $out;
    }


    /**
     * Aplica modo (site/content/disable) y extrae campo de SEO Profile.
     */
    private function pickMeta(string $field, ?WebsiteSeoProfileMetaMode $mode): mixed
    {
        if ($mode === WebsiteSeoProfileMetaMode::Disable) {
            return null;
        }
        if ($mode === WebsiteSeoProfileMetaMode::Content) {
            return $this->contentSeo?->{$field} ?? null;
        }
        // Site (default)
        return $this->siteSeo?->{$field} ?? null;
    }

    /** Limpia nulls/strings vacíos/arrays vacíos */
    private function clean(array $in): array
    {
        return array_filter($in, static function ($v) {
            if ($v === null)   return false;
            if (is_string($v)) return trim($v) !== '';
            if (is_array($v))  return $v !== [];
            return true;
        });
    }
}
