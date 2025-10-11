<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Calendar;
use App\Models\Event;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
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
            'calendar.connectors' => fn (BelongsToMany $query) => $query
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

        return Inertia::render('Events/Form', [
            'calendars' => $this->getCalendarsForSelect(),
        ]);
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $event = Event::create($request->validated());

        $redirectTo = $request->input('redirect_to', 'index');

        $route = match ($redirectTo) {
            'new' => route('events.create'),
            'show' => route('events.show', $event),
            default => route('events.index'),
        };

        return redirect($route)
            ->with('success', __('events.event_created'));
    }

    public function edit(Event $event): Response
    {
        Gate::authorize('update', $event);

        $event->loadMissing('calendar:id');

        return Inertia::render('Events/Form', [
            'event' => $event,
            'calendars' => $this->getCalendarsForSelect(),
        ]);
    }

    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $event->update($request->validated());

        $redirectTo = $request->input('redirect_to', 'show');

        $route = match ($redirectTo) {
            'edit' => route('events.edit', $event),
            'index' => route('events.index'),
            default => route('events.show', $event),
        };

        return redirect($route)
            ->with('success', __('events.event_updated'));
    }

    public function destroy(Event $event): RedirectResponse
    {
        Gate::authorize('delete', $event);

        $event->delete();

        return redirect()->route('events.index')
            ->with('success', __('events.event_deleted'));
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\Calendar>
     */
    private function getCalendarsForSelect(): Collection
    {
        return Calendar::with([
            'connectors' => fn (BelongsToMany $query) => $query
                ->select('connectors.id', 'provider_id', 'user_id')
                ->whereBelongsTo(Auth::user()),
            'connectors.provider:id,name',
        ])
            ->forCurrentUser(true)
            ->select('id', 'name')
            ->get();
    }
}
