<?php

namespace Koneko\KonekoWebsiteAdmin\Application\Enums\WebsiteMenuItem;

enum WebsiteMenuItemType: string
{
    case Custom       = 'custom';
    case CmsPage      = 'cms_page';
    case BlogArticle  = 'blog_article';
    case BlogCategory = 'blog_category';
    case Action       = 'action';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
