<?php

namespace Koneko\KonekoWebsiteAdmin\Support\Traits\Context;

use Koneko\KonekoWebsiteAdmin\Application\Bootstrap\Context\SiteContext;
use Koneko\KonekoWebsiteAdmin\Models\WebsiteSite;

/**
 * 🧬 Trait para inyectar y resolver contexto de sitio activo
 */
trait HasSiteContext
{
    protected ?WebsiteSite $site = null;

    /**
     * Asigna sitio manualmente
     */
    public function setSite(WebsiteSite $site): static
    {
        $this->site = $site;
        return $this;
    }

    /**
     * Devuelve el sitio activo desde el contexto global o el asignado
     */
    public function getSite(): ?WebsiteSite
    {
        return $this->site ?? SiteContext::resolve();
    }

    /**
     * Shortcut para obtener el ID del sitio (o null)
     */
    public function getSiteId(): ?int
    {
        return $this->getSite()?->id;
    }

    /**
     * Shortcut para inyectar contexto en settings o servicios
     */
    public function applySiteScopeToSettings(): static
    {
        if ($site = $this->getSite()) {
            settings()->scope('site', $site->id);
        }

        return $this;
    }

    public function applySiteScopeToCache(): static
    {
        if ($site = $this->getSite()) {
            cache_m()->scope('site', $site->id);
        }

        return $this;
    }
}
