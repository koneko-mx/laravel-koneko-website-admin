<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\Template;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Koneko\KonekoVuexyAdmin\Application\Settings\Manager\KonekoSettingManager;
use Koneko\KonekoWebsiteAdmin\Models\{WebsiteSite, WebsiteSeoProfile};
use Illuminate\Http\UploadedFile;

/**
 * Servicio para gestionar favicon y logos administrativos.
 * - Versiona logos en 4 variantes (normal/horizontal × light/dark) sin perder proporción.
 * - Genera tamaños small/medium/large + base64 por variante.
 */
class WebsiteImageHandler
{
    private string $driver;
    private string $imageDisk = 'public';
    private array $paths = [];

    // Tamaños por área de píxeles, conservando aspecto (no recorta)
    private const PIXELS_SMALL  = 22500;   // ~150×150
    private const PIXELS_MEDIUM = 75625;   // ~275×275
    private const PIXELS_LARGE  = 262144;  // ~512×512
    private const PIXELS_BASE64 = 230400;  // ~480×480 (calidad para inline)

    // Variantes admitidas para logos
    // "default", "h_default" (horizontal), "dark", "h_dark" (horizontal + dark)
    public const LOGO_VARIANTS = ['default', 'h_default', 'dark', 'h_dark'];

    private const GROUP = 'layout';

    /**
     * Tamaños estándar de favicons.
     */
    public static function getFaviconSizes(): array
    {
        return config('koneko.media.favicon_sizes');
    }

    public function __construct()
    {
        $this->driver    = config('image.driver', 'gd');
        $this->imageDisk = config('koneko.media.default_disk', 'public');
        $this->paths = [
            'favicon' => config('koneko.media.paths.favicon', 'favicon-website/'),
            'logo'    => config('koneko.media.paths.logo',    'logo-website/'),
            'share'   => config('koneko.media.paths.share',   'share-website/'),
        ];
    }

    private function settings(WebsiteSite $site, $section): KonekoSettingManager
    {
        return settings('website-admin')
            ->context(self::GROUP, $section)
            ->scope($site);
    }

    /**
     * Procesa y guarda múltiples versiones del favicon (recorta a tamaño exacto: cover WxH).
     */
    public function processAndSaveFavicon(UploadedFile $image, WebsiteSeoProfile $profile): void
    {
        $imageManager = new ImageManager($this->driver);
        $original     = $imageManager->read($image->getRealPath());

        $new = [];
        foreach (self::getFaviconSizes() as $size => [$w,$h]) {
            $resized = (clone $original)->cover($w,$h);
            $file    = uniqid('favicon_', true)."_{$size}.png";
            Storage::disk($this->imageDisk)->put(
                $this->paths['favicon'].$file,
                $resized->toPng(true)
            );
            $new[$size] = $file;
        }

        $p            = WebsiteSeoProfile::query()->findOrFail($profile->id);
        $oldFavicons  = (array) ($p->favicon ?? []);

        $p->update(['favicon' => $new]);

        $this->deleteOldFiles(array_map(
            fn($v) => $this->paths['favicon'].$v, $oldFavicons
        ));
    }


    /**
     * Procesa y guarda un logo en UNA variante (e.g. '', 'h', 'dark', 'h_dark').
     * Mantiene proporción real: redimensiona por área de píxeles, sin recortes.
     */
    public function processAndSaveImageLogo(UploadedFile $image, WebsiteSite $site, string $variant = 'default'): void
    {
        $imageManager = new ImageManager($this->driver);
        $original     = $imageManager->read($image->getRealPath());

        try {
            $this->saveLogoForVariant($original, $site, $variant);
        } finally {
            if (is_object($image) && method_exists($image, 'delete')) {
                $image->delete();
            };
        }
    }

    /**
     * Procesa y guarda un logo en VARIAS variantes a la vez.
     * @param array $variants e.g. WebsiteImageHandler::LOGO_VARIANTS
     */
    public function processAndSaveImageLogoVariants(UploadedFile $image, WebsiteSite $site, array $variants = self::LOGO_VARIANTS): void
    {
        $imageManager = new ImageManager($this->driver);
        $original     = $imageManager->read($image->getRealPath());

        try {
            foreach ($variants as $variant) {
                $this->saveLogoForVariant($original, $site, (string)$variant);
            }
        } finally {
            if (is_object($image) && method_exists($image, 'delete')) {
                $image->delete();
            };
        }
    }

    /**
     * Obtiene las variables (paths/base64) del logo para una variante.
     * $variant: '', 'h', 'dark', 'h_dark'
     */
    public function getImageLogoVars(WebsiteSite $site, string $sub_group): array
    {
        $settings = $this->settings($site, 'brand')->subGroup($sub_group)->asArray()->all() ?? [];

        $urlOrFallback = function (string $key) use ($settings) {
            if (!empty($settings[$key])) {
                return Storage::disk($this->imageDisk)
                    ->url($this->paths['logo'] . $settings[$key]);
            }
            // fallback a un asset del vendor/tema, ya absoluto
            return vendor_or_url(config('koneko.branding.app_logo'));
        };

        return [
            'small'  => $urlOrFallback('small'),
            'medium' => $urlOrFallback('medium'),
            'large'  => $urlOrFallback('large'),
            'base64' => $settings['base64'] ?? '',
        ];
    }

    /**
     * Shortcut para obtener TODAS las variantes en un arreglo estructurado.
     */
    public function getAllLogoVars(WebsiteSite $site): array
    {
        $out = [];
        foreach (self::LOGO_VARIANTS as $variant) {
            $out[$variant] = $this->getImageLogoVars($site, $variant);
        }
        return $out;
    }

    /**
     * Guarda (borra previas y re-genera) small/medium/large/base64 de una variante.
     */
    private function saveLogoForVariant($originalImage, WebsiteSite $site, string $variant='default'): void
    {
        $previous = $this->settings($site,'brand')->subGroup($variant)->asArray()->all() ?? [];

        $this->saveResizedLogo($originalImage, self::PIXELS_SMALL,  $site, 'small',  $variant);
        $this->saveResizedLogo($originalImage, self::PIXELS_MEDIUM, $site, 'medium', $variant);
        $this->saveResizedLogo($originalImage, self::PIXELS_LARGE,  $site, 'large',  $variant);
        $this->saveBase64Logo ($originalImage, self::PIXELS_BASE64, $site,           $variant);

        // borra lo viejo al final
        $paths = [];
        foreach ($previous as $k => $v) {
            if (is_string($v) && $k !== 'base64' && !str_starts_with($v,'data:')) {
                $paths[] = $this->paths['logo'].$v;
            }
        }
        $this->deleteOldFiles($paths);
    }


    /**
     * Redimensiona y guarda un logo (sin recorte, conservando aspecto).
     */
    private function saveResizedLogo($image, int $maxPixels, WebsiteSite $site, string $size = '', string $variant = 'default'): void
    {
        $resized = clone $image;
        $this->resizeImageToMaxPixels($resized, $maxPixels);

        $fileName = uniqid("logo_{$size}_{$variant}_", true) . '.png';
        $path     = $this->paths['logo'] . $fileName;

        Storage::disk($this->imageDisk)->put($path, $resized->toPng(indexed: true));

        $keyName = $size;

        $this->settings($site, 'brand')->subGroup($variant)->set($keyName, $fileName);
    }

    /**
     * Guarda un logo en base64 (JPG calidad 40 por tamaño/uso inline). Mantiene proporción.
     */
    private function saveBase64Logo($image, int $maxPixels, WebsiteSite $site, string $variant = 'default'): void
    {
        $resized = clone $image;
        $this->resizeImageToMaxPixels($resized, $maxPixels);

        $base64 = (string) $resized->toJpg(40)->toDataUri();

        $this->settings($site, 'brand')->subGroup($variant)->set('base64', $base64);
    }

    /**
     * Elimina archivos de imágenes antiguos (solo archivos; ignora valores base64).
     */
    private function deleteOldFiles(array $paths): void
    {
        foreach ($paths as $path) {
            if ($path && !str_starts_with($path, 'data:') && Storage::disk($this->imageDisk)->exists($path)) {
                Storage::disk($this->imageDisk)->delete($path);
            }
        }
    }

    /**
     * Redimensiona imagen conservando aspecto. Calcula W×H aproximados a partir del área.
     */
    private function resizeImageToMaxPixels($image, int $maxPixels)
    {
        $originalWidth  = $image->width();
        $originalHeight = $image->height();
        $aspectRatio    = $originalWidth / max(1, $originalHeight);

        if ($aspectRatio > 1) {
            $newWidth  = sqrt($maxPixels * $aspectRatio);
            $newHeight = $newWidth / $aspectRatio;
        } else {
            $newHeight = sqrt($maxPixels / max(0.0001, $aspectRatio));
            $newWidth  = $newHeight * $aspectRatio;
        }

        $image->resize((int) round($newWidth), (int) round($newHeight), function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        return $image;
    }



    /**
     * Guarda imagen para OG/Twitter con tamaño fijo y recorte COVER.
     * $kind: 'og' => 1200x630, 'twitter' => 800x418 (summary_large_image)
     * Devuelve ruta relativa: e.g. "share-website/og_...png"
     */
    public function processAndSaveShareImage(UploadedFile $image, string $kind = 'og'): string
    {
        $dims = match ($kind) {
            'twitter' => [800, 418],
            default   => [1200, 630],
        };

        $imageManager = new ImageManager($this->driver);
        $original     = $imageManager->read($image->getRealPath());

        Storage::disk($this->imageDisk)->makeDirectory($this->paths['share']);

        $file   = uniqid($kind . '_', true) . '.png';
        $path   = $this->paths['share'] . $file;

        // cover: recorte centrado al canvas objetivo
        $processed = clone $original;
        $processed = $processed->cover($dims[0], $dims[1]); // fill y recorta
        Storage::disk($this->imageDisk)->put($path, $processed->toPng(indexed: true));

        // limpia temp
        $image->delete();

        return $path; // relativo a storage/
    }
}
