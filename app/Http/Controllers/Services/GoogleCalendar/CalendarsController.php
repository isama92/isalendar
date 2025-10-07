<?php

namespace App\Http\Controllers\Services\GoogleCalendar;

use App\Data\CalendarData;
use App\Http\Requests\UpdateCalendarRequest;
use App\Services\GoogleCalendar\Actions\Calendars\GetCalendars;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class CalendarsController
{
    public function index(Request $request, GetCalendars $getCalendars): Response
    {
        $user = Auth::user();

        abort_if(is_null($user->calendar), SymfonyResponse::HTTP_FORBIDDEN);
        abort_unless(is_null($user->calendar->external_calendar_id), SymfonyResponse::HTTP_FORBIDDEN);

        $calendars = $getCalendars->execute($user->calendar);

        $request->session()->put('pick_calendar.calendars', $calendars);

        return Inertia::render('Services/GoogleCalendar/PickCalendar', [
            'calendars' => $calendars,
        ]);
    }

    public function update(UpdateCalendarRequest $request): RedirectResponse
    {
        $calendars = $request->session()->get('pick_calendar.calendars');

        $calendar = $calendars->where('id', $request->validated('calendar_id'))->sole();

        Auth::user()->calendar->update(CalendarData::from($calendar)->all());

        return redirect()->route('profile.edit')
            ->with('success', __('profile.google_calendar_connected'));
    }
}
