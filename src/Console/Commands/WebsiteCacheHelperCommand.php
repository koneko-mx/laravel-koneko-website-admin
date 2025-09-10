<?php

namespace Koneko\KonekoWebsiteAdmin\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteContent;
use Koneko\KonekoWebsiteAdmin\Application\Services\WebsiteRenderCacheService;

class WebsiteCacheHelperCommand extends Command
{
    protected $signature = 'website:cache
        {--slug= : Slug de la página (para operaciones individuales)}
        {--clear : Limpia el caché de una página o de todo el sitio}
        {--summary : Lista las claves de caché activas para contenido renderizado}
        {--simulate : Simula el render y cachea la página especificada}
        {--ttl=900 : TTL en segundos para el cacheo simulado}
    ';

    protected $description = 'Herramientas para depurar y administrar el caché HTML renderizado del Website';

    public function handle(): int
    {
        $slug = $this->option('slug');
        $ttl  = (int) $this->option('ttl');

        if ($this->option('summary')) {
            $this->info('🔍 Claves de cacheo HTML renderizado (Redis/Tags):');
            $this->line('- (Nota: Laravel no permite listar claves directamente desde tags)');
            $this->line('💡 Usa observabilidad desde Redis CLI para inspección manual o eventos de log.');
            return self::SUCCESS;
        }

        if ($this->option('clear')) {
            if ($slug) {
                WebsiteRenderCacheService::invalidate('website', $slug);
                $this->info("🧹 Cache HTML limpiado para la página: {$slug}");
            } else {
                Cache::tags(['rendered_html', 'website'])->flush();
                $this->info('🧼 Cache HTML global de website limpiado.');
            }
            return self::SUCCESS;
        }

        if ($this->option('simulate')) {
            if (! $slug) {
                $this->error('❌ Debes proporcionar un slug con --slug para simular cacheo.');
                return self::FAILURE;
            }

            $content = WebsiteContent::published()->bySlug($slug)->first();
            if (! $content) {
                $this->error("❌ Contenido no encontrado para slug: {$slug}");
                return self::FAILURE;
            }

            $html = WebsiteRenderCacheService::getOrRender('website', $content->slug, fn() => $content->toHtml(), $ttl);
            $this->info("✅ HTML cacheado para '{$slug}' con TTL de {$ttl} segundos");
            return self::SUCCESS;
        }

        $this->warn('⚠️ No se especificó ninguna acción. Usa --help para ver las opciones disponibles.');
        return self::SUCCESS;
    }
}
