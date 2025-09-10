<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Database\Seeders;

use Koneko\KonekoVuexyAdmin\Support\Seeders\Base\AbstractDataSeeder;
use Koneko\KonekoVuexyAdmin\Support\Traits\Seeders\HandlesFileSeeders;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

class WebsiteSiteSeeder extends AbstractDataSeeder
{
    use HandlesFileSeeders;

    // Datos del Modelo
    protected string $model          = WebsiteSite::class;
    protected string|array $uniqueBy = 'domain';
}
