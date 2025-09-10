<?php

declare(strict_types=1);

namespace Koneko\KonekoWebsiteAdmin\Application\Enums\WebsiteSeoProfile;

enum WebsiteSeoProfileScope: string
{
    case Site    = 'site';
    case Content = 'content';
}
