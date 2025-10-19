<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfSetupNeeded
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! User::withTrashed()->exists() && ! $request->routeIs('setup', 'setup.*', 'locale.update')) {
            return redirect()->route('setup');
        }

        return $next($request);
    }
}
