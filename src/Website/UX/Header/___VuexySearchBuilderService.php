<?php

declare(strict_types=1);

namespace Koneko\KonekoVuexyAdmin\Application\UX\Navbar;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Route;
use Koneko\KonekoVuexyAdmin\Application\UX\Menu\VuexyMenuBuilderService;
use Koneko\KonekoVuexyAdmin\Support\Traits\Cache\InteractsWithKonekoVarsCache;

class ___VuexySearchBuilderService
{
    use InteractsWithKonekoVarsCache;

    private const CACHE_PREFIX = 'vuexy_search_user_id:';

    private Authenticatable $user;

    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
        $this->initCacheConfig(true); // relacionado al menú
    }

    /**
     * Obtiene el índice de búsqueda para el usuario autenticado.
     */
    public function getForUser(): array
    {
        return $this->cacheOrComputeForUser(fn () => $this->buildIndex());
    }

    /**
     * Construye el índice de búsqueda a partir del menú procesado.
     */
    private function buildIndex(): array
    {
        $menu = app(VuexyMenuBuilderService::class)->getForUser($this->user);
        return $this->buildFromMenu($menu);
    }

    /**
     * Recorre el menú para construir el índice plano de rutas accesibles.
     */
    private function buildFromMenu(array $menu, string $parent = ''): array
    {
        $entries = [];

        foreach ($menu as $title => $item) {
            $fullPath = $parent ? "$parent / $title" : $title;

            $url = $item['url'] ?? (
                isset($item['route']) && Route::has($item['route']) ? route($item['route']) : null
            );

            if ($url) {
                $entries[] = [
                    'name' => $fullPath,
                    'icon' => $item['icon'] ?? 'ti ti-point',
                    'url'  => $url,
                ];
            }

            if (!empty($item['submenu'])) {
                $entries = [...$entries, ...$this->buildFromMenu($item['submenu'], $fullPath)];
            }
        }

        return $entries;
    }
}
