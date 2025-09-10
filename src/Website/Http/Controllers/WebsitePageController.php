<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Website\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class WebsitePageController extends Controller
{
    /**
     * Renderiza la página pública principal.
     */
    public function __invoke(Request $request)
    {
        return view('koneko-website-admin::website.main.layout');
    }

    /**
     * Vista previa segura firmada.
     */
    public function preview(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Firma de vista previa no válida.');
        }

        return view('koneko-website-admin::website.main.layout');
    }
}
