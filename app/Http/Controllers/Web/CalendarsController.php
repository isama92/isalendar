<?php

namespace App\Http\Controllers\Web;

use App\Actions\GetProviderCalendars;
use App\Data\CalendarData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCalendarRequest;
use App\Http\Requests\UpdateCalendarRequest;
use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class CalendarsController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', Calendar::class);

        // TODO: fix pagination
        $calendars = Calendar::with([
            'connectors' => fn (BelongsToMany $query) => $query
                ->select('connectors.id', 'provider_id', 'user_id')
                ->whereBelongsTo(Auth::user()),
            'connectors.provider:id,name',
        ])
            ->forCurrentUser()
            ->withCount('events')
            ->paginate(10);

        return Inertia::render('Calendars/Index', [
            'calendars' => $calendars,
            'providers' => Provider::enabled()->get(['id', 'name']),
        ]);
    }

    public function create(Request $request, Connector $connector, GetProviderCalendars $getProviderCalendars): Response
    {
        Gate::authorize('create', Calendar::class);
        abort_unless($connector->provider->enabled, SymfonyResponse::HTTP_NOT_FOUND);
        abort_unless($connector->user_id === Auth::id(), SymfonyResponse::HTTP_NOT_FOUND);

        // Filter out existing calendars
        $existingCalendarsExtIds = $connector->calendars()->pluck('external_id');
        $calendarDataCollection = $getProviderCalendars->execute($connector)
            ->filter(fn (CalendarData $calendarData) => $existingCalendarsExtIds
                ->doesntContain($calendarData->externalId))
            ->values();

        $request->session()->put('calendars.create.list', $calendarDataCollection);

        return Inertia::render('Calendars/Create', [
            'connectorRouteKey' => $connector->getRouteKey(),
            'provider' => $connector->provider->only(['id', 'name', 'image_path']),
            'calendars' => $calendarDataCollection,
        ]);
    }

    public function store(StoreCalendarRequest $request, Connector $connector): RedirectResponse
    {
        Gate::authorize('create', Calendar::class);
        abort_unless($connector->provider->enabled, SymfonyResponse::HTTP_NOT_FOUND);
        abort_unless($connector->user_id === Auth::id(), SymfonyResponse::HTTP_NOT_FOUND);

        /** @var \App\Data\CalendarData $calendarData */
        $calendarData = $request->session()->get('calendars.create.list')
            ->where('externalId', $request->validated('external_id'))
            ->sole();

        DB::transaction(function () use ($connector, $calendarData): void {
            $calendar = Calendar::withTrashed()
                ->updateOrCreate(
                    ['external_id' => $calendarData->externalId],
                    $calendarData->all()
                );
            $calendar->restoreQuietly();
            $connector->calendars()->attach($calendar);
            $connector->update(['enabled' => true]);
        });

        return redirect()->route('calendars.index')
            ->with('success', __('calendars.calendar_created'));
    }

    public function update(UpdateCalendarRequest $request, Calendar $calendar): RedirectResponse
    {
        $calendar->update($request->validated());

        return redirect()->route('calendars.index')
            ->with('success', $calendar->enabled
                ? __('calendars.calendar_enabled')
                : __('calendars.calendar_disabled'));
    }

    public function destroy(Calendar $calendar): RedirectResponse
    {
        Gate::authorize('delete', $calendar);

        $connector = $calendar->connectors()
            ->forCurrentUser()
            ->sole();

        $connector->calendars()->detach($calendar);

        // TODO: needs testing
        if (! $calendar->connectors()->exists()) {
            $calendar->delete();
        }

        if (! $connector->calendars()->count()) {
            $connector->delete();
        }

        return redirect()->route('calendars.index')
            ->with('success', __('calendars.calendar_deleted'));
    }
}
