<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Website\UX\Template;

use Illuminate\Support\Facades\{Cache,Schema};
use Koneko\KonekoVuexyAdmin\Support\Traits\Cache\InteractsWithKonekoVarsCache;

class WebsiteVarsBuilderService
{
    use InteractsWithKonekoVarsCache;

    public const CACHE_WEBSITE_VARS_TAG = 'vuexy_website_settings_vars';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->initCacheConfig();
    }

    /**
     * Obtiene las variables de website principales.
     *
     * @param string|null $key
     * @return array|mixed
     */
    public function getWebsiteVars(?string $key = null): mixed
    {
        /*
        if (!Schema::hasTable('settings')) {
            return $this->getDefaultWebsiteVars($key);
        }
            */

        $websiteVars = $this->cacheOrCompute(self::CACHE_WEBSITE_VARS_TAG, function () {
            $settings = settings()->self()->getGroup();

            return $this->buildWebsiteVarsArray($settings);
        });

        return $key ? ($websiteVars[$key] ?? null) : $websiteVars;
    }

    /**
     * Construye las variables del website.
     */
    private function buildWebsiteVarsArray(array $settings): array
    {
        return [
            'title'       => $settings['title'] ?? config('koneko.title', 'Default Title'),
            'author'      => $settings['author'] ?? config('koneko.author', 'Default Author'),
            'description' => $settings['description'] ?? config('koneko.description', 'Default Description'),
            'favicon'     => $this->buildFaviconPaths($settings),
            'app_name'    => $settings['app_name'] ?? config('koneko.app_name', 'Default App Name'),
            'image_logo'  => $this->buildImageLogoPaths($settings),
        ];
    }

    /**
     * Construye las rutas de favicon.
     */
    private function buildFaviconPaths(array $settings): array
    {
        $namespace      = $settings['favicon_ns'] ?? null;
        $defaultFavicon = config('koneko.favicon', 'favicon.ico');

        return [
            'namespace' => $namespace,
            '16x16'     => $namespace ? "{$namespace}_16x16.png" : $defaultFavicon,
            '76x76'     => $namespace ? "{$namespace}_76x76.png" : $defaultFavicon,
            '120x120'   => $namespace ? "{$namespace}_120x120.png" : $defaultFavicon,
            '152x152'   => $namespace ? "{$namespace}_152x152.png" : $defaultFavicon,
            '180x180'   => $namespace ? "{$namespace}_180x180.png" : $defaultFavicon,
            '192x192'   => $namespace ? "{$namespace}_192x192.png" : $defaultFavicon,
        ];
    }

    /**
     * Construye las rutas de logos.
     */
    private function buildImageLogoPaths(array $settings): array
    {
        $defaultLogo = config('koneko.app_logo', 'logo-default.png');

        return [
            'small'       => $settings['image_logo_small'] ?? $defaultLogo,
            'medium'      => $settings['image_logo_medium'] ?? $defaultLogo,
            'large'       => $settings['image_logo'] ?? $defaultLogo,
            'small_dark'  => $settings['image_logo_small_dark'] ?? $defaultLogo,
            'medium_dark' => $settings['image_logo_medium_dark'] ?? $defaultLogo,
            'large_dark'  => $settings['image_logo_dark'] ?? $defaultLogo,
        ];
    }

    /**
     * Valores de fallback si no hay base de datos.
     */
    private function getDefaultWebsiteVars(?string $key = null): array
    {
        return $key
            ? ($this->buildWebsiteVarsArray([])[$key] ?? null)
            : $this->buildWebsiteVarsArray([]);
    }

    /**
     * Limpia las caches del website
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_WEBSITE_VARS_TAG);
    }
}
