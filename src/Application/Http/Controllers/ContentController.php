<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Koneko\KonekoWebsiteAdmin\Application\ConfigBuilders\Faq\FaqTableConfigBuilder;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function faqIndex(Request $request)
    {
        if ($request->ajax()) {
            return app(FaqTableConfigBuilder::class)
                ->getQueryBuilder($request)
                ->getJson();
        }
        return view('koneko-website-admin::content.faq.index');
    }

    public function galleryIndex()
    {
        return view('koneko-website-admin::content.gallery.index');
    }

    public function legalIndex()
    {
        return view('koneko-website-admin::content.legal.index');
    }
}
