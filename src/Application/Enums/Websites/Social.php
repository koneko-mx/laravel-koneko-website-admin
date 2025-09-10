<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\Enums\Websites;

enum Social: string
{
    case WHATSAPP  = 'whatsapp';
    case FACEBOOK  = 'facebook';
    case INSTAGRAM = 'instagram';
    case LINKEDIN  = 'linkedin';
    case X_TWITTER = 'x_twitter';
    case TIKTOK    = 'tiktok';
    case GOOGLE    = 'google';
    case PINTEREST = 'pinterest';
    case YOUTUBE   = 'youtube';
    case VIMEO     = 'vimeo';

    /** Etiqueta legible para UI */
    public function label(): string
    {
        return match ($this) {
            self::WHATSAPP  => 'WhatsApp',
            self::FACEBOOK  => 'Facebook',
            self::INSTAGRAM => 'Instagram',
            self::LINKEDIN  => 'LinkedIn',
            self::X_TWITTER => 'X (Twitter)',
            self::TIKTOK    => 'TikTok',
            self::GOOGLE    => 'Google',
            self::PINTEREST => 'Pinterest',
            self::YOUTUBE   => 'YouTube',
            self::VIMEO     => 'Vimeo',
        };
    }

    /** Clase de ícono (Tabler Icons) */
    public function icon(): string
    {
        return match ($this) {
            self::WHATSAPP  => 'ti ti-brand-whatsapp',
            self::FACEBOOK  => 'ti ti-brand-facebook',
            self::INSTAGRAM => 'ti ti-brand-instagram',
            self::LINKEDIN  => 'ti ti-brand-linkedin',
            self::X_TWITTER => 'ti ti-brand-twitter',
            self::TIKTOK    => 'ti ti-brand-tiktok',
            self::GOOGLE    => 'ti ti-brand-google',
            self::PINTEREST => 'ti ti-brand-pinterest',
            self::YOUTUBE   => 'ti ti-brand-youtube',
            self::VIMEO     => 'ti ti-brand-vimeo',
        };
    }

    /** Fontawesome */
    public function iconFA(): string
    {
        return match ($this) {
            self::WHATSAPP  => 'fab fa-whatsapp',
            self::FACEBOOK  => 'fab fa-facebook-f',
            self::INSTAGRAM => 'fab fa-instagram',
            self::LINKEDIN  => 'fab fa-linkedin-in',
            self::X_TWITTER => 'fab fa-x-twitter',
            self::TIKTOK    => 'fab fa-tiktok',
            self::GOOGLE    => 'fab fa-google',
            self::PINTEREST => 'fab fa-pinterest-p',
            self::YOUTUBE   => 'fab fa-youtube',
            self::VIMEO     => 'fab fa-vimeo-v',
        };
    }

    /** Placeholder recomendado para el input */
    public function placeholder(): string
    {
        return match ($this) {
            self::WHATSAPP  => '+525512345678',
            self::FACEBOOK  => 'https://facebook.com/tu-pagina o @tu-pagina',
            self::INSTAGRAM => 'https://instagram.com/tu-usuario o @tu-usuario',
            self::LINKEDIN  => 'https://linkedin.com/in/tu-usuario o @tu-usuario',
            self::X_TWITTER => 'https://x.com/tu-usuario o @tu-usuario',
            self::TIKTOK    => 'https://tiktok.com/@tu-usuario o @tu-usuario',
            self::GOOGLE    => 'https://maps.app.goo.gl/... o perfil',
            self::PINTEREST => 'https://pinterest.com/tu-usuario o @tu-usuario',
            self::YOUTUBE   => 'https://youtube.com/@tu-usuario',
            self::VIMEO     => 'https://vimeo.com/tu-usuario',
        };
    }

    /**
     * Normaliza un valor ingresado (URL o @handle) a URL final.
     *
     * @param string      $value   Valor crudo del input (teléfono, URL o @handle)
     * @param string|null $site    Macro {site}
     * @param string|null $title   Macro {title}
     * @param string|null $url     Macro {url}
     * @param string|null $message Solo para WhatsApp: mensaje base (con macros)
     */
    public function normalize(string $value, ?string $site = null, ?string $title = null, ?string $url = null, ?string $message = null): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        // Si ya es URL absoluta, devolver tal cual (excepto WhatsApp, que aceptará E.164 + mensaje)
        $isAbsolute = (bool)preg_match('#^https?://#i', $value);

        $toHandle = static function (string $v): string {
            $v = trim($v);
            if (str_starts_with($v, '@')) {
                $v = substr($v, 1);
            }
            // quitar posibles espacios/trailing slashes
            return rtrim($v, "/ \t\n\r\0\x0B");
        };

        return match ($this) {
            self::WHATSAPP => $this->normalizeWhatsapp($value, $message ?? '', $site, $title, $url),

            self::FACEBOOK => $isAbsolute ? $value : 'https://facebook.com/' . $toHandle($value),

            self::INSTAGRAM => $isAbsolute ? $value : 'https://instagram.com/' . $toHandle($value),

            self::LINKEDIN => $isAbsolute ? $value : 'https://www.linkedin.com/in/' . $toHandle($value),

            self::X_TWITTER => $isAbsolute ? $value : 'https://x.com/' . $toHandle($value),

            self::TIKTOK => $isAbsolute ? $value : 'https://www.tiktok.com/@' . $toHandle($value),

            self::GOOGLE => $value, // normalmente es URL (Maps/Perfil/Bussiness). No forzar handle.

            self::PINTEREST => $isAbsolute ? $value : 'https://www.pinterest.com/' . $toHandle($value),

            self::YOUTUBE => $isAbsolute ? $value : 'https://www.youtube.com/@' . $toHandle($value),

            self::VIMEO => $isAbsolute ? $value : 'https://vimeo.com/' . $toHandle($value),
        };
    }

    /** Construye wa.me con teléfono E.164 y mensaje con macros codificadas */
    private function normalizeWhatsapp(string $rawPhone, string $message, ?string $site, ?string $title, ?string $url): ?string
    {
        $phone = preg_replace('/\s+/', '', $rawPhone); // quitar espacios
        // Aceptar +, dígitos; quitar guiones y paréntesis
        $phone = str_replace(['-', '(', ')'], '', $phone);

        if (!preg_match('/^\+?[1-9]\d{6,14}$/', $phone)) {
            // no parece E.164 razonable
            return null;
        }

        $macros = [
            '{site}'  => (string)($site ?? ''),
            '{title}' => (string)($title ?? ''),
            '{url}'   => (string)($url ?? ''),
        ];
        $finalMessage = strtr($message, $macros);
        $encoded      = rawurlencode($finalMessage);

        // Quitar '+' para wa.me
        $digits = ltrim($phone, '+');

        return $encoded !== ''
            ? "https://wa.me/{$digits}?text={$encoded}"
            : "https://wa.me/{$digits}";
    }

    /** Útil para iterar en vistas o validaciones */
    public static function casesMap(): array
    {
        $out = [];
        foreach (self::cases() as $case) {
            $out[$case->value] = [
                'label'       => $case->label(),
                'icon'        => $case->icon(),
                'placeholder' => $case->placeholder(),
            ];
        }
        return $out;
    }
}
