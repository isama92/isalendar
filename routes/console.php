<?php

declare(strict_types=1);

use App\Console\Commands\SynchronizeGoogleCalendarCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(SynchronizeGoogleCalendarCommand::class)
    ->everyTenMinutes()
    ->withoutOverlapping()
    ->timezone('Europe/Amsterdam')
    ->environments('production');
