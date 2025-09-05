<?php

namespace App\Enums;

enum TimeTrackingActionEnum: string
{
    case EDITED = 'edited';
    case CANCELLED = 'cancelled';
    case RESTORED = 'restored';

    /**
     * Retorna o nome amigável da ação
     */
    public function label(): string
    {
        return match($this) {
            self::EDITED => 'Editado',
            self::CANCELLED => 'Cancelado',
            self::RESTORED => 'Restaurado',
        };
    }

    /**
     * Retorna todas as ações disponíveis
     */
    public static function getAll(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    /**
     * Retorna a cor CSS para cada ação (para exibição)
     */
    public function color(): string
    {
        return match($this) {
            self::EDITED => 'text-blue-600',
            self::CANCELLED => 'text-red-600',
            self::RESTORED => 'text-yellow-600',
        };
    }
}
