<?php

namespace Koneko\KonekoWebsiteAdmin\Application\Bootstrap\Context;

use Illuminate\Http\Request;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

/**
 * 🧠 Resolve contexto de sitio activo en modo multisite
 */
class SiteContextCopy
{
    protected static ?WebsiteSite $resolved = null;

    /**
     * Devuelve el sitio actual (usando cache interna si ya fue resuelto)
     */
    public static function resolve(): ?WebsiteSite
    {
        return static::$resolved;
    }

    /**
     * Resuelve el sitio activo desde el request actual (dominio o path)
     */
    public static function resolveFromRequest(Request $request): ?WebsiteSite
    {
        // Evita doble resolución
        if (static::$resolved instanceof WebsiteSite) {
            return static::$resolved;
        }


        $host = ltrim($request->getHost(), 'www.');
        $site = WebsiteSite::where('domain', $host)->first();

        // Establece contexto (null si no hay match)
        static::$resolved = $site;

        return $site;
    }

    /**
     * Fuerza un sitio específico (desde sesión o entorno controlado)
     */
    public static function set(WebsiteSite $site): void
    {
        static::$resolved = $site;
    }

    /**
     * Limpia el contexto (en tests o entorno controlado)
     */
    public static function forget(): void
    {
        static::$resolved = null;
    }
}
