<?php

namespace App\Services\Calendar\GoogleCalendar\Actions\Auth;

use App\Enums\Service;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;

class RequestAccess
{
    public function execute(): RedirectResponse
    {
        return ensure(Socialite::driver('google'), GoogleProvider::class)
            ->scopes(Service::GOOGLE_CALENDAR->scopes())
            ->with(Service::GOOGLE_CALENDAR->accessParams())
            ->redirect();
    }
}
