<?php

use App\Console\Commands\SyncCalendars;
use Illuminate\Support\Facades\Schedule;

Schedule::command(SyncCalendars::class)
    ->hourly()
    ->timezone('Europe/Amsterdam')
    ->environments('production');
