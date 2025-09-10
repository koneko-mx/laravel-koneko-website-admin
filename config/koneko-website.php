<?php
// Variables
return [
    // ================== 📦 CACHE DE COMPONENTE ==================
    'cache' => [
        'enabled' => (bool) env('KONEKO_WEBSITE_CACHE_ENABLED', true),
        'ttl'     => (int) env('KONEKO_WEBSITE_CACHE_TTL', 20 * 24 * 60),
    ],
    'menu' => [
        'cache' => [
            'enabled' => (bool) env('VUEXY_WEBSITE_MENU_CACHE_ENABLED', true),
            'ttl'     => (int) env('VUEXY_WEBSITE_MENU_CACHE_TTL', 3600),
        ],
        'debug' => [
            'show_broken_routes'   => (bool) env('VUEXY_WEBSITE_MENU_DEBUG_SHOW_BROKEN_ROUTES', false),
            'show_disallowed_links' => (bool) env('VUEXY_WEBSITE_MENU_DEBUG_SHOW_DISALLOWED_LINKS', false),
        ],
    ],
    'html' => [
        'cache' => [
            'enabled' => (bool) env('VUEXY_WEBSITE_HTML_CACHE_ENABLED', true),
            'ttl'     => (int) env('VUEXY_WEBSITE_HTML_CACHE_TTL', 900),
        ],
        'debug' => [
            'mode' => (bool) env('VUEXY_WEBSITE_HTML_DEBUG_MODE', true),
        ]
    ]
];
