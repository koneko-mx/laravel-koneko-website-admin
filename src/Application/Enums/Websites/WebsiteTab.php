<?php

namespace Koneko\KonekoWebsiteAdmin\Application\Enums\Websites;

use Illuminate\Contracts\Auth\Authenticatable;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

enum WebsiteTab: string {
    case General     = 'general';
    case Template    = 'template';
    case Brand       = 'brand';
    case Seo         = 'seo';
    case Contact     = 'contact';
    case Locations   = 'locations';
    case Social      = 'social';
    case Chat        = 'chat';
    case Integrations= 'integrations';
    case Pages       = 'pages';
    case Menus       = 'menus';
    case ContentBlocks = 'content-blocks';

    public function label(): string
    {
        return match($this) {
            self::General      => 'General',
            self::Template     => 'Plantilla',
            self::Brand        => 'Marca',
            self::Seo          => 'SEO',
            self::Contact      => 'Contacto',
            self::Locations    => 'Ubicaciones',
            self::Social       => 'Redes sociales',
            self::Chat         => 'Chat',
            self::Integrations => 'Integraciones',
            self::Pages        => 'Páginas',
            self::Menus        => 'Menús',
            self::ContentBlocks => 'Bloques de contenido',
        };
    }

    public function icon(): string
    {
        // nombres Tabler Icons (ti-*)
        return match($this) {
            self::General      => 'layout-dashboard',
            self::Template     => 'template',
            self::Brand        => 'transform-point-bottom-right',
            self::Seo          => 'chart-line',
            self::Contact      => 'address-book',
            self::Locations    => 'map',
            self::Social       => 'brand-facebook',
            self::Chat         => 'messages',
            self::Integrations => 'plug-connected',
            self::Pages        => 'file-description',
            self::Menus        => 'menu-deep',
            self::ContentBlocks => 'blocks',
        };
    }

    /** Orden lateral y filtro por permisos/feature-flags */
    public static function forSidebar(?Authenticatable $user, WebsiteSite $site): array
    {
        $ordered = [
            self::General, self::Template, self::Brand, self::Seo, self::Contact,
            self::Locations, self::Social, self::Chat, self::Integrations, self::Pages,
            self::Menus, self::ContentBlocks,
        ];

        // Aquí puedes aplicar permisos: policies, roles, o features del sitio
        return array_values(array_filter($ordered, function (self $tab) use ($user, $site) {
            // ejemplo: ocultar Integrations si el sitio no tiene esa feature
            //if ($tab === self::Integrations && !($site->features['integrations'] ?? false)) return false;
            // ejemplo: policy
            // if ($tab === self::Seo && $user?->cannot('manageSeo', $site)) return false;
            return true;
        }));
    }
}
