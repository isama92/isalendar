<?php

namespace App\Http\Controllers\Services\GoogleCalendar;

use Illuminate\Http\RedirectResponse;

class CalendarsController
{
    public function index()
    {
        // TODO: show calendars list, user selects one
    }

    public function update(): RedirectResponse
    {
        // TODO: save selected calendar

        return redirect()->route('profile.edit')
            ->with('success', __('profile.google_calendar_connected'));
    }
}
