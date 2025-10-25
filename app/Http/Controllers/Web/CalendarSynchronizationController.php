<?php

namespace App\Http\Controllers\Web;

use App\Actions\SynchronizeCalendar;
use App\Http\Controllers\Controller;
use App\Models\Calendar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class CalendarSynchronizationController extends Controller
{
    public function update(Calendar $calendar, SynchronizeCalendar $synchronizeCalendar): RedirectResponse
    {
        abort_unless($calendar->connectors()->where('user_id', Auth::id())->exists(), SymfonyResponse::HTTP_FORBIDDEN);
        abort_unless($calendar->enabled, SymfonyResponse::HTTP_NOT_FOUND);

        $synchronizeCalendar->execute($calendar);

        return redirect()->route('calendars.index')
            ->with('success', __('calendars.synchronization_started'));
    }
}
