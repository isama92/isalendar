<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
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
        $events = Event::query()
            ->forCurrentUser()
            ->paginate(10);

        return Inertia::render('Events/Index', [
            'events' => $events,
        ]);
    }

    public function show(Event $event): Response
    {
        Gate::authorize('view', $event);

        return Inertia::render('Events/Show', [
            'event' => $event,
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('create', Event::class);

        return Inertia::render('Events/Form');
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $event = Auth::user()
            ->events()
            ->create($request->validated());

        if ($request->input('redirect_to_show')) {
            return redirect()->route('events.show', $event);
        }

        return redirect()->route('events.index');
    }

    public function edit(Event $event): Response
    {
        Gate::authorize('update', $event);

        return Inertia::render('Events/Form', [
            'event' => $event,
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
