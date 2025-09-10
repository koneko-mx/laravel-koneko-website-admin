<?php

declare(strict_types=1);

// Este archivo **NO se registra como config**, es usado por VuexyMenuRegistry

return [
    'Sitios Web' => [
        '_meta' => [
            'icon' => 'ti ti-world',
            'description' => 'Administra múltiples sitios, perefiles SEO y configuración global.',
            'home_at_root' => true,
            'priority' => 100,
        ],
        'submenu' => [
            'Sitios web' => [
                'icon' => 'ti ti-world',
                'route' => 'admin.website-admin.websites.manager.index',
                'can' => 'admin.website-admin.websites.manager.view',
                'description' => 'Listado general de sitios activos y configurables.',
            ],
            'Perfiles SEO' => [
                'icon' => 'ti ti-graph',
                'route' => 'admin.website-admin.seo.profile.index',
                'can' => 'admin.website-admin.seo.profile.view',
                'description' => 'Define metadatos, OG y Twitter Cards del sitio.',
            ],
            'Plantillas' => [
                'icon' => 'ti ti-template',
                'route' => 'admin.website-admin.cms.templates.index',
                'can' => 'admin.website-admin.cms.templates.view',
                'description' => 'Gestiona y asigna plantillas de presentación.',
            ],
            'Bloques de contenido' => [
                'icon' => 'ti ti-box-model',
                'route' => 'admin.website-admin.cms.blocks.index',
                'can' => 'admin.website-admin.cms.blocks.view',
                'description' => 'Visualiza y limpia caché por bloques individuales.',
            ],
            'Componentes Blade' => [
                'icon' => 'ti ti-fidget-spinner',
                'route' => 'admin.website-admin.cms.blade.index',
                'can' => 'admin.website-admin.cms.blade.view',
                'description' => 'Gestiona componentes Blade individuales.',
            ],
        ],
    ],
    'Sistema de Cache' => [
        '_meta' => [
            'icon' => 'ti ti-database-cog',
            'description' => 'Herramientas avanzadas de rendimiento y renderizado.',
            'widget_label' => 'Motor de Cache',
            'home_at_root' => true,
            'priority' => 500,
        ],
        'submenu' => [
            'HTML completo' => [
                'icon' => 'ti ti-file-type-html',
                'route' => 'admin.website-admin.cache.full.index',
                'can' => 'admin.website-admin.cache.full.view',
                'description' => 'Gestiona caché render de páginas completas.',
            ],
            'Bloques de contenido' => [
                'icon' => 'ti ti-box-model',
                'route' => 'admin.website-admin.cache.blocks.index',
                'can' => 'admin.website-admin.cache.blocks.view',
                'description' => 'Visualiza y limpia caché por bloques individuales.',
            ],
            'Previews firmadas' => [
                'icon' => 'ti ti-key',
                'route' => 'admin.website-admin.cache.previews.index',
                'can' => 'admin.website-admin.cache.previews.view',
                'description' => 'Controla la vigencia y firma de URLs temporales.',
            ],
        ],
    ],
];
