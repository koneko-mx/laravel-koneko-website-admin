<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Website\Menu;

use Illuminate\Support\Facades\{Auth, Cache, Config, Route};
use Koneko\KonekoWebsiteAdmin\Models\{WebsiteMenu, WebsiteMenuItem};

class WebsiteMenuRenderer
{
    public static function tree(string $slug): array
    {
        $cacheEnabled = Config::get('koneko.website.menu.cache.enabled', true);
        $cacheTtl     = Config::get('koneko.website.menu.cache.ttl', 3600);

        $cacheKey = self::cacheKey($slug);

        if ($cacheEnabled) {
            return Cache::remember($cacheKey, $cacheTtl, fn() => self::build($slug));
        }

        return self::build($slug);
    }

    public static function clearCache(string $slug): void
    {
        Cache::forget(self::cacheKey($slug));
    }

    public static function clearAllCache(): void
    {
        $prefix = 'website_menu:';
        foreach (Cache::getRedis()->keys("{$prefix}*") as $key) {
            Cache::forget(str_replace(config('cache.prefix') . ':', '', $key));
        }
    }

    private static function cacheKey(string $slug): string
    {
        return "website_menu:{$slug}";
    }

    private static function build(string $slug): array
    {
        $menu = WebsiteMenu::where('slug', $slug)
            ->where('is_active', true)
            ->with(['items' => fn($query) => $query->where('is_active', true)->orderBy('order')])
            ->first();

        if (!$menu) {
            return [];
        }

        return self::buildTree($menu->items->whereNull('parent_id'), $menu->items);
    }

    private static function buildTree($items, $allItems, $parentId = null): array
    {
        $tree = [];

        foreach ($items as $item) {
            if ($item->parent_id !== $parentId) {
                continue;
            }

            if (!self::canAccess($item)) {
                continue;
            }

            if (!self::isVisible($item)) {
                continue;
            }

            $meta = self::generateUrl($item);

            $tree[] = [
                'id'            => $item->id,
                'title'         => $item->localized_title,
                'slug'          => $item->slug,
                'url'           => $item->url ?? 'javascript:;',
                'target'        => $item->target ?? '_self',
                'type'          => $item->type->value ?? 'custom',
                'icon'          => $item->icon,
                'badge'         => $item->badge,
                'badge_color'   => $item->badge_color,
                'method'        => $item->method ?? 'GET',
                'js_event'      => $item->js_event,
                'meta'          => $meta,
                'children'      => self::buildTree($allItems, $allItems, $item->id),
            ];
        }

        return $tree;
    }

    private static function canAccess(WebsiteMenuItem $item): bool
    {
        return self::canAccessRole($item) && self::canAccessPermission($item);
    }

    private static function canAccessRole(WebsiteMenuItem $item): bool
    {
        if (empty($item->roles)) {
            return true;
        }

        if (!Auth::check()) {
            return in_array('guest', $item->roles);
        }

        return Auth::user()->hasAnyRole($item->roles);
    }

    private static function canAccessPermission(WebsiteMenuItem $item): bool
    {
        if (empty($item->permissions)) {
            return true;
        }

        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasAnyPermission($item->permissions);
    }

    private static function isVisible(WebsiteMenuItem $item): bool
    {
        $now = now();

        if ($item->visible_from && $now->lt($item->visible_from)) {
            return false;
        }

        if ($item->visible_until && $now->gt($item->visible_until)) {
            return false;
        }

        return true;
    }

    private static function generateUrl(WebsiteMenuItem &$item): array
    {
        $debugBrokenRoutes = Config::get('koneko.website.menu.debug.show_broken_routes', false);
        $meta = [];

        if (!empty($item->laravel_route)) {
            if (Route::has($item->laravel_route)) {
                $item->url = route($item->laravel_route);
                $meta['route_found'] = true;
            } else {
                $item->url = $debugBrokenRoutes ? 'javascript:; /* broken route */' : 'javascript:;';
                $meta['route_found'] = false;
            }
        } elseif (!empty($item->url)) {
            $meta['route_found'] = true;
        } else {
            $item->url = 'javascript:;';
            $meta['route_found'] = false;
        }

        return $meta;
    }
}
