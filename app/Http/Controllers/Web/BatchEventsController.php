<?php

namespace App\Http\Controllers\Web;

use App\Actions\GetOrderedTimezones;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBatchEventRequest;
use App\Models\Calendar;
use App\Models\Event;
use App\Models\EventTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class BatchEventsController extends Controller
{
    public function create(GetOrderedTimezones $getOrderedTimezones): Response
    {
        Gate::authorize('create', Event::class);
        abort_if(Auth::user()->connectors()->doesntExist(), SymfonyResponse::HTTP_FORBIDDEN);

        return Inertia::render('Events/CreateBatch', [
            'calendars' => $this->getCalendarsForSelect(),
            'timezones' => $getOrderedTimezones->execute(),
            'eventTemplates' => $this->getEventTemplatesForSelect(),
        ]);
    }

    public function store(StoreBatchEventRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        $calendar = Calendar::forCurrentUser()
            ->findOrFail($validatedData['calendar_id']);

        /** @var array<int, array{starts_at: string, ends_at: null|string}> $dates */
        $dates = $validatedData['dates'];
        $dateEntries = collect($dates)
            ->each(fn (array $dateEntry) => $calendar
                ->events()
                ->create([
                    'title' => $validatedData['title'],
                    'all_day' => $validatedData['all_day'],
                    'starts_at' => $dateEntry['starts_at'],
                    'ends_at' => $dateEntry['ends_at'],
                    'timezone' => $validatedData['timezone'] ?? null,
                    'description' => $validatedData['description'] ?? null,
                    'created_by_connector_id' => $calendar->connector->id,
                ]));

        return redirect()->route('events.index')
            ->with('success', __('events.batch_created', ['count' => $dateEntries->count()]));
    }

    /**
     * @return Collection<int, Calendar>
     */
    private function getCalendarsForSelect(): Collection
    {
        return Calendar::forCurrentUser()
            ->select('id', 'name')
            ->get();
    }

    /**
     * @return Collection<int, EventTemplate>
     */
    private function getEventTemplatesForSelect(): Collection
    {
        return EventTemplate::query()
            ->forCurrentUser()
            ->with('calendar:id,name')
            ->get();
    }
}
