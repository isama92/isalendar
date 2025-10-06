<?php

namespace App\Http\Controllers;

use App\Actions\GetDashboardEventStats;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly GetDashboardEventStats $getDashboardEventStats,
    ) {}

    public function index(): Response
    {
        [
            'totalEvents' => $totalEvents,
            'eventsToday' => $eventsToday,
            'eventsTomorrow' => $eventsTomorrow,
            'eventsThisWeek' => $eventsThisWeek,
            'eventsNextWeek' => $eventsNextWeek,
            'averagePerDayOfWeek' => $averagePerDayOfWeek,
        ] = $this->getDashboardEventStats->execute(auth()->user());

        return Inertia::render('Dashboard', [
            'totalEvents' => $totalEvents,
            'eventsToday' => $eventsToday,
            'eventsTomorrow' => $eventsTomorrow,
            'eventsThisWeek' => $eventsThisWeek,
            'eventsNextWeek' => $eventsNextWeek,
            'averagePerDayOfWeek' => $averagePerDayOfWeek,
        ]);
    }
}
