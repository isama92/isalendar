<?php

namespace App\Http\Controllers\Web;

use App\Actions\GetOrderedTimezones;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Calendar;
use App\Models\Event;
use App\Models\Provider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class EventsController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', Event::class);

        $events = Event::with(['calendar:id,name'])
            ->forCurrentUser()
            ->get();

        return Inertia::render('Events/Index', [
            'events' => $events,
            'providers' => Provider::enabled()->get(['id', 'name']),
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

    public function create(GetOrderedTimezones $getOrderedTimezones): Response
    {
        Gate::authorize('create', Event::class);
        abort_if(Auth::user()->connectors()->doesntExist(), SymfonyResponse::HTTP_FORBIDDEN);

        return Inertia::render('Events/Form', [
            'calendars' => $this->getCalendarsForSelect(),
            'timezones' => $getOrderedTimezones->execute(),
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
            ->with('success', __('actions.named.created', ['name' => __('events.event')]));
    }

    public function edit(GetOrderedTimezones $getOrderedTimezones, Event $event): Response
    {
        Gate::authorize('update', $event);

        $event->loadMissing('calendar:id');

        return Inertia::render('Events/Form', [
            'event' => $event,
            'calendars' => $this->getCalendarsForSelect(),
            'timezones' => $getOrderedTimezones->execute(),
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
            ->with('success', __('actions.named.updated', ['name' => __('events.event')]));
    }

    public function destroy(Event $event): RedirectResponse
    {
        Gate::authorize('delete', $event);

        $this->deleteEvent($event);

        return redirect()->route('events.index')
            ->with('success', __('actions.named.deleted', ['name' => __('events.event')]));
    }

    private function deleteEvent(Event $event): void
    {
        if (is_null($event->external_id)) {
            $event->delete();

            return;
        }

        $event->update(['marked_for_deletion_at' => now()]);
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\Calendar>
     */
    private function getCalendarsForSelect(): Collection
    {
        return Calendar::forCurrentUser(true)
            ->select('id', 'name')
            ->get();
    }
}
