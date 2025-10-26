<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CalendarEventController extends Controller
{
    public function __invoke(): Response
    {
        Gate::authorize('viewAny', Event::class);

        $events = Event::with(['calendar:id,name'])
            ->forCurrentUser()
            ->get();

        return Inertia::render('Events/Calendar', [
            'events' => $events,
        ]);
    }
}
