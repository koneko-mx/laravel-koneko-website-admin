<?php

namespace Koneko\KonekoWebsiteAdmin\Website\Traits\Cache;

use Illuminate\Support\Facades\Cache;

trait HasRenderCache
{
    public function hasRenderCache(): bool
    {
        return Cache::has("rendered_html.{$this->slug}");
    }

    public function getRenderCache(): ?string
    {
        return Cache::get("rendered_html.{$this->slug}");
    }

    public function setRenderCache(string $html): void
    {
        Cache::tags(["rendered_html", "website", "website_{$this->slug}"])->put("rendered_html.{$this->slug}", $html);
    }

    public function clearRenderCache(): void
    {
        Cache::tags(["rendered_html", "website", "website_{$this->slug}"])->forget("rendered_html.{$this->slug}");
    }
}
