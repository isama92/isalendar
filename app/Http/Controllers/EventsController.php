<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class EventsController extends Controller
{
    public function index()
    {
        return Inertia::render('Events/Index', [
            'title' => 'test',
        ]);
    }
}
