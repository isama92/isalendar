<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventDurationSuggestionRequest;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class EventDurationSuggestionController extends Controller
{
    public function __invoke(EventDurationSuggestionRequest $request): array
    {
        $title = $request->input('title');
        $allDay = $request->boolean('all_day');

        return Event::query()
            ->forCurrentUser()
            ->where('title', 'LIKE', "%{$title}%")
            ->where('all_day', $allDay)
            ->whereNotNull('starts_at')
            ->whereNotNull('ends_at')
            ->select(
                DB::raw('TIMESTAMPDIFF(MINUTE, starts_at, ends_at) as duration_minutes'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('duration_minutes')
            ->orderByDesc('count')
            ->limit(3)
            ->pluck('duration_minutes')
            ->toArray();
    }
}
