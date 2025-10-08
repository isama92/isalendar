<?php

namespace App\Http\Controllers\Web;

use App\Actions\GetDashboardEventStats;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly GetDashboardEventStats $getDashboardEventStats,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Dashboard', $this->getDashboardEventStats->execute());
    }
}
