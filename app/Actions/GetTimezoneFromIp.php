<?php

namespace App\Actions;

use Torann\GeoIP\Facades\GeoIP;

class GetTimezoneFromIp
{
    public function execute(?string $ip): string
    {
        $geo = GeoIP::getLocation($ip);

        return $geo->time_zone['name']
            ?? $geo->timezone
            ?? config('app.default_user_timezone');
    }
}
