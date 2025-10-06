<?php

namespace App\Actions;

use App\Builders\EventBuilder;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class GetDashboardEventStats
{
    private User $user;

    /**
     * @return array{
     *     totalEvents: int,
     *     eventsToday: int,
     *     eventsTomorrow: int,
     *     eventsThisWeek: int,
     *     eventsNextWeek: int,
     *     averagePerDayOfWeek: array<int>
     * }
     */
    public function execute(User $user): array
    {
        $this->user = $user;
        $today = CarbonImmutable::now()->startOfDay();

        return [
            'totalEvents' => $this->getTotalEvents(),
            'eventsToday' => $this->getEventsForDate($today),
            'eventsTomorrow' => $this->getEventsForDate($today->addDay(), $today->addDay()->endOfDay()),
            'eventsThisWeek' => $this->getEventsBetween($today->startOfWeek(), $today->endOfWeek()),
            'eventsNextWeek' => $this->getEventsBetween($today->addWeek()->startOfWeek(), $today->addWeek()->endOfWeek()),
            'averagePerDayOfWeek' => $this->getAveragePerDayOfWeek(),
        ];
    }

    private function getTotalEvents(): int
    {
        return $this->user->events()->count();
    }

    private function getEventsForDate(CarbonImmutable $date, ?CarbonImmutable $endDate = null): int
    {
        return $this->user->events()
            ->where(function ($query) use ($date, $endDate): void {
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
        return $this->user->events()
            ->whereBetween('starts_at', [$start, $end])
            ->count();
    }

    /**
     * @return list<int>
     */
    private function getAveragePerDayOfWeek(): array
    {
        $averagePerDayOfWeek = $this->user->events()
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
