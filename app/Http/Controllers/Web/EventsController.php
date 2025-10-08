<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Calendar;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class EventsController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', Event::class);

        // TODO: fix pagination
        $events = Event::with([
            'calendar:id,name',
            'calendar.connectors:id,provider_id,user_id',
            'calendar.connectors.provider:id,name',
            // TODO: filter connectors by user or every connector will be added in the response
        ])
            ->forCurrentUser()
            ->paginate(10);

        return Inertia::render('Events/Index', [
            'events' => $events,
        ]);
    }

    public function show(Event $event): Response
    {
        Gate::authorize('view', $event);
        // TODO: show calendar

        return Inertia::render('Events/Show', [
            'event' => $event,
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('create', Event::class);

        // TODO: danger - super admin can create events for other users
        $calendars = Calendar::query()
            ->forCurrentUser()
            ->select('id', 'name')
            ->get();

        return Inertia::render('Events/Form', [
            'calendars' => $calendars,
        ]);
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $event = Event::create($request->validated());

        if ($request->input('redirect_to_show')) {
            return redirect()->route('events.show', $event);
        }

        return redirect()->route('events.index');
    }

    public function edit(Event $event): Response
    {
        Gate::authorize('update', $event);

        $event->loadMissing('calendar:id');

        // TODO: danger - super admin can create events for other users
        $calendars = Calendar::query()
            ->forCurrentUser()
            ->select('id', 'name')
            ->get();

        return Inertia::render('Events/Form', [
            'event' => $event,
            'calendars' => $calendars,
        ]);
    }

    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $event->update($request->validated());

        if ($request->input('redirect_to_show')) {
            return redirect()->route('events.show', $event);
        }

        return redirect()->route('events.index');
    }

    public function destroy(Event $event): RedirectResponse
    {
        Gate::authorize('delete', $event);

        $event->delete();

        return redirect()->route('events.index');
    }
}
