<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Koneko\KonekoWebsiteAdmin\Website\Http\Controllers\WebsitePageController;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Typography\FontFactory;

// Contenido publico
Route::get('/{slug}', WebsitePageController::class)
    ->where('slug', '^(?!admin|login|register|logout|email|user|storage|api|livewire|_debugbar|sanctum|preview|placeholder)(.*)$')
    ->name('website.content');

// Vista previa con firma
Route::get('/preview/{slug}', [WebsitePageController::class, 'preview'])
    ->middleware(['signed']) // Protege con firma
    ->name('website.preview');

// Generador de imagenes Placeholder
Route::get('/placeholder/{size}', function (Request $req, string $size) {
    // 1) Parseo WxH
    [$w, $h] = array_pad(explode('x', Str::lower($size), 2), 2, null);
    $w = max((int) $w, 1);
    $h = max((int) $h, 1);

    // 2) Parámetros
    $bg   = $req->query('bg', '#e0e0e0');
    $fg   = $req->query('fg', '#888888');
    $text = $req->query('text', "{$w}x{$h}");
    $fontPath = dirname(__DIR__) . '/resources/fonts/OpenSans-Bold.ttf';

    // 3) Driver desde config/image.php
    $driverClass = config('image.driver') === ImagickDriver::class ? ImagickDriver::class : GdDriver::class;
    $manager = new ImageManager(new $driverClass());

    // 4) Generar imagen
    $img = $manager->create($w, $h)->fill($bg);
    $img->text($text, $w / 2, $h / 2, function (FontFactory $font) use ($fg, $fontPath, $w) {
        if (is_file($fontPath)) $font->file($fontPath);
        $font->size(max(12, min(48, (int) ($w / 12))));
        $font->color($fg);
        $font->align('center');
        $font->valign('middle');
    });

    // 5) Codificar (elige el formato)
    $format = Str::lower($req->query('format', 'png')); // png|jpg|webp
    $encoded = match ($format) {
        'jpg', 'jpeg' => $img->toJpeg(85),
        'webp'        => $img->toWebp(80),
        default       => $img->toPng(), // mediaType: image/png
    };

    // 6) Responder manualmente
    return response($encoded->toString(), 200, [
        'Content-Type'  => $encoded->mediaType(),   // p.ej. image/png
        'Cache-Control' => 'public, max-age=3600, s-maxage=3600',
    ]);
});


