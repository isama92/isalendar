<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $locale = $request->input('locale');

        if (! in_array($locale, ['en', 'it'])) {
            $locale = 'en';
        }

        session(['locale' => $locale]);

        return back();
    }
}
