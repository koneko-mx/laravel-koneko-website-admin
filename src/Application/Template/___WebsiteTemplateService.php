<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\Services;

use Illuminate\Support\Facades\{Cache,Schema};
use Koneko\KonekoVuexyAdmin\Models\Setting;

/**
 * Servicio para gestionar la configuración y personalización del template del Website.
 *
 * Esta clase maneja las configuraciones del template del website, incluyendo variables
 * de personalización, logos, favicons y otras configuraciones de la interfaz.
 * Implementa un sistema de caché para optimizar el rendimiento.
 */
class ___WebsiteTemplateService
{
    /** @var int Tiempo de vida del caché en minutos (60 * 24 * 30 = 30 días) */
    protected $cacheTTL = 60 * 24 * 30;

    /** @var string Prefijo del caché */
    protected $cachePrefix = 'vuexy_website:';

    /**
     * Obtiene las variables del template del website.
     *
     * @param string $setting Clave de la configuración a obtener
     * @return array Array con las variables del template
     */
    public function getWebsiteVars(string $setting = ''): array
    {
       try {
            // Verifica si la base de datos está inicializada
            if (!Schema::hasTable('migrations')) {
                return $this->getDefaultWebsiteVars($setting);
            }

            $webVars = Cache::remember('website_settings', $this->cacheTTL, function () {
                $settings = Setting::withVirtualValue()
                    ->where(function ($query) {
                        $query->where('key', 'LIKE', 'website.%')
                            ->orWhere('key', 'LIKE', 'google.%')
                            ->orWhere('key', 'LIKE', 'chat.%');
                    })
                    ->pluck('value', 'key')
                    ->toArray();

                return $this->buildWebsiteVars($settings);
            });

            return $setting ? ($webVars[$setting] ?? []) : $webVars;

        } catch (\Exception $e) {
            // Manejo de excepciones: devolver valores predeterminados
            return $this->getDefaultWebsiteVars($setting);
        }
    }

    /**
     * Construye las variables del template del website.
     *
     * @param array $settings Array asociativo de configuraciones
     * @return array Array con las variables del template
     */
    private function buildWebsiteVars(array $settings): array
    {
        return [
            'title'       => $settings['website.title'] ?? config('_var.appTitle'),
            'author'      => config('_var.author'),
            'description' => $settings['website.description'] ?? config('_var.appDescription'),
            'favicon'     => $this->getFaviconPaths($settings),
            'app_name'    => $settings['website.app_name'] ?? config('_var.appName'),
            'image_logo'  => $this->getImageLogoPaths($settings),
            //'template'    => $this->getTemplateVars($settings),
            'google'      => $this->getGoogleVars($settings),
            'chat'        => $this->getChatVars($settings),
            'contact'     => $this->getContactVars(),
            'social'      => $this->getSocialVars(),
        ];
    }

    /**
     * Obtiene las variables del template del website por defecto.
     *
     * @param string $setting Clave de la configuración a obtener
     * @return array Array con las variables del template
     */
    private function getDefaultWebsiteVars(string $setting = ''): array
    {
        $defaultVars = [
            'title'       => config('_var.appTitle', 'Default Title'),
            'author'      => config('_var.author', 'Default Author'),
            'description' => config('_var.appDescription', 'Default Description'),
            'favicon'     => $this->getFaviconPaths([]),
            'image_logo'  => $this->getImageLogoPaths([]),
            //'template'    => $this->getTemplateVars([]),
            'google'      => $this->getGoogleVars([]),
            'chat'        => $this->getChatVars([]),
            'contact'     => [],
            'social'      => [],
        ];

        return $setting ? ($defaultVars[$setting] ?? []) : $defaultVars;
    }

    /**
     * Genera las rutas para los diferentes tamaños de favicon.
     *
     * @param array $settings Array asociativo de configuraciones
     * @return array Array con las rutas de los favicons en diferentes tamaños
     */
    private function getFaviconPaths(array $settings): array
    {
        $defaultFavicon = config('koneko.appFavicon');
        $namespace = $settings['website.favicon_ns'] ?? null;

        return [
            'namespace' => $namespace,
            '16x16'     => $namespace ? "{$namespace}_16x16.png" : $defaultFavicon,
            '76x76'     => $namespace ? "{$namespace}_76x76.png" : $defaultFavicon,
            '120x120'   => $namespace ? "{$namespace}_120x120.png" : $defaultFavicon,
            '152x152'   => $namespace ? "{$namespace}_152x152.png" : $defaultFavicon,
            '180x180'   => $namespace ? "{$namespace}_180x180.png" : $defaultFavicon,
            '192x192'   => $namespace ? "{$namespace}_192x192.png" : $defaultFavicon,
        ];
    }

    /**
     * Genera las rutas para los diferentes tamaños y versiones del logo.
     *
     * @param array $settings Array asociativo de configuraciones
     * @return array Array con las rutas de los logos en diferentes tamaños y modos
     */
    private function getImageLogoPaths(array $settings): array
    {
        $defaultLogo = config('koneko.appLogo');

        return [
            'small'       => $this->getImagePath($settings, 'website.image.logo_small', $defaultLogo),
            'medium'      => $this->getImagePath($settings, 'website.image.logo_medium', $defaultLogo),
            'large'       => $this->getImagePath($settings, 'website.image.logo', $defaultLogo),
            'small_dark'  => $this->getImagePath($settings, 'website.image.logo_small_dark', $defaultLogo),
            'medium_dark' => $this->getImagePath($settings, 'website.image.logo_medium_dark', $defaultLogo),
            'large_dark'  => $this->getImagePath($settings, 'website.image.logo_dark', $defaultLogo),
        ];
    }

    /**
     * Obtiene la ruta de una imagen específica desde las configuraciones.
     *
     * @param array $settings Array asociativo de configuraciones
     * @param string $key Clave de la configuración
     * @param string $default Valor predeterminado si no se encuentra la configuración
     * @return string Ruta de la imagen
     */
    private function getImagePath(array $settings, string $key, ?string $default = ''): string
    {
        return (string) ($settings[$key] ?? $default ?? '');
    }

    /*
    private function getTemplateVars(array $settings): array
    {
        return [
            'style_switcher' => (bool)($settings['website.tpl_style_switcher'] ?? false),
            'footer_text'    => $settings['website.tpl_footer_text'] ?? '',
        ];
    }
    */

    /**
     * Obtiene las variables de Google Analytics.
     *
     * @param array $settings Array asociativo de configuraciones
     * @return array Array con las variables de Google Analytics
     */
    private function getGoogleVars(array $settings): array
    {
        return [
            'analytics' => [
                'enabled' => (bool)($settings['google.analytics_enabled'] ?? false),
                'id'      => $settings['google.analytics_id'] ?? '',
            ]
        ];
    }

    /**
     * Obtiene las variables de chat.
     *
     * @param array $settings Array asociativo de configuraciones
     * @return array Array con las variables de chat
     */
    private function getChatVars(array $settings): array
    {
        return [
            'provider'         => $settings['chat.provider'] ?? '',
            'whatsapp_number'  => $settings['chat.whatsapp_number'] ?? '',
            'whatsapp_message' => $settings['chat.whatsapp_message'] ?? '',
        ];
    }

    /**
     * Obtiene las variables de contacto.
     *
     * @return array Array con las variables de contacto
     */
    public function getContactVars(): array
    {
        $settings = Setting::withVirtualValue()
            ->where('key', 'LIKE', 'contact.%')
            ->pluck('value', 'key')
            ->toArray();

        return [
            'phone_number' => isset($settings['contact.phone_number'])
                ? preg_replace('/\D/', '', $settings['contact.phone_number'])  // Elimina todo lo que no sea un número
                : '',
            'phone_number_text' => $settings['contact.phone_number'] ?? '',
            'phone_number_ext' => $settings['contact.phone_number_ext'] ?? '',
            'phone_number_2' => isset($settings['contact.phone_number_2'])
                ? preg_replace('/\D/', '', $settings['contact.phone_number_2'])  // Elimina todo lo que no sea un número
                : '',
            'phone_number_2_text' => $settings['contact.phone_number_2'] ?? '',
            'phone_number_2_ext' => $settings['contact.phone_number_2_ext'] ?? '',
            'email'     => $settings['contact.email'] ?? '',
            'direccion' => $settings['contact.direccion'] ?? '',
            'horario'   => $settings['contact.horario'] ?? '',
            'location' => [
                'lat' => $settings['contact.location.lat'] ?? '',
                'lng' => $settings['contact.location.lng'] ?? '',
            ],
            'form' => [
                'to_email' => $settings['contact.form.to_email'] ?? '',
                'to_email_cc' => $settings['contact.form.to_email_cc'] ?? '',
                'subject' => $settings['contact.form.subject'] ?? '',
                'submit_message' => $settings['contact.form.submit_message'] ?? '',
            ],
        ];
    }

    /**
     * Obtiene las variables de redes sociales.
     *
     * @return array Array con las variables de redes sociales
     */
    public function getSocialVars(): array
    {
        $social = Setting::withVirtualValue()
            ->where('key', 'LIKE', 'social.%')
            ->pluck('value', 'key')
            ->toArray();

        return [
            'whatsapp'         => $social['social.whatsapp'] ?? '',
            'whatsapp_message' => $social['social.whatsapp_message'] ?? '',
            'facebook'         => $social['social.facebook'] ?? '',
            'instagram'        => $social['social.instagram'] ?? '',
            'linkedin'         => $social['social.linkedin'] ?? '',
            'tiktok'           => $social['social.tiktok'] ?? '',
            'x_twitter'        => $social['social.x_twitter'] ?? '',
            'google'           => $social['social.google'] ?? '',
            'pinterest'        => $social['social.pinterest'] ?? '',
            'youtube'          => $social['social.youtube'] ?? '',
            'vimeo'            => $social['social.vimeo'] ?? '',
        ];
    }


    /**
     * Limpia el caché de las variables del website.
     *
     * @return void
     */
    public static function clearWebsiteVarsCache()
    {
        Cache::forget("website_settings");
    }

    /**
     * Obtiene las variables de legal notice.
     *
     * @param string $legalDocument Documento legal a obtener
     * @return array Array con las variables de legal notice
     */
    public function getLegalVars($legalDocument = false)
    {
        $legal = Setting::withVirtualValue()
            ->where('key', 'LIKE', 'legal_notice.%')
            ->pluck('value', 'key')
            ->toArray();

        $legalDocuments =  [
            'legal_notice.terminos_y_condiciones' => [
                'title' => 'Términos y condiciones',
                'enabled' => (bool)($legal['legal_notice.terminos_y_condiciones_enabled'] ?? false),
                'content' => $legal['legal_notice.terminos_y_condiciones_content'] ?? '',
            ],
            'legal_notice.aviso_de_privacidad' => [
                'title' => 'Aviso de privacidad',
                'enabled' => (bool)($legal['legal_notice.aviso_de_privacidad_enabled'] ?? false),
                'content' => $legal['legal_notice.aviso_de_privacidad_content'] ?? '',
            ],
            'legal_notice.politica_de_devoluciones' => [
                'title' => 'Política de devoluciones y reembolsos',
                'enabled' => (bool)($legal['legal_notice.politica_de_devoluciones_enabled'] ?? false),
                'content' => $legal['legal_notice.politica_de_devoluciones_content'] ?? '',
            ],
            'legal_notice.politica_de_envios' => [
                'title' => 'Política de envíos',
                'enabled' => (bool)($legal['legal_notice.politica_de_envios_enabled'] ?? false),
                'content' => $legal['legal_notice.politica_de_envios_content'] ?? '',
            ],
            'legal_notice.politica_de_cookies' => [
                'title' => 'Política de cookies',
                'enabled' => (bool)($legal['legal_notice.politica_de_cookies_enabled'] ?? false),
                'content' => $legal['legal_notice.politica_de_cookies_content'] ?? '',
            ],
            'legal_notice.autorizaciones_y_licencias' => [
                'title' => 'Autorizaciones y licencias',
                'enabled' => (bool)($legal['legal_notice.autorizaciones_y_licencias_enabled'] ?? false),
                'content' => $legal['legal_notice.autorizaciones_y_licencias_content'] ?? '',
            ],
            'legal_notice.informacion_comercial' => [
                'title' => 'Información comercial',
                'enabled' => (bool)($legal['legal_notice.informacion_comercial_enabled'] ?? false),
                'content' => $legal['legal_notice.informacion_comercial_content'] ?? '',
            ],
            'legal_notice.consentimiento_para_el_login_de_terceros' => [
                'title' => 'Consentimiento para el login de terceros',
                'enabled' => (bool)($legal['legal_notice.consentimiento_para_el_login_de_terceros_enabled'] ?? false),
                'content' => $legal['legal_notice.consentimiento_para_el_login_de_terceros_content'] ?? '',
            ],
            'legal_notice.leyendas_de_responsabilidad' => [
                'title' => 'Leyendas de responsabilidad',
                'enabled' => (bool)($legal['legal_notice.leyendas_de_responsabilidad_enabled'] ?? false),
                'content' => $legal['legal_notice.leyendas_de_responsabilidad_content'] ?? '',
            ],
        ];

        return $legalDocument
            ? $legalDocuments[$legalDocument]
            : $legalDocuments;
    }























































}
