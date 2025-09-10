<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\Http\Controllers;

use Illuminate\Routing\Controller;

class AnalyticsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function googleAnalyticsIndex()
    {
        return view('koneko-website-admin::analytics.google-analytics.index');
    }

    public function googleTagsIndex()
    {
        return view('koneko-website-admin::analytics.google-tags.index');
    }

    public function googleSearchConsoleIndex()
    {
        return view('koneko-website-admin::analytics.google-search-console.index');
    }

    public function pixelMetaIndex()
    {
        return view('koneko-website-admin::analytics.pixel-meta.index');
    }
}
