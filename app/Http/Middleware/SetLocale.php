<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class SetLocale
{
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $locale = session('locale', config('app.locale'));

        if (in_array($locale, ['en', 'it'])) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
