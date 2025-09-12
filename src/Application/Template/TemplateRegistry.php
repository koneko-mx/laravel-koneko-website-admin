<?php

namespace Koneko\KonekoWebsiteAdmin\Application\Template;

final class TemplateRegistry
{
    public static function build(): array
    {
        $sources = (array) config('koneko.website-admin.templates', []);
        $flat = [];
        $groups = [];

        foreach ($sources as $pkgKey => $relativePath) {
            $path = base_path($relativePath);
            if (!is_file($path)) continue;

            $pkg = require $path;

            $pkgId   = $pkg['id']   ?? $pkg['package'] ?? $pkgKey;
            $pkgName = $pkg['name'] ?? $pkgId;
            $pkgTags = (array) ($pkg['tags'] ?? []);
            $version = $pkg['version'] ?? null;

            foreach ((array) ($pkg['templates'] ?? []) as $tplId => $tpl) {
                // Soportar formato “simple”: 'tpl' => 'Nombre'
                if (is_string($tpl)) {
                    $tpl = ['name' => $tpl];
                }

                $name = $tpl['name'] ?? $tplId;
                $meta = (array) ($tpl['meta'] ?? []);
                $tags = array_values(array_unique(array_merge($pkgTags, (array) ($tpl['tags'] ?? []))));

                // Resolver paths relativos a absolutos
                if (!empty($meta['thumbnail']) && is_string($meta['thumbnail'])) {
                    $abs = base_path($meta['thumbnail']);
                    $meta['thumbnail_abs'] = is_file($abs) ? $abs : null;
                }

                $key = "{$pkgId}::{$tplId}";
                $flat[$key] = [
                    'key'          => $key,
                    'package'      => $pkgId,
                    'package_name' => $pkgName,
                    'package_ver'  => $version,
                    'template'     => $tplId,
                    'label'        => $name,
                    'tags'         => $tags,
                    'meta'         => $meta,
                ];

                $groups[$pkgId]['label'] ??= $pkgName;
                $groups[$pkgId]['items'][$key] = $name;
            }
        }

        // Orden: por package y por weight/label
        foreach ($flat as $k => $row) {
            $weight = $row['meta']['weight'] ?? 1000;
            $flat[$k]['_sort'] = sprintf('%08d|%s|%s', (int)$weight, $row['package_name'], $row['label']);
        }
        uasort($flat, fn($a,$b) => $a['_sort'] <=> $b['_sort']);
        foreach ($groups as $pkg => &$g) {
            asort($g['items'], SORT_NATURAL | SORT_FLAG_CASE);
        }

        return [
            'flat'    => $flat,
            'groups'  => $groups,
        ];
    }

    /* ===== Base ===== */

    public static function all(): array
    {
        return self::build()['flat'];
    }

    public static function grouped(): array
    {
        return self::build()['groups'];
    }

    public static function key(string $package, string $layout): string
    {
        return "{$package}::{$layout}";
    }

    public static function split(string $composite): array
    {
        return explode('::', $composite, 2);
    }

    public static function has(string $package, string $layout): bool
    {
        return isset(self::all()[self::key($package, $layout)]);
    }

    public static function get(string $package, string $layout): ?array
    {
        return self::all()[self::key($package, $layout)] ?? null;
    }

    public static function validKeys(): array
    {
        return array_keys(self::all());
    }

    /* ===== UI: opciones agrupadas (optgroup nativo) ===== */

    /** Estructura: ['pkg_id' => ['label' => 'Nombre', 'items' => ['pkg::tpl' => 'Label', ...]], ...] */
    public static function groupedOptions(?string $package = null, ?array $tags = null): array
    {
        $groups = self::grouped();
        if (!$package && !$tags) {
            return $groups;
        }

        // Filtrado por paquete/tags (tags aplica sobre registry plano)
        $filtered = [];
        $registry = self::all();

        foreach ($groups as $pkgId => $group) {
            if ($package && $pkgId !== $package) continue;

            foreach ($group['items'] as $key => $label) {
                if ($tags && !self::rowHasAllTags($registry[$key]['tags'] ?? [], $tags)) {
                    continue;
                }
                $filtered[$pkgId]['label'] = $group['label'];
                $filtered[$pkgId]['items'][$key] = $label;
            }
        }

        return $filtered;
    }

    /* ===== UI: Select2 (grouped) ===== */

    /**
     * Select2 grouped format:
     * [
     *   ['text' => 'Porto', 'children' => [['id'=>'pkg::tpl','text'=>'Label'], ...]],
     *   ...
     * ]
     */
    public static function select2Data(
        ?string $q = null,
        ?string $package = null,
        ?array $tags = null,
        int $limit = 200
    ): array {
        $q = $q ? mb_strtolower($q) : null;

        $groups = self::groupedOptions($package, $tags);
        $out    = [];
        $count  = 0;

        foreach ($groups as $pkgId => $group) {
            $children = [];
            foreach ($group['items'] as $key => $label) {
                if ($q && !self::matches($key, $label, $q)) continue;
                $children[] = ['id' => $key, 'text' => $label];
                if (++$count >= $limit) break 2;
            }
            if ($children) {
                $out[] = ['text' => $group['label'], 'children' => $children];
            }
        }
        return $out;
    }

    /* ===== Metadatos para tarjetas / previews ===== */

    /** Retorna solo la sección meta del template (thumbnail, preview_url, etc.) */
    public static function meta(string $compositeKey): ?array
    {
        return self::all()[$compositeKey]['meta'] ?? null;
    }

    public static function label(string $compositeKey): ?string
    {
        return self::all()[$compositeKey]['label'] ?? null;
    }

    public static function packageLabel(string $package): ?string
    {
        return self::grouped()[$package]['label'] ?? null;
    }

    /* ===== Helpers internos ===== */

    private static function matches(string $key, string $label, string $q): bool
    {
        $hay = mb_strtolower($label.' '.$key);
        return str_contains($hay, $q);
    }

    private static function rowHasAllTags(array $rowTags, array $required): bool
    {
        if (!$required) return true;
        $rowTags = array_map('strval', $rowTags);
        foreach ($required as $t) {
            if (!in_array((string)$t, $rowTags, true)) return false;
        }
        return true;
    }
}
