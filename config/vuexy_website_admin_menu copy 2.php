<?php

declare(strict_types=1);

// Este archivo **NO se registra como config**, es usado por VuexyMenuRegistry

return [
    'Sitios Web' => [
        '_meta' => [
            'icon' => 'ti ti-world',
            'description' => 'Administra múltiples sitios, dominios, plantillas y configuración global.',
            'widget_label' => 'Sitios Web y Dominios',
            'home_at_root' => true,
            'priority' => 100,
        ],
        'submenu' => [
            'Todos los sitios' => [
                'icon' => 'ti ti-world',
                'route' => 'admin.website-admin.sites.index',
                'can' => 'admin.website-admin.sites.view',
                'description' => 'Listado general de sitios activos y configurables.',
            ],
            'Crear nuevo sitio' => [
                'icon' => 'ti ti-world-plus',
                'route' => 'admin.website-admin.sites.create',
                'can' => 'admin.website-admin.sites.create',
                'description' => 'Inicia un nuevo sitio web desde cero.',
            ],
        ],
    ],
    'Configuración del Sitio' => [
        '_meta' => [
            'icon' => 'ti ti-settings-cog',
            'description' => 'Ajustes, branding, visibilidad y plantilla del sitio actual.',
            'widget_label' => 'Configuración de Sitio Web',
            'home_at_root' => true,
            'priority' => 150,
        ],
        'submenu' => [
            'General y Branding' => [
                'icon' => 'ti ti-tools',
                'route' => 'admin.website-admin.settings.general.index',
                'can' => 'admin.website-admin.settings.general.view',
                'description' => 'Nombre, idioma, favicon, y plantilla activa.',
            ],
            'Indexación y Robots' => [
                'icon' => 'ti ti-search',
                'route' => 'admin.website-admin.settings.indexing.index',
                'can' => 'admin.website-admin.settings.indexing.view',
                'description' => 'Controla el acceso de motores de búsqueda a tu sitio.',
            ],
            'Canonical y manifest.json' => [
                'icon' => 'ti ti-file-code',
                'route' => 'admin.website-admin.settings.canonical.index',
                'can' => 'admin.website-admin.settings.canonical.view',
                'description' => 'Evita duplicados y mejora la visibilidad PWA.',
            ],
        ],
    ],
    'SEO y Metadatos' => [
        '_meta' => [
            'icon' => 'ti ti-zoom-code',
            'description' => 'Herramientas de optimización SEO, metadatos y JSON-LD.',
            'widget_label' => 'SEO & Metadatos',
            'home_at_root' => true,
            'priority' => 200,
        ],
        'submenu' => [
            'Perfil SEO' => [
                'icon' => 'ti ti-graph',
                'route' => 'admin.website-admin.seo.profile.index',
                'can' => 'admin.website-admin.seo.profile.view',
                'description' => 'Define metadatos, OG y Twitter Cards del sitio.',
            ],
            'JSON-LD y Schema.org' => [
                'icon' => 'ti ti-code-dots',
                'route' => 'admin.website-admin.seo.jsonld.index',
                'can' => 'admin.website-admin.seo.jsonld.view',
                'description' => 'Configura estructuras para Google y otros buscadores.',
            ],
            'Mapa del sitio y Robots' => [
                'icon' => 'ti ti-hierarchy',
                'route' => 'admin.website-admin.seo.sitemap.index',
                'can' => 'admin.website-admin.seo.sitemap.view',
                'description' => 'Controla el sitemap.xml y robots.txt globalmente.',
            ],
        ],
    ],
    'CMS Koneko' => [
        '_meta' => [
            'icon' => 'ti ti-layout-dashboard',
            'description' => 'Gestión de contenido visual, bloques, menús y plantillas.',
            'widget_label' => 'Editor de Contenido CMS',
            'home_at_root' => true,
            'priority' => 300,
        ],
        'submenu' => [
            'Páginas y bloques' => [
                'icon' => 'ti ti-file-text',
                'route' => 'admin.website-admin.cms.contents.index',
                'can' => 'admin.website-admin.cms.contents.view',
                'description' => 'Administra contenido dinámico estructurado por bloques.',
            ],
            'Menús del sitio' => [
                'icon' => 'ti ti-hierarchy-3',
                'route' => 'admin.website-admin.cms.menus.index',
                'can' => 'admin.website-admin.cms.menus.view',
                'description' => 'Gestiona la navegación del sitio.',
            ],
            'Versiones y previews' => [
                'icon' => 'ti ti-clock-edit',
                'route' => 'admin.website-admin.cms.versions.index',
                'can' => 'admin.website-admin.cms.versions.view',
                'description' => 'Historial de versiones de contenido y vista previa.',
            ],
            'Plantillas' => [
                'icon' => 'ti ti-template',
                'route' => 'admin.website-admin.cms.templates.index',
                'can' => 'admin.website-admin.cms.templates.view',
                'description' => 'Gestiona y asigna plantillas de presentación.',
            ],
        ],
    ],
    'Integraciones API' => [
        '_meta' => [
            'icon' => 'ti ti-plug',
            'description' => 'Configura APIs y servicios externos como Analytics o Chat.',
            'widget_label' => 'Extensiones y APIs',
            'home_at_root' => true,
            'priority' => 400,
        ],
        'submenu' => [
            'Google & Meta' => [
                'icon' => 'ti ti-brand-google',
                'route' => 'admin.website-admin.integrations.analytics.index',
                'can' => 'admin.website-admin.integrations.analytics.view',
                'description' => 'Google Analytics, Search Console, Pixel Meta, etc.',
            ],
            'Chat y comunicación' => [
                'icon' => 'ti ti-message-dots',
                'route' => 'admin.website-admin.integrations.chat.index',
                'can' => 'admin.website-admin.integrations.chat.view',
                'description' => 'Messenger, WhatsApp, Tawk.to y más.',
            ],
            'Traducción e idioma' => [
                'icon' => 'ti ti-language',
                'route' => 'admin.website-admin.integrations.translate.index',
                'can' => 'admin.website-admin.integrations.translate.view',
                'description' => 'Integraciones como Google Translate o DeepL.',
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
    'Blog' => [
        '_meta' => [
            'icon' => 'ti ti-news',
            'description' => 'Publica, edita y organiza artículos, categorías y comentarios de tu blog.',
            'after_to' => 'Web & SEO',
        ],
        'submenu' => [
            'Categorias' => [
                'icon' => 'ti ti-category',
                'route' => 'admin.website-admin.blog.categories.index',
                'can' => 'admin.website-admin.blog.categories.view',
            ],
            'Etiquetas' => [
                'icon' => 'ti ti-tags',
                'route' => 'admin.website-admin.blog.tags.index',
                'can' => 'admin.website-admin.blog.tags.view',
            ],
            'Articulos' => [
                'icon' => 'ti ti-news',
                'route' => 'admin.website-admin.blog.articles.index',
                'can' => 'admin.website-admin.blog.articles.view',
            ],
            'Comentarios' => [
                'icon' => 'ti ti-message',
                'route' => 'admin.website-admin.blog.comments.index',
                'can' => 'admin.website-admin.blog.comments.view',
            ],
        ]
    ],
];
