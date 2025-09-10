<?php

namespace Koneko\KonekoWebsiteAdmin\Application\Enums\WebsiteMenuItem;

enum WebsiteMenuItemTarget: string
{
    case _self = '_self';
    case _blank = '_blank';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}