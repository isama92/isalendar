<?php

namespace App\Actions;

use App\Builders\EventBuilder;
use App\Models\Calendar;
use App\Models\Event;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class GetDashboardEventStats
{
    /**
     * @return array{
     *     totalEvents: int,
     *     totalCalendars: int,
     *     eventsToday: int,
     *     eventsTomorrow: int,
     *     eventsThisWeek: int,
     *     eventsNextWeek: int,
     *     averagePerDayOfWeek: array<int>
     * }
     */
    public function execute(): array
    {
        $today = CarbonImmutable::now()->startOfDay();

        return [
            'totalEvents' => $this->getTotalEvents(),
            'totalCalendars' => $this->getTotalCalendars(),
            'eventsToday' => $this->getEventsForDate($today),
            'eventsTomorrow' => $this->getEventsForDate($today->addDay(), $today->addDay()->endOfDay()),
            'eventsThisWeek' => $this->getEventsBetween($today->startOfWeek(), $today->endOfWeek()),
            'eventsNextWeek' => $this->getEventsBetween($today->addWeek()->startOfWeek(), $today->addWeek()->endOfWeek()),
            'averagePerDayOfWeek' => $this->getAveragePerDayOfWeek(),
        ];
    }

    private function getTotalEvents(): int
    {
        return Event::forCurrentUser()->count();
    }

    private function getTotalCalendars(): int
    {
        return Calendar::forCurrentUser()->count();
    }

    private function getEventsForDate(CarbonImmutable $date, ?CarbonImmutable $endDate = null): int
    {
        return Event::forCurrentUser()
            ->where(function (EventBuilder $query) use ($date, $endDate): void {
                $query->where(function (EventBuilder $q) use ($date): void {
                    $q->where('all_day', true)
                        ->whereDate('starts_at', $date);
                })
                    ->orWhere(function (EventBuilder $q) use ($date, $endDate): void {
                        $q->where('all_day', false);

                        if (is_null($endDate)) {
                            $q->whereDate('starts_at', $date);

                            return;
                        }

                        $q->whereBetween('starts_at', [$date, $endDate]);
                    });
            })
            ->count();
    }

    private function getEventsBetween(CarbonImmutable $start, CarbonImmutable $end): int
    {
        return Event::forCurrentUser()
            ->whereBetween('starts_at', [$start, $end])
            ->count();
    }

    /**
     * @return list<int>
     */
    private function getAveragePerDayOfWeek(): array
    {
        $averagePerDayOfWeek = Event::forCurrentUser()
            ->select(DB::raw('DAYOFWEEK(starts_at) - 1 as day_of_week, COUNT(*) as count'))
            ->groupBy('day_of_week')
            ->pluck('count', 'day_of_week')
            ->toArray();

        $dayAverages = [];
        for ($i = 0; $i < 7; $i++) {
            $dayAverages[$i] = $averagePerDayOfWeek[$i] ?? 0;
        }

        return array_values($dayAverages);
    }
}
