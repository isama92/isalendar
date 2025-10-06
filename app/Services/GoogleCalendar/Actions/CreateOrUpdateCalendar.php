<?php

namespace App\Services\GoogleCalendar\Actions;

use App\Data\CalendarData;
use App\Models\Calendar;
use App\Models\User;
use App\Services\GoogleCalendar\Data\GoogleUserData;
use Laravel\Socialite\Contracts\User as ProviderUser;
use Laravel\Socialite\Two\User as GoogleUser;

class CreateOrUpdateCalendar
{
    public function execute(User $user, ProviderUser|GoogleUser $providerUser): Calendar
    {
        $googleUserData = GoogleUserData::from(
            ensure($providerUser, GoogleUser::class)
        );

        return $user->calendar()->updateOrCreate(
            ['user_id' => $user->id],
            CalendarData::from($googleUserData)->all(),
        );
    }
}
