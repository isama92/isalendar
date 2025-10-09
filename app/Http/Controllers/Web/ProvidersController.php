<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\StoreProviderRequest;
use App\Models\Calendar;
use App\Models\Connector;
use App\Models\Provider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ProvidersController
{
    public function create(): Response
    {
        Gate::authorize('create', Calendar::class);

        $providers = Provider::query()
            ->enabled()
            ->orderBy('name')
            ->get(['id', 'name', 'image_path']);

        return Inertia::render('Providers/Create', [
            'providers' => $providers,
        ]);
    }

    public function store(StoreProviderRequest $request): RedirectResponse
    {
        $provider = Provider::findOrFail($request->validated('provider_id'));

        abort_unless($provider->enabled, SymfonyResponse::HTTP_NOT_FOUND);

        $connector = Auth::user()->connectors()
            ->whereBelongsTo($provider)
            ->first();

        if ($connector instanceof Connector) {
            return redirect()->route('calendars.create', $connector->getRouteKey());
        }

        return redirect()->route($provider->auth_route);
    }
}
