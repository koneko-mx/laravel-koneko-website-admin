<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\Enums;

enum WebsiteContentType: string
{
    case Page      = 'page';
    case Partial   = 'partial';
    case Menu      = 'menu';
    case Component = 'component';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}