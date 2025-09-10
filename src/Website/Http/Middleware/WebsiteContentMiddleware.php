<?php

namespace Koneko\KonekoWebsiteAdmin\Website\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, View};
use Koneko\KonekoWebsiteAdmin\Application\Bootstrap\Context\SiteContext;
use Koneko\KonekoWebsiteAdmin\Application\Website\Services\WebsiteResponseBuilder;
use Koneko\KonekoWebsiteAdmin\Models\{WebsiteContent, WebsiteSite};
use Symfony\Component\HttpKernel\Exception\HttpException;

class WebsiteContentMiddleware
{
    public function __construct(
        private readonly SiteContextResolver $resolver,
        private readonly WebsiteCache $cache,
        private readonly WebsiteResponseBuilder $builder,
    ) {}

    public function handle(Request $request, Closure $next)
    {
        if (!str_contains($request->header('Accept', ''), 'text/html')) {
            return $next($request);
        }

        $ctx = $this->resolver->fromRequest($request);

        // Guardias de visibilidad
        if (!$ctx->isPreview) {
            if ($ctx->content->status === 'draft' && !Auth::check()) {
                throw new HttpException(403, 'Contenido no publicado.');
            }
            $now = CarbonImmutable::now();
            if (($ctx->content->visible_from && $now->lt($ctx->content->visible_from))
             || ($ctx->content->visible_until && $now->gt($ctx->content->visible_until))) {
                throw new HttpException(403, 'Contenido no disponible.');
            }
            // Roles/permissions (si aplican), aquí sólo validas acceso; la política de cache se maneja aparte
            // ...
        }

        $aud = AudienceKey::from($request, $ctx);
        $tags = CachePolicy::tagsFor($ctx);
        $key  = CacheKeys::content($ctx->env, $ctx->site->id, $ctx->content->slug, $aud);

        if ($ctx->isPreview || !CachePolicy::shouldCache($ctx)) {
            $vm = $this->builder->build($ctx);
        } else {
            $ttl = $ctx->content->cache_ttl ?? now()->addMinutes(30);

            // Anti-stampede con lock
            $vm = $this->cache->get($key, $tags);
            if (!$vm) {
                $lock = Cache::lock('lock:'.$key, 10);
                try {
                    $lock->block(5);
                    $vm = $this->cache->remember($key, $ttl, function () use ($ctx) {
                        return $this->builder->build($ctx)->toArray();
                    }, $tags);
                } finally {
                    optional($lock)->release();
                }
            }
        }

        // Share a vistas
        $arr = ($vm instanceof \Koneko\KonekoWebsiteAdmin\Application\Website\DTO\ViewModel) ? $vm->toArray() : $vm;
        View::share([
            '_layout'  => $arr['layout'],
            '_seo'     => $arr['seo'],
            '_social'  => $arr['social'],
            '_contact' => $arr['contact'],
            '_headerBlocks'  => $arr['blocks']['header'],
            '_contentBlocks' => $arr['blocks']['content'],
            '_footerBlocks'  => $arr['blocks']['footer'],
        ]);

        return $next($request);
    }
}
