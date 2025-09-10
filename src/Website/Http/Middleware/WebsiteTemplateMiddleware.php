<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;
use Koneko\KonekoWebsiteAdmin\Website\UX\Content\WebsiteBreadcrumbsBuilderService;
use Koneko\KonekoWebsiteAdmin\Website\UX\Menu\WebsiteMenuBuilderService;
use Koneko\KonekoWebsiteAdmin\Website\UX\Template\WebsiteVarsBuilderService;

class WebsiteTemplateMiddleware
{
    public function handle($request, Closure $next)
    {
        // Aplicar configuración de layout antes de que la vista se cargue
        if (str_contains($request->header('Accept'), 'text/html')) {
            View::share([
                '_web'       => []//app(WebsiteVarsBuilderService::class)->getWebsiteVars(),
                //'_menu'        => app(WebsiteMenuBuilderService::class)->getForUser(),
                //'_breadcrumbs' => app(WebsiteBreadcrumbsBuilderService::class)->getBreadcrumbs(),
            ]);
        }

        return $next($request);
    }
}
