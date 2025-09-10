<?php

namespace Koneko\KonekoWebsiteAdmin\Database\Seeders;

use Koneko\KonekoVuexyAdmin\Support\Seeders\Base\AbstractDataSeeder;
use Koneko\KonekoVuexyAdmin\Support\Traits\Seeders\HandlesFileSeeders;
use Koneko\KonekoWebsiteAdmin\Models\{WebsiteSeoProfile, WebsiteSite};
use Koneko\KonekoWebsiteAdmin\Application\Enums\WebsiteSeoProfile\WebsiteSeoProfileScope;

class WebsiteSeoProfileSeeder extends AbstractDataSeeder
{
    use HandlesFileSeeders;

    // Datos del Modelo
    protected string $model          = WebsiteSeoProfile::class;
    protected string|array $uniqueBy = 'id';

    protected function sanitizeRow(array $row): array
    {
        $website = WebsiteSite::where('domain', $row['site_domain'])->first();

        if ($website) {
            $row['seoable_type'] = WebsiteSite::class;
            $row['seoable_id']   = $website->id;
            $row['scope']        = WebsiteSeoProfileScope::Site->value;
        }

        return $row;
    }
}
