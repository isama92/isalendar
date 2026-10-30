<?php

declare(strict_types=1);

namespace App\Enums;

enum Role: string
{
    case USER = 'user';
    case SUPER_ADMIN = 'super_admin';

    public function label(): string
    {
        return match ($this) {
            self::USER => 'User',
            self::SUPER_ADMIN => 'Super Admin',
        };
    }
}
