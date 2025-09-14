<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Website\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Config, URL, View};
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Koneko\KonekoVuexyAdmin\Application\Cache\Manager\KonekoCacheManager;
use Koneko\KonekoWebsiteAdmin\Website\Layout\Builders\WebsiteResponseBuilder;
use Koneko\KonekoWebsiteAdmin\Website\Resolvers\SiteContextResolver;
use Koneko\KonekoWebsiteAdmin\Website\Settings\WebsiteSettingsLoader;

final class WebsiteRuntimeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Render estático/API no necesita todo el runtime del website
        if (!str_contains((string) $request->header('Accept'), 'text/html')) {
            return $next($request);
        }

        $resolved = app(SiteContextResolver::class)->resolve($request);
        if (!$resolved || !$resolved['site']) {
            throw new HttpException(404, 'Sitio no encontrado.');
        }

        [$site, $content, $isPreview] = [$resolved['site'], $resolved['content'], $resolved['isPreview']];

        if (!$content) {
            throw new HttpException(404, 'Página no encontrado.');
        }


        $host = trim((string) $site->domain);                 // p.ej. "agroform.test"
        $host = rtrim($host, '/');

        // Si tu modelo tiene un flag de HTTPS, úsalo; si no, toma el de la request
        $scheme = $request->isSecure() ? 'https' : 'http';

        // Si ya trae esquema, respétalo; si no, prepéndelo
        $siteUrl = Str::startsWith($host, ['http://','https://'])
            ? $host
            : "{$scheme}://{$host}";

        // --- ahora sí tus sets ---
        Config::set('app.url', $siteUrl);
        URL::forceRootUrl($siteUrl);
        URL::forceScheme(parse_url($siteUrl, PHP_URL_SCHEME) ?: 'https');

        Config::set('app.asset_url', $siteUrl);
        Config::set('filesystems.disks.public.url', $siteUrl.'/storage');


        // Validaciones de acceso SOLO si no es preview
        if (!$isPreview && $content) {
            // estado
            if ($content->status->value === 'draft' && !Auth::check()) {
                throw new HttpException(403, 'Contenido no publicado.');
            }

            // ventanas
            $now = now();
            if (
                ($content->visible_from && $content->visible_from > $now) ||
                ($content->visible_until && $content->visible_until < $now)
            ) {
                throw new HttpException(403, 'Contenido no disponible.');
            }

            // roles/permissions
            $user = Auth::user();
            $roles = (array) ($content->roles ?? []);
            $perms = (array) ($content->permissions ?? []);
            if ($roles && !$user?->hasAnyRole($roles)) {
                throw new HttpException(403, 'Acceso restringido.');
            }
            if ($perms && !$user?->hasAnyPermission($perms)) {
                throw new HttpException(403, 'Permiso insuficiente.');
            }
        }

        // audience + slug (para home usa __home__)
        $aud   = $isPreview ? 'preview' : 'pub';
        $slug  = $resolved['slug'] !== '' ? $resolved['slug'] : '__home__';

        // cache key contextual multi-tenant
        $cache = KonekoCacheManager::make([
            'namespace'   => config('koneko.namespace','koneko'),
            'environment' => app()->environment(),
            'component'   => 'website',
            'group'       => 'site',
            'section'     => 'payload',
            //'sub_group'   => $aud,
            'key_name'    => 'page',
            'scope'       => $content,
        ]);

        // TTL: respeta configuración de contenido; en preview no cacheamos
        if ($isPreview) {
            $payload = $this->buildPayload($site, $content);
        } else {
            $ttl = ($content && $content->enable_cache)
                ? (int) ($content->cache_ttl ?? 0)
                : 0;

            // ttl<=0 => usa TTL global resuelto por el manager
            if ($content && $content->enable_cache && $ttl > 0) {
                $cache->ttl($ttl);
            }

            //$payload = $cache->remember(fn() => $this->buildPayload($site, $content));
            $payload = $this->buildPayload($site, $content);
        }

        View::share($payload);

        return $next($request);
    }

    private function buildPayload($site, $content): array
    {
        $settings = WebsiteSettingsLoader::forSite($site)->load(true); // bloques sociales, chat, contacto, integraciones...

        return WebsiteResponseBuilder::make($site, $content, $settings)->build();
    }
}
