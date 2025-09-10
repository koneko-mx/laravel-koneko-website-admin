<?php

namespace Koneko\KonekoWebsiteAdmin\Application\Enums\Websites;

enum WebsiteSiteStatus: string
{
    case ACTIVE      = 'active';
    case MAINTENANCE = 'maintenance';
    case COMING_SOON = 'coming_soon';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE      => 'Activo',
            self::MAINTENANCE => 'Mantenimiento',
            self::COMING_SOON => 'Próximamente',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE      => 'success',
            self::MAINTENANCE => 'warning',
            self::COMING_SOON => 'info',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->label(),
        ])->toArray();
    }
}
