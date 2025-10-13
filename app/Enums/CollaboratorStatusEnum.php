<?php

namespace App\Enums;

enum CollaboratorStatusEnum: string
{
    case ACTIVE = 'ativo';
    case INACTIVE = 'inativo';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Ativo',
            self::INACTIVE => 'Inativo',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::ACTIVE => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            self::INACTIVE => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::ACTIVE => 'fa-check-circle',
            self::INACTIVE => 'fa-times-circle',
        };
    }
}
