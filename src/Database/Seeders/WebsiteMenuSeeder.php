<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Database\Seeders;

use Koneko\KonekoVuexyAdmin\Support\Seeders\Base\AbstractDataSeeder;
use Koneko\KonekoVuexyAdmin\Support\Traits\Seeders\HandlesFileSeeders;
use Koneko\KonekoWebsiteAdmin\Models\{WebsiteMenu, WebsiteSite};

class WebsiteMenuSeeder extends AbstractDataSeeder
{
    use HandlesFileSeeders;

    // Datos del Modelo
    protected string $model          = WebsiteMenu::class;
    protected string|array $uniqueBy = ['site_id', 'slug'];

    // Ruta del archivo de datos
    //protected string $targetFile = 'website_menus.json';

    protected function sanitizeRow(array $row): array
    {
        return array_merge($row, [
            'site_id' => $this->findSiteId($row['site_domain']),
        ]);
    }

    protected function findSiteId($domain): ?int
    {
        return WebsiteSite::where('domain', $domain)->first()?->id;
    }

}
