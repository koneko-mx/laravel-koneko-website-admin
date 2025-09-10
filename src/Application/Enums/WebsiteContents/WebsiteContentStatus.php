<?php

namespace Koneko\KonekoWebsiteAdmin\Application\Enums\WebsiteContents;

enum WebsiteContentStatus: string
{
    case Draft     = 'draft';
    case Published = 'published';
    case Archived  = 'archived';
    case Deleted   = 'deleted';

    public function label(): string
    {
        return match($this) {
            self::Draft     => 'Borrador',
            self::Published => 'Publicado',
            self::Archived  => 'Archivado',
            self::Deleted   => 'Eliminado',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    // Visibles en UI (no aceptamos Deleted desde el formulario)
    public static function formValues(): array
    {
        return [self::Draft->value, self::Published->value, self::Archived->value];
    }

    public static function optionsForForm(): array
    {
        return [
            self::Draft->value     => self::Draft->label(),
            self::Published->value => self::Published->label(),
            self::Archived->value  => self::Archived->label(),
        ];
    }
}
