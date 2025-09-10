<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\Http\Controllers;

use Illuminate\Routing\Controller;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function generalIndex()
    {
        return view('koneko-website-admin::settings.general.index');
    }

    public function socialIndex()
    {
        return view('koneko-website-admin::settings.social.index');
    }

    public function indexingIndex()
    {
        return view('koneko-website-admin::settings.indexing.index');
    }
}
