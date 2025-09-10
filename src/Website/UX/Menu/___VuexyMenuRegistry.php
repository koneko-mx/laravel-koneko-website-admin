<?php

declare(strict_types=1);

namespace Koneko\KonekoVuexyAdmin\Website\UX\Menu;

use Illuminate\Support\Facades\File;
use Koneko\KonekoVuexyAdmin\Application\Bootstrap\KonekoModuleRegistry;

class ___VuexyMenuRegistry
{
    private array $baseMenu = [];
    private array $moduleMenus = [];
    private array $projectMenu = [];

    /**
     * Construye y devuelve el menú completo del ERP.
     */
    public function getMerged(): array
    {
        $this->baseMenu    = $this->loadBaseMenu();
        $this->moduleMenus = $this->loadModuleMenus();
        $this->projectMenu = $this->loadProjectMenu();


        // Create a single array with all menus in the correct order
        $allMenus = array_merge(
            [$this->baseMenu],
            $this->moduleMenus,
            [$this->projectMenu]
        );

        return $this->sortByPriority(
            $this->mergeMenus(...$allMenus)
        );
    }

    /**
     * Devuelve la estructura sin mezclar (útil para debugging).
     */
    public function getRaw(): array
    {
        return [
            'base'    => $this->baseMenu,
            'modules' => $this->moduleMenus,
            'project' => $this->projectMenu,
        ];
    }

    /**
     * Carga el menú base del sistema (no registrado en config).
     */
    private function loadBaseMenu(): array
    {
        $path = base_path('vendor/koneko/laravel-koneko-vuexy-admin/config/vuexy_menu.php');

        return File::exists($path) ? require $path : [];
    }

    /**
     * Carga todos los menús definidos por módulos activos.
     */
    private function loadModuleMenus(): array
    {
        $menus = [];

        foreach (KonekoModuleRegistry::enabled() as $module) {
            $menuPath = $module->extensions['menu']['path'] ?? null;

            if ($menuPath) {
                $fullPath = $module->basePath . DIRECTORY_SEPARATOR . $menuPath;
                if (File::exists($fullPath)) {
                    $menus[] = require $fullPath;
                }
            }
        }

        return $menus;
    }

    /**
     * Carga un menú adicional desde el proyecto (si existe).
     */
    private function loadProjectMenu(): array
    {
        $menuConfig = config('vuexy-admin.extensions.menu');

        // Si no está definido o es null, no carga menú del proyecto
        if (!$menuConfig || !isset($menuConfig['path'])) {
            return [];
        }

        $path = $menuConfig['path'];

        // Permitir rutas relativas a storage/
        if (str_starts_with($path, 'storage/')) {
            $fullPath = storage_path(substr($path, strlen('storage/')));
        } else {
            // Por defecto: ruta relativa al config/
            $fullPath = base_path($path);
        }

        return File::exists($fullPath) ? require $fullPath : [];
    }

    /**
     * Fusión recursiva de todos los menús (base + módulos + proyecto).
     */
    private function mergeMenus(array ...$menus): array
    {
        $merged = [];

        foreach ($menus as $menu) {
            $merged = $this->deepMerge($merged, $menu);
        }

        return $merged;
    }

    /**
     * Fusión profunda para mantener jerarquía y submenús.
     */
    private function deepMerge(array $base, array $extension): array
    {
        foreach ($extension as $key => $value) {
            if (!isset($base[$key])) {
                $base[$key] = $value;
                continue;
            }

            if (is_array($base[$key]) && is_array($value)) {
                // Fusionar submenu recursivamente si existen
                if (isset($base[$key]['submenu']) && isset($value['submenu'])) {
                    $base[$key]['submenu'] = $this->deepMerge(
                        $base[$key]['submenu'],
                        $value['submenu']
                    );
                }

                // Fusionar _meta con prioridad del módulo o del proyecto
                if (isset($base[$key]['_meta']) && isset($value['_meta'])) {
                    $base[$key]['_meta'] = array_merge(
                        $base[$key]['_meta'],
                        $value['_meta']
                    );
                }

                // Merge general sin aplastar `submenu` ni `_meta`
                $merged = array_merge($base[$key], $value);
                $merged['submenu'] = $base[$key]['submenu'] ?? $value['submenu'] ?? null;
                $merged['_meta'] = $base[$key]['_meta'] ?? $value['_meta'] ?? null;

                $base[$key] = $merged;

            } else {
                // Sobrescribir directamente si no es array
                $base[$key] = $value;
            }
        }

        return $base;
    }

    /**
     * Ordena un menú por _meta.priority y claves 'before' / 'after'.
     */
    private function sortByPriority(array $menu): array
    {
        // Paso 1: extraer todos los ítems con claves
        $items = [];
        foreach ($menu as $key => $value) {
            $meta = $value['_meta'] ?? [];
            $items[$key] = [
                'key'      => $key,
                'value'    => $value,
                'before'   => $meta['before'] ?? null,
                'after'    => $meta['after'] ?? null,
                'priority' => $meta['priority'] ?? 999,
            ];
        }

        // Paso 2: ordenar respetando before/after y priority
        $sorted = [];
        $inserted = [];

        while (count($items) > 0) {
            foreach ($items as $key => $item) {
                $canInsert = true;

                if ($item['before'] && !isset($inserted[$item['before']])) {
                    $canInsert = false;
                }

                if ($item['after'] && !isset($inserted[$item['after']])) {
                    $canInsert = false;
                }

                if ($canInsert) {
                    $position = null;

                    if ($item['before']) {
                        $position = array_search($item['before'], array_keys($sorted), true);
                    } elseif ($item['after']) {
                        $position = array_search($item['after'], array_keys($sorted), true);
                        $position = $position !== false ? $position + 1 : null;
                    }

                    if ($position !== null) {
                        $sorted = array_slice($sorted, 0, $position, true)
                                + [$key => $item['value']]
                                + array_slice($sorted, $position, null, true);
                    } else {
                        $sorted[$key] = $item['value'];
                    }

                    $inserted[$key] = true;
                    unset($items[$key]);
                }
            }

            // Si no pudimos insertar nada, rompemos el ciclo para evitar loop infinito
            if (count($items) === count(array_diff_key($items, $inserted))) {
                // Ordenar el resto por priority como fallback
                uasort($items, fn ($a, $b) => $a['priority'] <=> $b['priority']);
                foreach ($items as $key => $item) {
                    $sorted[$key] = $item['value'];
                }
                break;
            }
        }

        // Paso 3: ordenar submenús recursivamente
        foreach ($sorted as &$item) {
            if (isset($item['submenu']) && is_array($item['submenu'])) {
                $item['submenu'] = $this->sortByPriority($item['submenu']);
            }
        }

        return $sorted;
    }

}
