<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Database\Seeders;

use Koneko\KonekoVuexyAdmin\Support\Seeders\Base\AbstractDataSeeder;
use Koneko\KonekoVuexyAdmin\Support\Traits\Seeders\HandlesFileSeeders;
use Koneko\KonekoWebsiteAdmin\Models\{WebsiteMenuItem, WebsiteMenu, WebsiteSite};

class WebsiteMenuItemSeeder extends AbstractDataSeeder
{
    use HandlesFileSeeders;

    // Datos del Modelo
    protected string $model          = WebsiteMenuItem::class;
    protected string|array $uniqueBy = ['menu_id', 'title'];

    // Ruta del archivo de datos
    //protected string $targetFile = 'website_menus_web_info.json';

    protected function sanitizeRow(array $row): array
    {
        return array_merge($row, [
            'menu_id' => $this->findMenuId($row['site_domain'], $row['menu_slug']),
        ]);
    }

    protected function findSiteId($domain): ?int
    {
        return WebsiteSite::where('domain', $domain)->first()?->id;
    }

    protected function findMenuId($domain, $slug): ?int
    {
        return WebsiteMenu::where('slug', $slug)->where('site_id', $this->findSiteId($domain))->first()?->id;
    }
}
