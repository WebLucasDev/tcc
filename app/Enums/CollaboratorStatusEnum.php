<?php

namespace App\Enums;

enum CollaboratorStatusEnum: string
{
    case ACTIVE = 'ativo';
    case INACTIVE = 'inativo';

    /**
     * Get all possible values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get label for display
     */
    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Ativo',
            self::INACTIVE => 'Inativo',
        };
    }

    /**
     * Get CSS class for status badge
     */
    public function badgeClass(): string
    {
        return match($this) {
            self::ACTIVE => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            self::INACTIVE => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        };
    }

    /**
     * Get icon for status
     */
    public function icon(): string
    {
        return match($this) {
            self::ACTIVE => 'fa-check-circle',
            self::INACTIVE => 'fa-times-circle',
        };
    }
}
