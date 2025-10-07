<?php

namespace App\Http\Controllers\Services\GoogleCalendar;

use App\Http\Controllers\Controller;
use App\Services\GoogleCalendar\Actions\Auth\RequestAccess;
use App\Services\GoogleCalendar\Actions\Auth\SaveCredentials;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AuthController extends Controller
{
    public function redirect(RequestAccess $requestAccess): RedirectResponse
    {
        abort_if(Auth::user()->calendar()->exists(), SymfonyResponse::HTTP_FORBIDDEN);

        return $requestAccess->execute();
    }

    public function callback(SaveCredentials $saveGoogleUser): RedirectResponse
    {
        abort_if(Auth::user()->calendar()->exists(), SymfonyResponse::HTTP_FORBIDDEN);

        $saveGoogleUser->execute(
            Auth::user(),
            Socialite::driver('google')->user()
        );

        return redirect()->route('services.google.calendar.index');
    }

    public function disconnect(): RedirectResponse
    {
        $user = Auth::user();

        abort_if(is_null($user->calendar), SymfonyResponse::HTTP_FORBIDDEN);

        $user->calendar->delete();

        return redirect()->route('profile.edit')->with('success', __('profile.google_calendar_disconnected'));
    }
}
