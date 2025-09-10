<?php

namespace Koneko\KonekoWebsiteAdmin\Website\Cache;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WebsiteRenderCacheService
{
    public const TAG_MAIN = 'rendered_html';

    /**
     * Genera la clave base del cache para un tipo y slug
     */
    public static function cacheKeyFor(string $type, string $slug): string
    {
        return "rendered_page:{$type}:{$slug}";
    }

    /**
     * Obtiene una vista renderizada desde cache, o la genera y almacena.
     */
    public static function getOrRender(string $type, string $slug, Closure $callback, ?int $ttl = null): string
    {
        $ttl = $ttl ?? Config::get('koneko.website.cache.html.ttl.default', 900);
        $key = self::cacheKeyFor($type, $slug);

        return Cache::tags([
            self::TAG_MAIN,
            $type,
            "{$type}_{$slug}"
        ])->remember($key, now()->addMinutes($ttl), $callback);
    }

    /**
     * Invalida un contenido renderizado
     */
    public static function invalidate(string $type, string $slug): void
    {
        Cache::tags([
            self::TAG_MAIN,
            $type,
            "{$type}_{$slug}"
        ])->flush();
    }

    /**
     * Devuelve una respuesta HTTP renderizada con headers de depuración.
     */
    public static function responseWithHeaders(string $html, string $type, string $slug): Response
    {
        $debug = Config::get('koneko.website.cache.html.debug_mode', false);

        return response($html)->withHeaders([
            'X-Koneko-Cache' => 'HIT',
            'X-Koneko-Type'  => $type,
            'X-Koneko-Slug'  => $slug,
            'X-Koneko-TTL'   => Config::get('koneko.website.cache.html.ttl.default', 900),
            'X-Koneko-Debug' => $debug ? 'true' : 'false',
        ]);
    }

    /**
     * Limpia toda la cache HTML
     */
    public static function flushAll(): void
    {
        Cache::tags([self::TAG_MAIN])->flush();
        Log::info('[WebsiteRenderCacheService] Toda la cache HTML ha sido invalidada.');
    }
}
