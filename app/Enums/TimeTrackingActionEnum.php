<?php

namespace App\Enums;

enum TimeTrackingActionEnum: string
{
    case EDITED = 'edited';
    case CANCELLED = 'cancelled';
    case RESTORED = 'restored';

    public function label(): string
    {
        return match($this) {
            self::EDITED => 'Editado',
            self::CANCELLED => 'Cancelado',
            self::RESTORED => 'Restaurado',
        };
    }

    public static function getAll(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public function color(): string
    {
        return match($this) {
            self::EDITED => 'text-blue-600',
            self::CANCELLED => 'text-red-600',
            self::RESTORED => 'text-yellow-600',
        };
    }
}
