<?php

declare(strict_types=1);

namespace App\Services\Calendar\GoogleCalendar\Auth;

use App\Contracts\AccessToken as AccessTokenContract;

class AccessToken implements AccessTokenContract
{
    public function __construct(
        public readonly string $id,
        public readonly string $token,
        public readonly string $refreshToken,
        public readonly int $expiresIn,
    ) {}
}
