<?php

namespace App\Services\GoogleCalendar\Actions\Auth;

use App\Enums\Service;
use App\Models\Connector;
use App\Models\User;
use App\Services\GoogleCalendar\Auth\AccessToken;
use Laravel\Socialite\Contracts\User as ProviderUser;
use Laravel\Socialite\Two\User as GoogleUser;

class SaveCredentials
{
    public function execute(User $user, ProviderUser|GoogleUser $providerUser): Connector
    {
        $googleUser = ensure($providerUser, GoogleUser::class);

        return $user->connectors()->create([
            'external_id' => $googleUser->getId(),
            'provider_id' => Service::GOOGLE_CALENDAR->id(),
            'access_details' => new AccessToken(
                $googleUser->getId(),
                $googleUser->token,
                $googleUser->refreshToken,
                $googleUser->expiresIn,
            ),
            'enabled' => false,
        ]);
    }
}
