<?php

namespace Koneko\KonekoWebsiteAdmin\Application\Enums\Websites;

enum WebsiteRobotsMode: string
{
    case Suspended = 'suspended';
    case Site      = 'site';
    case Content   = 'content';

    public function label(): string
    {
        return match ($this) {
            self::Suspended  => 'Sin indexación',
            self::Site       => 'Reglas del sitio',
            self::Content    => 'Reglas de contenidos',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Suspended  => 'warning',
            self::Site       => 'success',
            self::Content    => 'info',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->label(),
        ])->toArray();
    }
}
