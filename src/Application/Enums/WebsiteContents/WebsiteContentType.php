<?php

namespace Koneko\KonekoWebsiteAdmin\Application\Enums\WebsiteContents;

enum WebsiteContentType: string
{
    case Page        = 'page';
    case LandingPage = 'landing-page';
    case Gallery     = 'gallery';
    case Blog        = 'blog';

    public function label(): string
    {
        return match($this) {
            self::Page        => 'Page',
            self::LandingPage => 'LandingPage',
            self::Gallery     => 'Gallery',
            self::Blog        => 'Blog',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn ($c) => [$c->value => $c->label()]
        )->toArray();
    }
}
