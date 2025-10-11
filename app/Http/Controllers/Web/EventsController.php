<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Calendar;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
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
            'calendar.connectors' => fn ($query) => $query
                ->select('connectors.id', 'provider_id', 'user_id')
                ->whereBelongsTo(Auth::user()),
            'calendar.connectors.provider:id,name',
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

        $event->loadMissing('calendar:id,name');

        return Inertia::render('Events/Show', [
            'event' => $event,
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('create', Event::class);

        $calendars = Calendar::query()
            ->forCurrentUser(true)
            ->select('id', 'name')
            ->get();

        return Inertia::render('Events/Form', [
            'calendars' => $calendars,
        ]);
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        Event::create($request->validated());

        return redirect()->route('events.index')
            ->with('success', __('events.event_created'));
    }

    public function edit(Event $event): Response
    {
        Gate::authorize('update', $event);

        $event->loadMissing('calendar:id');

        $calendars = Calendar::query()
            ->forCurrentUser(true)
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

        return redirect()->route('events.index')
            ->with('success', __('events.event_updated'));
    }

    public function destroy(Event $event): RedirectResponse
    {
        Gate::authorize('delete', $event);

        $event->delete();

        return redirect()->route('events.index')
            ->with('success', __('events.event_deleted'));
    }
}
