<?php

namespace Koneko\KonekoWebsiteAdmin\Console\Commands;

use Illuminate\Console\Command;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSeoProfile;

class WebsiteSeoHelperCommand extends Command
{
    protected $signature = 'website:seo
        {--id= : ID del perfil SEO}
        {--slug= : Slug del perfil SEO}
        {--summary : Listar todos los perfiles SEO}
        {--dump : Mostrar información detallada del perfil SEO}
        {--meta : Mostrar metadatos SEO generados (meta tags)}
        {--jsonld : Mostrar JSON-LD del perfil}
        {--active : Filtrar solo perfiles activos (noindex = false)}
    ';

    protected $description = 'Herramientas para gestionar WebsiteSeoProfile: inspección, metadata, JSON-LD, filtros';

    public function handle(): int
    {
        $id   = $this->option('id');
        $slug = $this->option('slug');
        $activeOnly = $this->option('active');

        if ($this->option('summary')) {
            $this->info('📄 Listado de perfiles SEO:');
            $query = WebsiteSeoProfile::select('id', 'slug', 'title', 'type');
            if ($activeOnly) {
                $query->active();
            }
            $all = $query->orderBy('id')->get();

            foreach ($all as $seo) {
                $this->line("[{$seo->id}] {$seo->slug} — {$seo->title} ({$seo->type->value})");
            }
            return self::SUCCESS;
        }

        if ($id || $slug) {
            $seo = $id
                ? WebsiteSeoProfile::find($id)
                : WebsiteSeoProfile::where('slug', $slug)->first();

            if (! $seo) {
                $this->error('❌ Perfil SEO no encontrado.');
                return self::FAILURE;
            }

            if ($this->option('dump')) {
                $this->info("🧾 Información completa del perfil SEO:");
                dump($seo->toArray());
            }

            if ($this->option('meta')) {
                $this->info("📑 Meta Tags generados:");
                dump($seo->getMetaTags());
            }

            if ($this->option('jsonld')) {
                $this->info("🧬 JSON-LD:");
                dump($seo->toJsonLd());
            }

            return self::SUCCESS;
        }

        $this->warn('⚠️ No se especificó ninguna acción. Usa --help para ver las opciones disponibles.');
        return self::SUCCESS;
    }
}
