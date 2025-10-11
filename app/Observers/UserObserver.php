<?php

namespace App\Observers;

use App\Actions\GetTimezoneFromIp;
use App\Models\User;

class UserObserver
{
    public function __construct(private readonly GetTimezoneFromIp $getTimezoneFromIp) {}

    public function created(User $user): void
    {
        $user->settings()->create([
            'default_timezone' => $this->getTimezoneFromIp->execute(request()->ip()),
        ]);
    }
}
