<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Koneko\KonekoWebsiteAdmin\Models\SitemapUrl;

class SitemapGenerate extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Genera un sitemap.xml con rutas dinámicas del sistema';

    public function handle()
    {
        $urls = SitemapUrl::where('is_active', true)->get();

        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($urls as $url) {
            $sitemap .= "    <url><loc>{$url->url}</loc>" . PHP_EOL;
            $sitemap .= "    <changefreq>{$url->changefreq}</changefreq>" . PHP_EOL;
            $sitemap .= "    <priority>{$url->priority}</priority>" . PHP_EOL;
            if ($url->lastmod) {
                $sitemap .= "    <lastmod>{$url->lastmod->toDateString()}</lastmod>" . PHP_EOL;
            }
            $sitemap .= "    </url>" . PHP_EOL;
        }

        $sitemap .= '</urlset>';

        Storage::disk('public')->put('sitemap.xml', $sitemap);

        $this->info('✅ Sitemap generado en storage/app/public/sitemap.xml');
    }
}
