<?php

namespace App\Http\Controllers\Services\GoogleCalendar;

use App\Http\Controllers\Controller;
use App\Models\Calendar;
use App\Services\GoogleCalendar\Actions\Auth\RequestAccess;
use App\Services\GoogleCalendar\Actions\Auth\SaveCredentials;
use App\Services\GoogleCalendar\Actions\Calendars\FetchNormalizedCalendars;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AuthController extends Controller
{
    public function create(RequestAccess $requestAccess): SymfonyResponse
    {
        Gate::authorize('create', Calendar::class);

        return Inertia::location(
            $requestAccess->execute()->getTargetUrl()
        );
    }

    public function store(SaveCredentials $saveGoogleUser, FetchNormalizedCalendars $getCalendars): RedirectResponse
    {
        Gate::authorize('create', Calendar::class);

        try {
            $googleCalendarUser = Socialite::driver('google')->user();
        } catch (InvalidStateException) {
            abort(SymfonyResponse::HTTP_FORBIDDEN);
        }

        $connector = $saveGoogleUser->execute(
            Auth::user(),
            $googleCalendarUser,
        );

        return redirect()->route('calendars.create', $connector->getRouteKey());
    }
}
