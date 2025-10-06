<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User;

class GoogleCalendarController extends Controller
{
    public function redirect(): RedirectResponse
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

    public function callback(): RedirectResponse
    {
        $googleUser = ensure(Socialite::driver('google')->user(), User::class);

        $user = Auth::user();

        $user->calendar()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'external_user_id' => $googleUser->id,
                'external_calendar_id' => 'primary',
                'external_token' => $googleUser->token,
                'external_refresh_token' => $googleUser->refreshToken,
                'external_token_expires_at' => now()->addSeconds($googleUser->expiresIn),
            ]
        );

        return redirect()->route('profile.edit')->with('success', __('profile.google_calendar_connected'));
    }

    // TODO: add select calendar

    public function disconnect(): RedirectResponse
    {
        $user = Auth::user();

        $user->calendar()->delete();

        return redirect()->route('profile.edit')->with('success', __('profile.google_calendar_disconnected'));
    }
}
