<?php

namespace App\Actions;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GetDashboardEventStats
{
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
        $now = Carbon::now();

        $dateRanges = $this->calculateDateRanges($now);

        return [
            'totalEvents' => $this->getTotalEvents($user),
            'eventsToday' => $this->getEventsForDate($user, $dateRanges['today']),
            'eventsTomorrow' => $this->getEventsForDate($user, $dateRanges['tomorrow'], $dateRanges['endOfTomorrow']),
            'eventsThisWeek' => $this->getEventsBetween($user, $dateRanges['thisWeekStart'], $dateRanges['thisWeekEnd']),
            'eventsNextWeek' => $this->getEventsBetween($user, $dateRanges['nextWeekStart'], $dateRanges['nextWeekEnd']),
            'averagePerDayOfWeek' => $this->getAveragePerDayOfWeek($user),
        ];
    }

    /**
     * @return array<string, Carbon>
     */
    private function calculateDateRanges(Carbon $now): array
    {
        $today = $now->copy()->startOfDay();
        $tomorrow = $now->copy()->addDay()->startOfDay();

        return [
            'today' => $today,
            'tomorrow' => $tomorrow,
            'endOfTomorrow' => $tomorrow->copy()->endOfDay(),
            'thisWeekStart' => $now->copy()->startOfWeek(),
            'thisWeekEnd' => $now->copy()->endOfWeek(),
            'nextWeekStart' => $now->copy()->addWeek()->startOfWeek(),
            'nextWeekEnd' => $now->copy()->addWeek()->endOfWeek(),
        ];
    }

    private function getTotalEvents(User $user): int
    {
        return $user->events()->count();
    }

    private function getEventsForDate(User $user, Carbon $date, ?Carbon $endDate = null): int
    {
        return $user->events()
            ->where(function ($query) use ($date, $endDate): void {
                $query->where('all_day', true)
                    ->whereDate('starts_at', $date)
                    ->orWhere(function ($q) use ($date, $endDate): void {
                        $q->where('all_day', false);

                        if ($endDate instanceof Carbon) {
                            $q->whereBetween('starts_at', [$date, $endDate]);

                            return;
                        }

                        $q->whereDate('starts_at', $date);
                    });
            })
            ->count();
    }

    private function getEventsBetween(User $user, Carbon $start, Carbon $end): int
    {
        return $user->events()
            ->whereBetween('starts_at', [$start, $end])
            ->count();
    }

    /**
     * @return array<int>
     */
    private function getAveragePerDayOfWeek(User $user): array
    {
        $averagePerDayOfWeek = $user->events()
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
