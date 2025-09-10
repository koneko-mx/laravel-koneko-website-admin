<?php

namespace Koneko\KonekoWebsiteAdmin\Website\Cache;

use Illuminate\Contracts\Cache\Repository;

class RenderCacheInvalidator
{
    public function __construct(private Repository $cache)
    {
    }

    public function invalidate(): void
    {
        $this->cache->forget('website.render');
    }
}