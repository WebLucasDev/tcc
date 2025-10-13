<?php

namespace App\Enums;

enum WorkHoursStatusEnum: string
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
            self::ACTIVE => 'fa-solid fa-check-circle',
            self::INACTIVE => 'fa-solid fa-times-circle',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'green',
            self::INACTIVE => 'red',
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }
}
