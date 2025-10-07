<?php

namespace App\Services\GoogleCalendar\Actions\Auth;

use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;

class RequestAccess
{
    public function execute(): RedirectResponse
    {
        return ensure(Socialite::driver('google'), GoogleProvider::class)
            ->scopes([
                'https://www.googleapis.com/auth/calendar',
                'https://www.googleapis.com/auth/calendar.events',
                'openid',
                'profile',
                'email',
            ])
            ->with([
                'access_type' => 'offline',
                'prompt' => 'consent',
            ])
            ->redirect();
    }
}
