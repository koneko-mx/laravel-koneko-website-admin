<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Website\Layout\Builders;

use Illuminate\Support\Arr;
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
            'site_id'     => $this->site->id,
            'page_id'     => $this->content->id,
            'template'    => $layout,
            'theme_color' => $theme,
        ]);
    }

    // ===================== SEO =====================
    private function seo(): array
    {
        $title = $this->content ? $this->content->getEffectiveTitle($this->site) : $this->site->title;

        $author  = $this->pickMeta('author', $this->contentSeo?->author_mode ?? $this->siteSeo?->author_mode);
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
                $value = $links[$case->value] ?? null;
                if (!filled($value)) {
                    return null;
                }

                return [
                    'key'      => $case->value,
                    'url'      => $value,
                    'label'    => $case->label(),
                    'icon_ti'  => $case->icon(),
                    'icon_fa'  => method_exists($case, 'iconFA') ? $case->iconFA() : null,
                    'color'    => $case->color(),
                ];
            })
            ->filter()
            ->mapWithKeys(function (array $row) {
                $key = $row['key'];
                return [$key => Arr::except($row, 'key')]; // ahora la clave es string
            })
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
        $config   = (array) ($chat[$provider] ?? []);

        // Armar contexto de macros
        $siteName   = (string) $this->site->brand_name;
        $pageTitle  = (string) ($this->content?->getEffectiveTitle($this->site) ?? $this->site->title);
        $pageUrl    = (string) ($this->content?->canonical_url ?? url()->current());

        // WhatsApp: producir wa_url + wa_digits aplicando macros y normalizando
        if ($provider === 'whatsapp') {
            $rawPhone  = (string) ($config['wa_phone'] ?? '');
            $greeting  = (string) ($config['wa_greeting'] ?? '');
            $message   = strtr($greeting, [
                '{site}'  => $siteName,
                '{title}' => $pageTitle,
                '{url}'   => $pageUrl,
            ]);

            // Normaliza a E.164 si es posible; wa.me necesita dígitos sin '+'
            [$e164, $digits] = $this->normalizePhoneForWa($rawPhone);
            $waUrl = $digits
                ? ('https://wa.me/' . $digits . ($message !== '' ? ('?text=' . rawurlencode($message)) : ''))
                : null;

            $config['wa_e164']  = $e164;
            $config['wa_digits']= $digits;
            $config['wa_url']   = $waUrl;
            $config['wa_message_resolved'] = $message;
        }

        return $this->clean([
            'provider' => $provider,
            'config'   => $config,
        ]);
    }

    /** Limpieza flexible: 00→+, quita separadores, corrige +521…→+52…, retorna [e164, digits] o [null,null] */
    private function normalizePhoneForWa(?string $raw): array
    {
        $v = trim((string)$raw);
        if ($v === '') return [null, null];

        // 00xx → +xx
        if (str_starts_with($v, '00')) {
            $v = '+' . substr($v, 2);
        }
        // quita separadores visuales
        $v = preg_replace('/[\s().-]+/', '', $v);
        // asegurar un solo '+'
        if (str_contains($v, '+')) {
            $v = '+' . ltrim($v, '+');
        }
        // corrige MX legado
        $v = preg_replace('/^\+521(\d{10})$/', '+52$1', $v);

        // E.164 directo
        if (preg_match('/^\+[1-9]\d{7,14}$/', $v)) {
            return [$v, ltrim($v, '+')];
        }

        // 10 dígitos → asume MX +52 (ajusta si necesitas por sitio)
        if (preg_match('/^\d{10}$/', $v)) {
            $e164 = '+52' . $v;
            return [$e164, '52' . $v];
        }

        // 1+10 (US/CA)
        if (preg_match('/^1\d{10}$/', $v)) {
            $e164 = '+' . $v;
            return [$e164, ltrim($e164, '+')];
        }

        return [null, null];
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
        $copyright = $this->pickMeta('copyright', $this->contentSeo?->copyright_mode ?? $this->siteSeo?->copyright_mode);

        return $this->clean([
            'name'   => $this->site->brand_name,
            'slogan' => $this->site->slogan,
            'copyright' => $copyright,
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
