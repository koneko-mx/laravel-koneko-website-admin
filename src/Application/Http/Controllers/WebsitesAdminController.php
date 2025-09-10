<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\Http\Controllers;

use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Koneko\KonekoVuexyAdmin\Application\UX\Breadcrumbs\Breadcrumbs;
use Koneko\KonekoWebsiteAdmin\Application\Enums\Websites\WebsiteTab;
use Koneko\KonekoWebsiteAdmin\Application\UX\ConfigBuilders\Pages\PagesTableConfigBuilder;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

class WebsitesAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): ViewContract
    {
        $sites = WebsiteSite::all();

        return view('koneko-website-admin::sites.index', compact('sites'));
    }

    public function siteManager(Request $request, WebsiteSite $site, $tab = 'general'): JsonResponse|ViewContract
    {
        if ($request->ajax()) {
            if($tab == 'pages'){
                $builder = app(PagesTableConfigBuilder::class)->getQueryBuilder($request);

                return $builder->getJson();
            }
        }

        $tabEnum = WebsiteTab::tryFrom($tab) ?? WebsiteTab::General;

        Breadcrumbs::extend([
            [
                'name'   => $site->domain,
                'link'   => route('admin.website-admin.websites.manager.site', [$site, 'general']),
                'active' => $tabEnum === WebsiteTab::General,
            ],
            [
                'name'   => $tabEnum->label(),
            ],
        ]);

        return view('koneko-website-admin::sites.site.index', [
            'site' => $site,
            'tab'  => $tabEnum->value,
        ]);
    }

    public function pageCreate(WebsiteSite $site): ViewContract
    {
        Breadcrumbs::extend([
            [
                'name'   => $site->domain,
                'link'   => route('admin.website-admin.websites.manager.site', [$site, 'general']),
            ],
            [
                'name'   => 'Páginas',
                'link'   => route('admin.website-admin.websites.manager.site', [$site, 'pages']),
            ],
            [
                'name'   => 'Crear',
            ],
        ]);

        return view('koneko-website-admin::sites.pages.create', compact('site'));
    }

    public function pageEdit(WebsiteSite $site): ViewContract
    {


        return view('koneko-website-admin::sites.pages.create', compact('site'));
    }

}
