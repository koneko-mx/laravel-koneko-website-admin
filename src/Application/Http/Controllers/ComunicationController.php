<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\Http\Controllers;

use Illuminate\Routing\Controller;

class ComunicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function messengerIndex()
    {
        return view('koneko-website-admin::comunication.messenger.index');
    }

    public function whatsappIndex()
    {
        return view('koneko-website-admin::comunication.whatsapp.index');
    }

    public function tawkToIndex()
    {
        return view('koneko-website-admin::comunication.tawk-to.index');
    }

    public function twitterIndex()
    {
        return view('koneko-website-admin::comunication.twitter.index');
    }

}
