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
}
