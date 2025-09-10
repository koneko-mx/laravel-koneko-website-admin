<?php

namespace Koneko\KonekoWebsiteAdmin\Console\Commands;

use Illuminate\Console\Command;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteContent;
use Illuminate\Support\Facades\Cache;

class WebsiteContentHelperCommand extends Command
{
    protected $signature = 'website:content
        {--slug= : Slug del contenido a consultar}
        {--id= : ID del contenido}
        {--preview : Mostrar URL de previsualización firmada}
        {--dump : Mostrar contenido completo con metadata}
        {--summary : Listado resumen de contenidos}
        {--clear-cache : Limpia la caché del contenido especificado}
        {--clear-all-cache : Limpia toda la caché HTML renderizada}
        {--versions : Listar versiones del contenido}
        {--meta : Mostrar metadatos SEO efectivos}
        {--html : Renderizar HTML del contenido en consola}
        {--canonical : Mostrar URL canónica}
        {--ttl= : TTL en minutos para previsualización (default 30)}
        {--routes : Mostrar rutas públicas generadas a partir de los slugs}
    ';

    protected $description = 'Utilidades para contenidos web: cacheo, previsualización, versiones, SEO, HTML, rutas, etc';

    public function handle(): int
    {
        $slug = $this->option('slug');
        $id   = $this->option('id');
        $ttl  = (int) ($this->option('ttl') ?? 30);

        if ($this->option('clear-all-cache')) {
            Cache::tags(['rendered_html'])->flush();
            $this->info('🧹 Caché HTML global limpiada.');
            return self::SUCCESS;
        }

        if ($this->option('routes')) {
            $this->info('🌐 Rutas públicas de contenido publicado:');
            WebsiteContent::published()->orderBy('id')->get()->each(function ($content, $i) {
                $url = url($content->slug);
                $this->line(sprintf("[%d] %s → %s", $i + 1, $url, $content->slug));
            });
            return self::SUCCESS;
        }

        if ($slug || $id) {
            $content = $slug
                ? WebsiteContent::where('slug', $slug)->first()
                : WebsiteContent::find($id);

            if (! $content) {
                $this->error('❌ Contenido no encontrado.');
                return self::FAILURE;
            }

            if ($this->option('clear-cache')) {
                Cache::tags(["rendered_html", "website", "website_{$content->slug}"])->flush();
                $this->info("🧹 Caché HTML de '{$content->slug}' limpiada.");
            }

            if ($this->option('preview')) {
                $url = $content->previewUrl(auth()->id(), $ttl);
                $this->line("🔍 Vista previa: <comment>{$url}</comment>");
            }

            if ($this->option('dump')) {
                $this->info("📦 Contenido completo:");
                dump($content->toArray());
            }

            if ($this->option('versions')) {
                $this->info("📜 Versiones:");
                foreach ($content->versions as $v) {
                    $this->line(" - {$v->version_label} [ID: {$v->id}] ({$v->created_at})");
                }
            }

            if ($this->option('meta')) {
                $this->info("📄 Metadatos SEO efectivos:");
                dump($content->getEffectiveSeoMetadata());
            }

            if ($this->option('canonical')) {
                $this->line("🔗 Canonical URL: <info>{$content->getCanonicalUrl()}</info>");
            }

            if ($this->option('html')) {
                $this->info("🖼 HTML renderizado:");
                $this->line($content->toHtml());
            }
        }

        if ($this->option('summary')) {
            $this->info("📚 Resumen de contenidos:");
            $all = WebsiteContent::select('id', 'slug', 'title', 'template', 'type', 'status')
                ->orderBy('id', 'asc')->get();

            foreach ($all as $c) {
                $flag = $c->status == "draft" ? '📝' : '✅';
                $this->line("[{$c->id}] {$flag} {$c->slug} — {$c->title} ({$c->template})");
            }
        }

        if (! $slug && ! $id && ! $this->option('summary') && ! $this->option('clear-all-cache') && ! $this->option('routes')) {
            $this->warn("⚠️ No se especificó ninguna acción. Usa --help para ver las opciones disponibles.");
        }

        return self::SUCCESS;
    }
}
