<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Password;

class SendUserPasswordResetController extends Controller
{
    public function __invoke(User $user): RedirectResponse
    {
        Gate::authorize('view', $user);

        $status = Password::sendResetLink(
            ['email' => $user->email]
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', __('users.password_reset_sent'));
        }

        return back()->with('error', __($status));
    }
}
