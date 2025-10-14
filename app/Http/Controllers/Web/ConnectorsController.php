<?php

namespace App\Http\Controllers\Web;

use App\Actions\SynchronizeConnector;
use App\Http\Controllers\Controller;
use App\Models\Connector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ConnectorsController extends Controller
{
    public function update(Connector $connector, SynchronizeConnector $synchronizeConnector): RedirectResponse
    {
        abort_unless($connector->user_id === Auth::id(), SymfonyResponse::HTTP_FORBIDDEN);
        abort_unless($connector->enabled, SymfonyResponse::HTTP_NOT_FOUND);

        $synchronizeConnector->execute($connector);

        return redirect()->route('calendars.index')
            ->with('success', __('calendars.synchronization_started'));
    }
}
