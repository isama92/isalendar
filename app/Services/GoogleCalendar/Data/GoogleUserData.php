<?php

namespace App\Services\GoogleCalendar\Data;

use Carbon\Carbon;
use Laravel\Socialite\Two\User as GoogleUser;
use Spatie\LaravelData\Data;

class GoogleUserData extends Data
{
    public function __construct(
        public string $id,
        public string $token,
        public string $refreshToken,
        public int $expiresIn,
    ) {}

    public static function fromGoogleUser(GoogleUser $googleUser): self
    {
        return new self(
            id: $googleUser->getId(),
            token: $googleUser->token,
            refreshToken: $googleUser->refreshToken,
            expiresIn: $googleUser->expiresIn,
        );
    }

    public function expiresAt(): Carbon
    {
        return now()->addSeconds($this->expiresIn);
    }
}
