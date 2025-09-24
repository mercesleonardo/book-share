<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN     = 'admin';
    case MODERATOR = 'moderator';
    case USER      = 'user';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN     => 'Administrator',
            self::MODERATOR => 'Moderador',
            self::USER      => 'Usuario',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ADMIN     => 'font-semibold text-red-600 dark:text-red-400',
            self::MODERATOR => 'font-semibold text-yellow-600 dark:text-yellow-400',
            self::USER      => 'font-semibold text-blue-600 dark:text-blue-400',
        };
    }
}
