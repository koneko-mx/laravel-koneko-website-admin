# 🧩 Laravel Vuexy Website Admin

<p align="center">
    <a href="https://koneko.mx" target="_blank">
        <img src="https://git.koneko.mx/Koneko-ST/koneko-st/raw/branch/main/logo-images/horizontal-05.png" width="400" alt="Koneko Soluciones Tecnológicas Logo">
    </a>
</p>

<p align="center">
    <a href="https://koneko.mx"><img src="https://img.shields.io/badge/Sitio%20Web-koneko.mx-blue" alt="Sitio Web"></a>
    <a href="https://packagist.org/packages/koneko/laravel-koneko-website-admin"><img src="https://img.shields.io/packagist/v/koneko/laravel-koneko-website-admin" alt="Versión estable"></a>
    <a href="https://packagist.org/packages/koneko/laravel-koneko-website-admin"><img src="https://img.shields.io/packagist/l/koneko/laravel-koneko-website-admin" alt="Licencia"></a>
    <a href="https://github.com/koneko-mx/laravel-koneko-website-admin"><img src="https://img.shields.io/github/issues/koneko-mx/laravel-koneko-website-admin" alt="Issues"></a>
</p>

---

## 📌 Descripción

**Laravel Vuexy Website Admin** es un módulo del ecosistema **Koneko ERP** desarrollado en Laravel 11, orientado a la administración de contenido web multisitio, multidioma y multitemplate. Forma parte del stack web profesional de Koneko, integrando funcionalidades CMS, SEO, blog, renderización por bloques, cache HTML y plantillas dinámicas.

Está diseñado para integrarse de forma transparente al backend Vuexy Admin y utilizarse con frontends basados en Vite y plantillas como **Porto, Landwind, Notus** y otras.

---

### 📦 Características

* Gestión de múltiples sitios web y dominios
* Configuración contextual del sitio (branding, idioma, indexación, manifest.json, etc.)
* Editor de contenido por bloques y plantillas Blade
* Administración de menús, páginas, SEO y JSON-LD
* Blog con artículos, etiquetas, categorías y comentarios
* Sistema de caché de contenido completo y por bloque
* Integraciones con APIs externas como Google Analytics, Pixel Meta, Tawk.to, etc.

---

### 🚀 Instalación rápida

```bash
composer require koneko/laravel-koneko-website-admin
php artisan migrate --seed
php artisan vendor:publish --tag=koneko-website-admin-config
```

> Debes tener instalado `laravel-koneko-vuexy-admin` antes de usar este complemento.

---

### 📦 Comandos Incluidos

```bash
php artisan website:seo-helper
php artisan website:menu-helper
php artisan website:content-helper
php artisan website:cache-helper
php artisan website:sitemap-generate
```

---

### ⚙️ Publicación de archivos

Este paquete publica:

* Rutas, vistas y Livewire components
* Archivos de configuración y permisos (RBAC)
* Comandos Artisan y generadores de contenido
* Extensiones para menú y sistema modular

```bash
php artisan vendor:publish --tag=koneko-website-admin-config
```

---

### 🔧 Uso y personalización

Este módulo permite personalizar su estructura y comportamiento utilizando:

* Middlewares para contexto web y contenido
* Configuración multisitio con soporte para templates dinámicos
* Sistema de vistas desacoplado compatible con Blade, Vite y renderizado parcial

Puedes extender o sobreescribir cualquier vista, layout o componente Livewire publicado.

---

## 🛠️ Requisitos

* PHP `^8.2`
* Laravel `^11.31`
* [koneko/laravel-koneko-vuexy-admin](https://github.com/koneko-mx/laravel-koneko-vuexy-admin) instalado y configurado

---

## 📄 Licencia

Este paquete se distribuye bajo la [Licencia Business Source 1.1 personalizada](LICENSE.es), con transición automática a MIT a los 3 años. Para uso comercial, redistribución o integraciones ampliadas, contacta a:

📧 [opensource@koneko.mx](mailto:opensource@koneko.mx)
    
---

## 📚 Más Información

* [Core Vuexy Admin](https://github.com/koneko-mx/laravel-koneko-vuexy-admin)
* [Documentación en inglés](README.en.md)
* [Sitio Oficial Koneko ST](https://koneko.mx)
* [Correo de Contacto](mailto:opensource@koneko.mx)

---

<p align="center">
    Hecho con ❤️ en México por <a href="https://koneko.mx">Koneko Soluciones Tecnológicas</a>
</p>
