<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\Http\Controllers;

use Illuminate\Routing\Controller;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function infoIndex()
    {
        return view('koneko-website-admin::contact.info.index');
    }

    public function formIndex()
    {
        return view('koneko-website-admin::contact.form.index');
    }
}
