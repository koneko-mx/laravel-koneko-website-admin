<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Providers;

use Illuminate\Support\ServiceProvider;
use Koneko\KonekoVuexyAdmin\Support\Traits\Modules\KonekoModuleBoots;

class KonekoWebsiteAdminServiceProvider extends ServiceProvider
{
    use KonekoModuleBoots;

    public function register(): void
    {
        $this->registerKonekoModule(dirname(__DIR__));
    }
}
