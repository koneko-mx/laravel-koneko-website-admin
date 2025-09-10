<?php

namespace Koneko\KonekoWebsiteAdmin\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Koneko\KonekoVuexyAdmin\Models\User;
use Koneko\KonekoWebsiteAdmin\Website\Menu\WebsiteMenuRenderer;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteMenu;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'website:menu')]
class WebsiteMenuHelperCommand extends Command
{
    protected $signature = 'website:menu
        {--list : Lista todos los menús disponibles}
        {--tree= : Muestra el árbol completo del menú (por slug)}
        {--slug= : Slug del menú a procesar}
        {--as= : Simula visibilidad como visitante o user:ID}
        {--json : Mostrar como JSON}
        {--dump : Mostrar con dump()}
        {--summary : Mostrar resumen del menú}
        {--clear-cache : Limpia caché del menú indicado}
        {--export= : Exporta el menú a un archivo JSON para seeder}
        {--id-node= : Muestra un nodo por ID auto-generado}';

    protected $description = 'Comando de ayuda para explorar, depurar y exportar los menús del Website.';

    public function handle(): int
    {
        $slug     = $this->option('slug') ?? $this->option('tree');
        $summary  = $this->option('summary');
        $as       = $this->option('as');
        $asUserId = null;

        if ($this->option('list')) {
            $this->listMenus();
            return self::SUCCESS;
        }

        if ($this->option('clear-cache')) {
            if ($slug) {
                WebsiteMenuRenderer::clearCache($slug);
                $this->info("🔁 Caché limpiada para el menú '{$slug}'");
            } else {
                WebsiteMenuRenderer::clearAllCache();
                $this->info("🔁 Caché global de menú limpiada");
            }
            return self::SUCCESS;
        }

        if (! $slug) {
            $this->error('⚠️  Debes especificar el slug del menú con --slug o --tree.');
            return self::FAILURE;
        }

        $user = null;
        if ($as === 'visitor') {
            $user = null;
        } elseif (str_starts_with($as, 'user:')) {
            $asUserId = (int) str_replace('user:', '', $as);
            $user = User::find($asUserId);
        }

        $tree = WebsiteMenuRenderer::tree($slug, $user);

        if ($this->option('id-node')) {
            $nodeId = (int) $this->option('id-node');
            $node   = $this->findNodeById($tree, $nodeId);
            return $this->renderOutput($node);
        }

        if ($this->option('export')) {
            $file = $this->option('export');
            File::put($file, json_encode($tree, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->info("📦 Menú exportado a: {$file}");
            return self::SUCCESS;
        }

        if ($summary) {
            $this->renderSummary($tree);
            return self::SUCCESS;
        }

        return $this->renderOutput($tree);
    }

    protected function listMenus(): void
    {
        $this->info("🧭 Menús disponibles:");
        foreach (WebsiteMenu::all() as $menu) {
            $this->line("- [{$menu->id}] {$menu->slug} — {$menu->title}");
        }
    }

    protected function renderOutput(array $tree): int
    {
        if ($this->option('json')) {
            $this->line(json_encode($tree, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } elseif ($this->option('dump')) {
            dump($tree);
        } else {
            $this->warn('⚠️  Usa --json, --dump o --summary para mostrar la salida.');
        }
        return self::SUCCESS;
    }

    protected function renderSummary(array $items, string $prefix = ''): void
    {
        foreach ($items as $item) {
            $id     = $item['id'] ?? '??';
            $title  = $item['title'] ?? '[sin título]';
            $this->line("[#{$id}] {$prefix}{$title}");

            if (!empty($item['children'])) {
                $this->renderSummary($item['children'], $prefix . '  └ ');
            }
        }
    }

    protected function findNodeById(array $items, int $id): ?array
    {
        foreach ($items as $item) {
            if (($item['id'] ?? null) === $id) {
                return $item;
            }
            if (!empty($item['children'])) {
                $found = $this->findNodeById($item['children'], $id);
                if ($found) return $found;
            }
        }
        return null;
    }
}
