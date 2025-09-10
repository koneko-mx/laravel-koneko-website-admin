<?php

declare(strict_types=1);

// Este archivo **NO se registra como config**, es usado por VuexyMenuRegistry

return [
    'Web & SEO' => [
        '_meta' => [
            'icon' => 'ti ti-settings',
            'description' => 'Administra la configuración, contenido, integraciones y visibilidad de tu sitio web empresarial.',
            'widget_label' => 'Sitio Web y SEO',
            'priority' => 200,
        ],
        'submenu' => [
            'Configuración general' => [
                '_meta' => [
                    'icon' => 'ti ti-settings',
                    'description' => 'Ajustes generales del sitio, redes sociales y configuración de indexado.',
                    'widget_label' => 'Configuración del Sitio Web',
                    'home_at_root' => true,
                    'priority' => 100,
                ],
                'submenu' => [
                    'Ajustes generales' => [
                        'icon' => 'ti ti-settings',
                        'route' => 'admin.website-admin.settings.general.index',
                        'can' => 'admin.website-admin.settings.general.view',
                        'description' => 'Personaliza el título, favicon, y otros aspectos básicos del sitio.',
                    ],
                    'Enlaces sociales' => [
                        'icon' => 'ti ti-share',
                        'route' => 'admin.website-admin.settings.social.index',
                        'can' => 'admin.website-admin.settings.social.view',
                        'description' => 'Administra los enlaces y metadatos de redes sociales.',
                    ],
                    'Visibilidad en buscadores' => [
                        'icon' => 'ti ti-search',
                        'route' => 'admin.website-admin.settings.indexing.index',
                        'can' => 'admin.website-admin.settings.indexing.view',
                        'description' => 'Controla el indexado del sitio web por los motores de búsqueda.',
                    ],
                ]
            ],
            'Contacto' => [
                '_meta' => [
                    'icon' => 'ti ti-device-mobile-question',
                    'description' => 'Información y formularios de contacto del sitio.',
                    'widget_label' => 'Información de contacto',
                    'home_at_root' => true,
                    'priority' => 200,
                ],
                'submenu' => [
                    'Información de contacto' => [
                        'icon' => 'ti ti-device-mobile-message',
                        'route' => 'admin.website-admin.contact.info.index',
                        'can' => 'admin.website-admin.contact.info.view',
                        'description' => 'Dirección, teléfonos, correos y ubicación.',
                    ],
                    'Formulario de contacto' => [
                        'icon' => 'ti ti-mail-cog',
                        'route' => 'admin.website-admin.contact.form.index',
                        'can' => 'admin.website-admin.contact.form.view',
                        'description' => 'Configuración y campos del formulario de contacto.',
                    ],
                ]
            ],
            'Chat & Comunicación' => [
                '_meta' => [
                    'icon' => 'ti ti-message-dots',
                    'description' => 'Soporte al cliente y canales de comunicación directa.',
                    'widget_label' => 'Chat & Comunicación de Sitio Web',
                    'home_at_root' => true,
                    'priority' => 500,
                ],
                'submenu' => [
                    'Facebook Messenger' => [
                        'icon' => 'ti ti-brand-messenger',
                        'route' => 'admin.website-admin.comunication.messenger.index',
                        'can' => 'admin.website-admin.comunication.messenger.view',
                        'description' => 'Activa el chat de Messenger en tu sitio.',
                    ],
                    'Whatsapp Chat' => [
                        'icon' => 'ti ti-brand-whatsapp',
                        'route' => 'admin.website-admin.comunication.whatsapp.index',
                        'can' => 'admin.website-admin.comunication.whatsapp.view',
                        'description' => 'Integra un botón de chat directo con WhatsApp.',
                    ],
                    'Tawk.to' => [
                        'icon' => 'ti ti-message-dots',
                        'route' => 'admin.website-admin.comunication.tawk-to.index',
                        'can' => 'admin.website-admin.comunication.tawk-to.view',
                        'description' => 'Agrega soporte en vivo con Tawk.to.',
                    ],
                    'Twitter API' => [
                        'icon' => 'ti ti-brand-x',
                        'route' => 'admin.website-admin.comunication.twitter.index',
                        'can' => 'admin.website-admin.comunication.twitter.view',
                        'description' => 'Configura la integración con Twitter/X.',
                    ],
                ]
            ],
            'CMS - Gestor de Contenidos' => [
                '_meta' => [
                    'icon' => 'ti ti-layout-grid-add',
                    'description' => 'Crea, edita y publica contenido dinámico basado en Blade para tu sitio web.',
                    'widget_label' => 'Administrador de Contenidos CMS',
                    'priority' => 250,
                ],
                'submenu' => [
                    'Menús del sitio' => [
                        'icon' => 'ti ti-hierarchy-3',
                        'route' => 'admin.website-admin.cms.menus.index',
                        'can' => 'admin.website-admin.cms.menus.view',
                        'description' => 'Administra la estructura de navegación y menús dinámicos.',
                    ],
                    'Perfil SEO' => [
                        'icon' => 'ti ti-settings',
                        'route' => 'admin.website-admin.cms.seo.index',
                        'can' => 'admin.website-admin.cms.seo.view',
                        'description' => 'Configura las metas y parámetros SEO del sitio.',
                    ],
                    'Contenidos dinámicos' => [
                        'icon' => 'ti ti-file-text',
                        'route' => 'admin.website-admin.cms.contents.index',
                        'can' => 'admin.website-admin.cms.contents.view',
                        'description' => 'Administra páginas, bloques y parciales Blade de tu sitio.',
                    ],
                    'Versiones de contenido' => [
                        'icon' => 'ti ti-clock-edit',
                        'route' => 'admin.website-admin.cms.versions.index',
                        'can' => 'admin.website-admin.cms.versions.view',
                        'description' => 'Gestiona las versiones de contenido y restauraciones.',
                    ],
                    'Plantillas disponibles' => [
                        'icon' => 'ti ti-template',
                        'route' => 'admin.website-admin.cms.templates.index',
                        'can' => 'admin.website-admin.cms.templates.view',
                        'description' => 'Define y organiza los templates base disponibles.',
                    ],
                ],
            ],
            'Cache' => [
                '_meta' => [
                    'icon' => 'ti ti-cpu',
                    'description' => 'Administración y limpieza de caché para mejorar el rendimiento.',
                    'widget_label' => 'Cache de Sitio Web',
                    'home_at_root' => true,
                    'priority' => 900,
                ],
                'submenu' => [
                    'Cache HTML renderizado' => [
                        'icon' => 'ti ti-file-type-html',
                        'description' => 'Visualiza y limpia la caché de HTML completo del sitio web.',
                        'route' => 'admin.website-admin.cache.fullpage.index',
                        'can' => 'admin.website-admin.cache.fullpage.view',
                    ],
                    'Previsualizaciones firmadas' => [
                        'icon' => 'ti ti-lock-access',
                        'description' => 'URLs de vista previa y control de firma temporal.',
                        'route' => 'admin.website-admin.cache.signed-previews.index',
                        'can' => 'admin.website-admin.cache.signed-previews.view',
                    ],
                ]
            ],
            'Traducciones e internacional' => [
                '_meta' => [
                    'icon' => 'ti ti-language',
                    'description' => 'Herramientas para traducción automática del sitio.',
                    'widget_label' => 'Herramientas de traducción de Sitio Web',
                    'home_at_root' => true,
                    'priority' => 500,
                ],
                'submenu' => [
                    'Google Translate' => [
                        'icon' => 'ti ti-language',
                        'route' => 'admin.website-admin.translate.google.index',
                        'can' => 'admin.website-admin.translate.google.view',
                        'description' => 'Activa la traducción automática con Google Translate.',
                    ],
                ]
            ],
            'Contenido' => [
                '_meta' => [
                    'icon' => 'ti ti-hierarchy',
                    'description' => 'Maneja contenido informativo y visual.',
                    'widget_label' => 'Contenidos del Sitio Web',
                    'home_at_root' => true,
                    'priority' => 400,
                ],
                'submenu' => [
                    'Preguntas frecuentes' => [
                        'icon' => 'ti ti-bubble-text',
                        'route' => 'admin.website-admin.content.faq.index',
                        'can' => 'admin.website-admin.content.faq.view',
                        'description' => 'Administra las preguntas frecuentes del sitio.',
                    ],
                    'Galería de imágenes' => [
                        'icon' => 'ti ti-photo',
                        'route' => 'admin.website-admin.content.gallery.index',
                        'can' => 'admin.website-admin.content.gallery.view',
                        'description' => 'Agrega y organiza tus imágenes.',
                    ],
                    'Avisos legales' => [
                        'icon' => 'ti ti-file-text-shield',
                        'route' => 'admin.website-admin.content.legal.index',
                        'can' => 'admin.website-admin.content.legal.view',
                        'description' => 'Documentos como Términos, Aviso de Privacidad, etc.',
                    ],
                ]
            ],
            'Analítica y seguimiento' => [
                '_meta' => [
                    'icon' => 'ti ti-device-analytics',
                    'description' => 'Conecta tu sitio con herramientas de análisis y tracking.',
                    'widget_label' => 'Integraciones de analítica y seguimiento',
                    'home_at_root' => true,
                    'priority' => 300,
                ],
                'submenu' => [
                    'Google Analytics' => [
                        'icon' => 'ti ti-chart-scatter-3d',
                        'route' => 'admin.website-admin.analytics.google-analytics.index',
                        'can' => 'admin.website-admin.analytics.google-analytics.view',
                        'description' => 'Integra tu cuenta de Google Analytics.',
                    ],
                    'Google Tags' => [
                        'icon' => 'ti ti-tags',
                        'route' => 'admin.website-admin.analytics.google-tags.index',
                        'can' => 'admin.website-admin.analytics.google-tags.view',
                        'description' => 'Administra etiquetas de Google Tag Manager.',
                    ],
                    'Google Search Console' => [
                        'icon' => 'ti ti-search',
                        'route' => 'admin.website-admin.analytics.google-search-console.index',
                        'can' => 'admin.website-admin.analytics.google-search-console.view',
                        'description' => 'Verifica y gestiona tu sitio con Search Console.',
                    ],
                    'Pixel Meta' => [
                        'icon' => 'ti ti-device-analytics',
                        'route' => 'admin.website-admin.analytics.pixel-meta.index',
                        'can' => 'admin.website-admin.analytics.pixel-meta.view',
                        'description' => 'Integra el pixel de Meta (Facebook Ads).',
                    ],
                ]
            ],
            'Herramientas SEO' => [
                '_meta' => [
                    'icon' => 'ti ti-code-dots',
                    'description' => 'Utilidades para mejorar la visibilidad en buscadores.',
                    'widget_label' => 'Herramientas SEO y Metadatos',
                    'home_at_root' => true,
                    'priority' => 500,
                ],
                'submenu' => [
                    'Mapa del sitio' => [
                        'icon' => 'ti ti-hierarchy',
                        'route' => 'admin.website-admin.seo.sitemap.index',
                        'can' => 'admin.website-admin.seo.sitemap.view',
                        'description' => 'Genera y publica el sitemap.xml.',
                    ],
                    'Google JSON-LD' => [
                        'icon' => 'ti ti-code-dots',
                        'route' => 'admin.website-admin.seo.jsonld.index',
                        'can' => 'admin.website-admin.seo.jsonld.view',
                        'description' => 'Configura el esquema estructurado para Google.',
                    ],
                    'Robots.txt' => [
                        'icon' => 'ti ti-code',
                        'route' => 'admin.website-admin.seo.robots.index',
                        'can' => 'admin.website-admin.seo.robots.view',
                        'description' => 'Controla qué partes del sitio pueden ser indexadas.',
                    ],
                    'manifest.json' => [
                        'icon' => 'ti ti-file-code',
                        'route' => 'admin.website-admin.seo.manifest.index',
                        'can' => 'admin.website-admin.seo.manifest.view',
                        'description' => 'Configura la compatibilidad como PWA.',
                    ],
                    'Canonical URLs' => [
                        'icon' => 'ti ti-link',
                        'route' => 'admin.website-admin.seo.canonical.index',
                        'can' => 'admin.website-admin.seo.canonical.view',
                        'description' => 'Evita duplicidad de URLs con etiquetas canónicas.',
                    ],
                    'Preview Social Cards' => [
                        'icon' => 'ti ti-brand-facebook',
                        'route' => 'admin.website-admin.seo.social-cards.index',
                        'can' => 'admin.website-admin.seo.social-cards.view',
                        'description' => 'Define títulos, imágenes y previews sociales.',
                    ],
                ]
            ],
        ]
    ],
    'Publicaciones' => [
        '_meta' => [
            'icon' => 'ti ti-news',
            'description' => 'Publica, edita y organiza artículos, categorías y comentarios de tu blog.',
            'after_to' => 'Web & SEO',
        ],
        'submenu' => [
            'Apartados' => [
                'icon' => 'ti ti-category',
                'route' => 'admin.website-admin.blog.categories.index',
                'can' => 'admin.website-admin.blog.categories.view',
            ],
            'Categorias' => [
                'icon' => 'ti ti-list',
                'route' => 'admin.website-admin.blog.categories.index',
                'can' => 'admin.website-admin.blog.categories.view',
            ],
            'Publicaciones' => [
                'icon' => 'ti ti-news',
                'route' => 'admin.website-admin.blog.articles.index',
                'can' => 'admin.website-admin.blog.articles.view',
            ],
            'Etiquetas' => [
                'icon' => 'ti ti-tags',
                'route' => 'admin.website-admin.blog.tags.index',
                'can' => 'admin.website-admin.blog.tags.view',
            ],
            'Comentarios' => [
                'icon' => 'ti ti-message',
                'route' => 'admin.website-admin.blog.comments.index',
                'can' => 'admin.website-admin.blog.comments.view',
            ],
        ]
    ],

    'Plantillas' => [
        '_meta' => [
            'icon' => 'ti ti-template',
            'description' => 'Gestiona las plantillas disponibles para tu sitio web.',
            'after_to' => 'Blog',
        ],
        'submenu' => [
            'Plantillas' => [
                'icon' => 'ti ti-template',
                'route' => 'admin.website-admin.templates.index',
                'can' => 'admin.website-admin.templates.view',
            ],
        ]
    ],
];
