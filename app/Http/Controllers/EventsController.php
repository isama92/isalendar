<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Inertia\Inertia;

class EventsController extends Controller
{
    public function index()
    {
        $events = Event::query()
            ->where('user_id', auth()->id())
            ->orderBy('starts_at')
            ->paginate(10);

        return Inertia::render('Events/Index', [
            'events' => $events,
        ]);
    }
}
