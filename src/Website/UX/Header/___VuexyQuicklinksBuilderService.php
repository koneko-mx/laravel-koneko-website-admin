<?php

declare(strict_types=1);

namespace Koneko\KonekoVuexyAdmin\Application\UX\Navbar;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\{Route,Auth};
use Koneko\KonekoVuexyAdmin\Application\UX\Menu\VuexyMenuBuilderService;
use Koneko\KonekoVuexyAdmin\Models\Setting;
use Koneko\KonekoVuexyAdmin\Support\Traits\Cache\InteractsWithKonekoVarsCache;

class ___VuexyQuicklinksBuilderService
{
    use InteractsWithKonekoVarsCache;

    private array $quicklinkRoutes = [];

    private ?Authenticatable $user = null;

    private const CACHE_PREFIX = 'vuexy_quick_links_user_id:';

    public function __construct(?Authenticatable $user = null)
    {
        $this->user = $user ?? Auth::user();
        $this->initCacheConfig(true);
    }

    public function getForUser(): array
    {
        $quickLinks = $this->cacheOrComputeForUser(fn () => $this->buildQuicklinks());

        $currentRoute = Route::currentRouteName();
        $quickLinks['current_page_in_list'] = $this->isCurrentPageInList($quickLinks, $currentRoute);

        return $quickLinks;
    }

    private function buildQuicklinks(): array
    {
        $menu = app(VuexyMenuBuilderService::class)->getForUser($this->user);

        $setting = Setting::where('user_id', $this->user->id)
            ->where('key', 'quicklinks')
            ->first();

        $this->quicklinkRoutes = $setting ? json_decode($setting->value, true) : [];

        $links = [];
        $this->collectFromMenu($menu, $links);

        return [
            'totalLinks' => count($links),
            'rows' => array_chunk($links, 2),
        ];
    }


    private function collectFromMenu(array $menu, array &$links, ?string $parent = null): void
    {
        foreach ($menu as $title => $item) {
            $route = $item['route'] ?? null;

            if ($route && in_array($route, $this->quicklinkRoutes)) {
                $links[] = [
                    'title' => $title,
                    'subtitle' => $parent ?? config('app.name'),
                    'icon' => $item['icon'] ?? 'ti ti-point',
                    'url' => Route::has($route) ? route($route) : ($item['url'] ?? 'javascript:;'),
                    'route' => $route,
                ];
            }

            if (isset($item['submenu']) && is_array($item['submenu'])) {
                $this->collectFromMenu($item['submenu'], $links, $title);
            }
        }
    }

    private function isCurrentPageInList(array $quickLinks, string $route): bool
    {
        foreach ($quickLinks['rows'] ?? [] as $row) {
            foreach ($row as $item) {
                if (($item['route'] ?? null) === $route) {
                    return true;
                }
            }
        }

        return false;
    }
}
