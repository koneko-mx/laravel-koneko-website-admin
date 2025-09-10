# 🧩 Laravel Vuexy Website Admin

<p align="center">
    <a href="https://koneko.mx" target="_blank">
        <img src="https://git.koneko.mx/Koneko-ST/koneko-st/raw/branch/main/logo-images/horizontal-05.png" width="400" alt="Koneko Soluciones Tecnológicas Logo">
    </a>
</p>

<p align="center">
    <a href="https://koneko.mx"><img src="https://img.shields.io/badge/Website-koneko.mx-blue" alt="Website"></a>
    <a href="https://packagist.org/packages/koneko/laravel-koneko-website-admin"><img src="https://img.shields.io/packagist/v/koneko/laravel-koneko-website-admin" alt="Latest Version"></a>
    <a href="https://packagist.org/packages/koneko/laravel-koneko-website-admin"><img src="https://img.shields.io/packagist/l/koneko/laravel-koneko-website-admin" alt="License"></a>
    <a href="https://github.com/koneko-mx/laravel-koneko-website-admin"><img src="https://img.shields.io/github/issues/koneko-mx/laravel-koneko-website-admin" alt="Issues"></a>
</p>

---

## 📌 Description

**Laravel Vuexy Website Admin** is a core plugin in the **Koneko ERP** ecosystem. Built on Laravel 11 and integrated with the official `laravel-koneko-vuexy-admin` system, this package provides a powerful content management interface for corporate and eCommerce websites.

It enables complete control over site configuration, SEO optimization, domain management, multilingual support, and template rendering. The module follows modern coding standards and is compatible with Redis, PostgreSQL, and multi-site deployments.

---

### 📦 Main Features

* Multi-site and multi-domain support
* Site-level settings and branding configuration
* SEO profile management with OpenGraph, Twitter Cards, JSON-LD, and canonical settings
* Sitemap, robots.txt, and manifest.json integration
* CMS with dynamic blocks, reusable menus, and content versioning
* Full page and block-based caching system
* Template selector and preview system
* Blog module with categories, tags, articles, and comment moderation
* API integration for Google, Meta, WhatsApp, Tawk.to, and more
* Translation and localization tools (DeepL, Google Translate)

---

### 🚀 Quick Installation

```bash
composer require koneko/laravel-koneko-website-admin
php artisan migrate --seed
php artisan vendor:publish --tag=koneko-website-admin-config
```

> You must have `laravel-koneko-vuexy-admin` installed before using this plugin.

---

### 📦 Included Commands

```bash
php artisan website:seo-helper
php artisan website:menu-helper
php artisan website:content-helper
php artisan website:cache-helper
php artisan website:sitemap-generate
```
---

### ⚙️ Publicación de archivos

This package publishes:

* Routes, views and Livewire components
* Configuration and permissions files (RBAC)
* Artisan commands and content generators
* Menu and modular system extensions

```bash
php artisan vendor:publish --tag=koneko-website-admin-config
```

---

### 🔧 Uso y personalización

This module allows you to customize its structure and behavior using:

* Middlewares for web and content context
* Multisite configuration with support for dynamic templates
* Decoupled view system compatible with Blade, Vite and partial rendering

You can extend or override any published view, layout or Livewire component.

---

### 🛠️ Requirements

* PHP `^8.2`
* Laravel `^11.31`
* [koneko/laravel-koneko-vuexy-admin](https://github.com/koneko-mx/laravel-koneko-vuexy-admin) install and configured

---

## 📄 License

This package is licensed under the [custom Business Source License 1.1](LICENSE), transitioning to MIT after 3 years. For commercial usage, redistribution, or extended usage, please contact:

📧 [opensource@koneko.mx](mailto:opensource@koneko.mx)

---

## 📚 More Information

* [Vuexy Admin Core](https://github.com/koneko-mx/laravel-koneko-vuexy-admin)
* [Documentation in Spanish](README.md)
* [Koneko ST Official Website](https://koneko.mx)
* [Contact Email](mailto:opensource@koneko.mx)

---

<p align="center">
    Made with ❤️ in Mexico by <a href="https://koneko.mx">Koneko Soluciones Tecnológicas</a>
</p>
