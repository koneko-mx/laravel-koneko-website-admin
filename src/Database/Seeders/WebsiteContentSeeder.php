<?php

namespace Koneko\KonekoWebsiteAdmin\Database\Seeders;

use Koneko\KonekoVuexyAdmin\Support\Seeders\Base\AbstractDataSeeder;
use Koneko\KonekoVuexyAdmin\Support\Traits\Seeders\HandlesFileSeeders;
use Koneko\KonekoWebsiteAdmin\Models\{WebsiteContent, WebsiteSeoProfile, WebsiteSite};

class WebsiteContentSeeder extends AbstractDataSeeder
{
    use HandlesFileSeeders;

    // Datos del Modelo
    protected string $model          = WebsiteContent::class;
    protected string|array $uniqueBy = ['site_id', 'slug'];

    // Ruta del archivo de datos
    //protected string $targetFile     = 'website_contents.json';

    protected function sanitizeRow(array $row): array
    {
        $row = array_merge($row, [
            'site_id' => $this->findSiteId($row['site_domain']),
        ]);

        return $row;
    }

    protected function afterRowProcessed($model, array $row): void
    {
        // Crear un WebsiteSeoProfile y asociarlo al WebsiteContent a través de la relación polimórfica
        if ($model instanceof WebsiteContent) {
            // Verificar si ya tiene un perfil SEO asociado
            if (!$model->seoProfile()->exists()) {
                $seo = $row['seo_overrides'] ?? [];

                // Crear un nuevo perfil SEO
                $seoProfile = new WebsiteSeoProfile([
                    'seoable_type' => $model->class,
                    'seoable_id' => $model->id,
                    'scope' => 'content',
                    'author_mode' => isset($seo['author_mode'])? $seo['author_mode']: 'site',
                    'author' => isset($seo['author'])? $seo['author']: null,
                    'copyright_mode' => isset($seo['copyright_mode'])? $seo['copyright_mode']: 'site',
                    'copyright' => isset($seo['copyright'])? $seo['copyright']: null,
                    'schema_mode' => isset($seo['schema_mode'])? $seo['schema_mode']: 'site',
                    'schema_org' => isset($seo['schema_org'])? $seo['schema_org']: null,
                    'favicon_mode' => isset($seo['favicon_mode'])? $seo['favicon_mode']: 'site',
                    'favicon' => isset($seo['favicon'])? $seo['favicon']: null,
                    'locale_mode' => isset($seo['locale_mode'])? $seo['locale_mode']: 'site',
                    'locale' => isset($seo['locale'])? $seo['locale']: null,
                    'template_mode' => isset($seo['template_mode'])? $seo['template_mode']: 'site',
                    'package' => isset($seo['package'])? $seo['package']: null,
                    'layout' => isset($seo['layout'])? $seo['layout']: null,
                    'theme_color' => isset($seo['theme_color'])? $seo['theme_color']: null,
                    'og_mode' => isset($seo['og_mode'])? $seo['og_mode']: 'site',
                    'og_type' => isset($seo['og_type'])? $seo['og_type']: null,
                    'og_title' => isset($seo['og_title'])? $seo['og_title']: null,
                    'og_description' => isset($seo['og_description'])? $seo['og_description']: null,
                    'og_image' => isset($seo['og_image'])? $seo['og_image']: null,
                    'og_url' => isset($seo['og_url'])? $seo['og_url']: null,
                    'og_site_name' => isset($seo['og_site_name'])? $seo['og_site_name']: null,
                    'twitter_mode' => isset($seo['twitter_mode'])? $seo['twitter_mode']: 'site',
                    'twitter_card' => isset($seo['twitter_card'])? $seo['twitter_card']: null,
                    'twitter_title' => isset($seo['twitter_title'])? $seo['twitter_title']: null,
                    'twitter_description' => isset($seo['twitter_description'])? $seo['twitter_description']: null,
                    'twitter_image' => isset($seo['twitter_image'])? $seo['twitter_image']: null,
                    'twitter_site' => isset($seo['twitter_site'])? $seo['twitter_site']: null,
                    'twitter_creator' => isset($seo['twitter_creator'])? $seo['twitter_creator']: null,
                ]);

                // Asociar el perfil SEO con el WebsiteContent mediante la relación polimórfica
                $model->seoProfile()->save($seoProfile);
            }
        }
    }

    protected function findSiteId($domain): ?int
    {
        return WebsiteSite::where('domain', $domain)->first()?->id;
    }
}
