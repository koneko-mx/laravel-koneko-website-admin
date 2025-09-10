<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Website\UX\Menu;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\{Auth,Route};
use Illuminate\Support\Str;
use Koneko\KonekoVuexyAdmin\Models\User;
use Koneko\KonekoVuexyAdmin\Support\Traits\Cache\InteractsWithKonekoVarsCache;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

/**
 * Clase encargada de construir el menú dinámico Vuexy
 * basado en permisos, configuración, y visibilidad.
 */
class ___WebsiteMenuBuilderService
{
    use InteractsWithKonekoVarsCache;

    /** @var array Arreglo de menú crudo combinado desde todos los módulos */
    private array $rawMenu = [];

    /** @var array Menú final procesado y autorizado */
    private array $finalMenu = [];

    /** @var bool Activa el modo de depuración completo */
    private bool $debugMode = false;

    private ?Authenticatable $user = null;

    private const CACHE_TAG    = 'vuexy-menu';
    private const CACHE_PREFIX = 'vuexy_menu_user_id:';

    /**
     * Constructor del builder
     *
     * @param Authenticatable|null $user Usuario a simular o autenticar
     */
    public function __construct(?Authenticatable $user = null)
    {
        $this->user = $user ?? Auth::user();
        $this->initCacheConfig(true);
    }

    /**
     * Construye el menú procesado, aplicando filtros y visibilidad
     *
     * @return array Menú final listo para renderizado
     */
    public function build(): array
    {
        $this->rawMenu = (new VuexyMenuRegistry())->getMerged();
        $this->finalMenu = $this->processMenu($this->rawMenu);

        $this->assignAutoKeys($this->finalMenu); // Ya lo tienes
        $this->assignSlugs($this->finalMenu);    // <- NUEVO

        return $this->finalMenu;
    }

    /**
     * Aplica filtros de visibilidad y convierte rutas en URLs
     *
     * @param array $menu Arreglo jerárquico del menú
     * @return array Menú procesado
     */
    private function processMenu(array $menu): array
    {
        $result = [];

        foreach ($menu as $key => $item) {
            // Validamos visibilidad
            if (!$this->isVisible($item)) {
                if (config(CoreModule::NAMESPACE.'.'.CoreModule::COMPONENT.'.menu.debug.show_hidden_items', false)) {
                    $item['_meta']['hidden_debug'] = true;

                } else {
                    continue;
                }
            }

            // Header
            if (isset($item['_meta']['type']) && $item['_meta']['type'] === 'header') {
                $result[$key] = ['_meta' => $item['_meta']];
                continue;
            }

            // Convertir route a URL
            $this->convertRouteToUrl($item);

            // Procesar submenu
            if (isset($item['submenu'])) {
                $item['submenu'] = $this->processMenu($item['submenu']);

                if (empty($item['submenu']) && !isset($item['route']) && !isset($item['url'])) {
                    continue;
                }
            }

            $result[$key] = $item;
        }

        // Asignar claves automáticas
        $this->assignAutoKeys($result);

        // Aplicar ordenamiento
        return $this->applySorting($result);
    }

    /**
     * Determina si un ítem de menú es visible para el usuario actual
     *
     * @param array $item Elemento del menú
     * @return bool Verdadero si es visible, falso si debe ocultarse
     */
    private function isVisible(array $item): bool
    {
        $forceVisible = $this->debugMode;

        if ($forceVisible || config(CoreModule::NAMESPACE.'.'.CoreModule::COMPONENT.'.menu.debug.show_hidden_items', false)) {
            return true;
        }

        if (isset($item['_meta']['visible']) && !$item['_meta']['visible']) {
            return false;
        }

        if (isset($item['can']) && !$this->userCan($item['can'])) {
            return (bool) config(CoreModule::NAMESPACE.'.'.CoreModule::COMPONENT.'.menu.debug.show_disallowed_links', false);
        }

        if (isset($item['route']) && !Route::has($item['route'])) {
            return (bool) config(CoreModule::NAMESPACE.'.'.CoreModule::COMPONENT.'.menu.debug.show_broken_routers', false);
        }

        return true;
    }

    /**
     * Evalúa si el usuario tiene permisos para ver un ítem específico
     *
     * @param string|array $permissions Permiso o arreglo de permisos
     * @return bool
     */
    private function userCan(string|array $permissions): bool
    {
        if (!$this->user || !method_exists($this->user, 'hasPermissionTo')) {
            return false;
        }

        try {
            if (is_array($permissions)) {
                foreach ($permissions as $perm) {
                    if ($this->user->hasPermissionTo($perm)) return true;
                }
                return false;
            }

            return $this->user->hasPermissionTo($permissions);

        } catch (PermissionDoesNotExist) {
            return false;
        }
    }

    /**
     * Convierte la clave 'route' en una URL real usando helper route()
     *
     * @param array $item Elemento individual del menú (modificado por referencia)
     */
    private function convertRouteToUrl(array &$item): void
    {
        if (isset($item['route'])) {
            if (Route::has($item['route'])) {
                $item['url'] = route($item['route']);

            } elseif (config(CoreModule::NAMESPACE.'.'.CoreModule::COMPONENT.'.menu.debug.show_broken_routers', false)) {
                $item['url'] = 'javascript:;';

            } else {
                $item['url'] = null;
            }
        }
    }

    /**
     * Asigna claves automáticas y mapeo jerárquico
     */
    private function assignAutoKeys(array &$menu, array &$map = [], string $prefix = '', int &$index = 0): void
    {
        foreach ($menu as $key => &$item) {
            $id = $index++;
            $item['_meta']['auto_id'] = $id;
            $map[$id] = $prefix . $key;

            if (isset($item['submenu']) && is_array($item['submenu'])) {
                $this->assignAutoKeys($item['submenu'], $map, $prefix . $key . '/', $index);
            }
        }
    }

    /**
     * Ordena los elementos del menú usando prioridad, before_to y after_to
     *
     * @param array $items Arreglo de elementos del menú
     * @return array Arreglo ordenado
     */
    private function applySorting(array $items): array
    {
        // 1️⃣ Normalizamos los ítems con prioridad y dependencias
        $nodes = [];

        foreach ($items as $key => $item) {
            $meta = $item['_meta'] ?? [];

            $priority = $meta['priority'] ?? 500;
            $priority = match (true) {
                $priority === 'first' => -999999,
                $priority === 'last'  => 999999,
                is_numeric($priority) => (int) $priority,
                default               => 500,
            };

            $nodes[$key] = [
                'key'       => $key,
                'item'      => $item,
                'before'    => $meta['before_to'] ?? null,
                'after'     => $meta['after_to'] ?? null,
                'priority'  => $priority,
                'inserted'  => false,
            ];
        }

        // 2️⃣ Orden inicial por prioridad
        uasort($nodes, fn($a, $b) => $a['priority'] <=> $b['priority']);

        $sorted = [];

        // 3️⃣ Resolución iterativa de dependencias
        while (!empty($nodes)) {
            $progress = false;

            foreach ($nodes as $key => $node) {
                $canInsert = true;

                // Si tiene dependencia y aún no se ha insertado el target, saltamos
                if ($node['before'] && !isset($sorted[$node['before']])) {
                    $canInsert = false;
                }

                if ($node['after'] && !isset($sorted[$node['after']])) {
                    $canInsert = false;
                }

                if (!$canInsert) {
                    continue;
                }

                // Insertamos en la posición correcta
                if ($node['before'] && isset($sorted[$node['before']])) {
                    $position = array_search($node['before'], array_keys($sorted), true);
                    $sorted = array_slice($sorted, 0, $position, true)
                            + [$key => $node['item']]
                            + array_slice($sorted, $position, null, true);
                } elseif ($node['after'] && isset($sorted[$node['after']])) {
                    $position = array_search($node['after'], array_keys($sorted), true) + 1;
                    $sorted = array_slice($sorted, 0, $position, true)
                            + [$key => $node['item']]
                            + array_slice($sorted, $position, null, true);
                } else {
                    // Sin dependencias, o ya satisfechas
                    $sorted[$key] = $node['item'];
                }

                unset($nodes[$key]);
                $progress = true;
            }

            // Prevención de loop infinito (por dependencias circulares o mal definidas)
            if (!$progress) {
                // Lo que queda se inserta en orden de prioridad
                foreach ($nodes as $key => $node) {
                    $sorted[$key] = $node['item'];
                }
                break;
            }
        }

        return $sorted;
    }

    protected function getUserId(): int|string
    {
        return $this->user->id ?? throw new \RuntimeException('No se puede obtener el ID de usuario.');
    }

    /**
     * Obtiene el menú procesado para un usuario específico, visitante o el autenticado.
     * Respeta la configuración de caché desde config/vuexy.php (VUEXY_CACHE_MENU).
     *
     * @param Authenticatable|null $user Usuario explícito o null para visitante.
     * @return array Menú final procesado y autorizado para el usuario.
     */
    public function getForUser(null|Authenticatable $user = null): array
    {
        $this->user = $user ?? Auth::user();

        if (!$this->user || !$this->user->id) {
            return $this->build();
        }

        $cacheKey = static::makeCacheKeyForUser($this->user->id);

        return $this->cacheOrComputeTagged($cacheKey, function () {
            logger()->info("Regenerando menú para usuario {$this->user->id}");
            return $this->build();
        });
    }

    private function assignSlugs(array &$menu, string $trail = ''): void
    {
        foreach ($menu as $key => &$item) {
            $currentTrail = trim($trail . ' ' . $key);
            $slug = Str::slug($currentTrail);

            if (!empty($item['submenu']) && empty($item['route'])) {
                $item['_slug'] = $slug;
                $item['_path'] = $currentTrail;
            }

            if (!empty($item['submenu'])) {
                $this->assignSlugs($item['submenu'], $currentTrail . ' /');
            }
        }
    }

    public function getNodeTreeBySlug(string $slug, User $user): ?array
    {
        $menu = $this->getForUser($user);
        return $this->searchTreeBySlug($menu, $slug);
    }

    private function searchTreeBySlug(array $menu, string $slug, array $trail = []): ?array
    {
        foreach ($menu as $key => $item) {
            $currentLabel = $item['_meta']['widget_label'] ?? $item['_meta']['original_key'] ?? $key;

            $currentTrail = array_merge($trail, [[
                'label' => $currentLabel,
                'icon' => $item['icon'] ?? 'ti ti-folder',
                'description' => $item['description'] ?? '',
                'slug' => $item['_slug'] ?? null,
                'auto_id' => $item['_meta']['auto_id'] ?? null,
            ]]);

            if (($item['_slug'] ?? null) === $slug) {
                return [
                    'node' => $item,
                    'tree' => $currentTrail,
                ];
            }

            if (!empty($item['submenu'])) {
                $found = $this->searchTreeBySlug($item['submenu'], $slug, $currentTrail);
                if ($found) {
                    return $found;
                }
            }
        }

        return null;
    }

    public static function clearAllCache(): void
    {
        static::flushCacheTags(static::CACHE_TAG);
    }
}
