<?php

namespace App\Http\Controllers\Web;

use App\Actions\GetOrderedTimezones;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventTemplateRequest;
use App\Http\Requests\UpdateEventTemplateRequest;
use App\Models\Calendar;
use App\Models\EventTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class EventTemplatesController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', EventTemplate::class);

        $eventTemplates = EventTemplate::with(['calendar:id,name'])
            ->forCurrentUser()
            ->get();

        return Inertia::render('EventTemplates/Index', [
            'eventTemplates' => $eventTemplates,
            'calendars' => Calendar::forCurrentUser()->enabled()->get(['id', 'name']),
        ]);
    }

    public function show(EventTemplate $eventTemplate): Response
    {
        Gate::authorize('view', $eventTemplate);

        $eventTemplate->loadMissing('calendar:id,name');

        return Inertia::render('EventTemplates/Show', [
            'eventTemplate' => $eventTemplate,
        ]);
    }

    public function create(GetOrderedTimezones $getOrderedTimezones): Response
    {
        Gate::authorize('create', EventTemplate::class);

        return Inertia::render('EventTemplates/Form', [
            'calendars' => $this->getCalendarsForSelect(),
            'timezones' => $getOrderedTimezones->execute(),
        ]);
    }

    public function store(StoreEventTemplateRequest $request): RedirectResponse
    {
        $eventTemplate = Auth::user()->eventTemplates()->create($request->validated());

        $redirectTo = $request->input('redirect_to', 'index');

        $route = match ($redirectTo) {
            'new' => route('event-templates.create'),
            'show' => route('event-templates.show', $eventTemplate),
            default => route('event-templates.index'),
        };

        return redirect($route)
            ->with('success', __('actions.named.created', ['name' => __('event-templates.event_template')]));
    }

    public function edit(GetOrderedTimezones $getOrderedTimezones, EventTemplate $eventTemplate): Response
    {
        Gate::authorize('update', $eventTemplate);

        return Inertia::render('EventTemplates/Form', [
            'eventTemplate' => $eventTemplate,
            'calendars' => $this->getCalendarsForSelect(),
            'timezones' => $getOrderedTimezones->execute(),
        ]);
    }

    public function update(UpdateEventTemplateRequest $request, EventTemplate $eventTemplate): RedirectResponse
    {
        $eventTemplate->update($request->validated());

        $redirectTo = $request->input('redirect_to', 'show');

        $route = match ($redirectTo) {
            'edit' => route('event-templates.edit', $eventTemplate),
            'index' => route('event-templates.index'),
            default => route('event-templates.show', $eventTemplate),
        };

        return redirect($route)
            ->with('success', __('actions.named.updated', ['name' => __('event-templates.event_template')]));
    }

    public function destroy(EventTemplate $eventTemplate): RedirectResponse
    {
        Gate::authorize('delete', $eventTemplate);

        $eventTemplate->delete();

        return redirect()->route('event-templates.index')
            ->with('success', __('actions.named.deleted', ['name' => __('event-templates.event_template')]));
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
