<?php

namespace App\Services\GoogleCalendar\Actions\Auth;

use App\Providers\Services\GoogleServiceProvider;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;

class RequestAccess
{
    public function execute(): RedirectResponse
    {
        return ensure(Socialite::driver('google'), GoogleProvider::class)
            ->scopes(GoogleServiceProvider::ACCESS_SCOPES)
            ->with(GoogleServiceProvider::ACCESS_PARAMS)
            ->redirect();
    }
}
