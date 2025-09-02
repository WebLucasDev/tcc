<?php

namespace App\Enums;

enum TimeTrackingStatusEnum: string
{
    case COMPLETO = 'completo';
    case INCOMPLETO = 'incompleto';
    case AUSENTE = 'ausente';

    public function label(): string
    {
        return match($this) {
            self::COMPLETO => 'Completo',
            self::INCOMPLETO => 'Incompleto',
            self::AUSENTE => 'Ausente',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::COMPLETO => 'success',
            self::INCOMPLETO => 'warning',
            self::AUSENTE => 'danger',
        };
    }

    public static function options(): array
    {
        return [
            self::COMPLETO->value => self::COMPLETO->label(),
            self::INCOMPLETO->value => self::INCOMPLETO->label(),
            self::AUSENTE->value => self::AUSENTE->label(),
        ];
    }
}
