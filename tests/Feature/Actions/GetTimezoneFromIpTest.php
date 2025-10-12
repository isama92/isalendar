<?php

use App\Actions\GetTimezoneFromIp;
use Torann\GeoIP\Facades\GeoIP;

uses()->group('actions');

beforeEach(function (): void {
    // Clear the global GeoIP mock from Pest.php
    Mockery::close();
});

it('returns timezone from time_zone name when available', function (): void {
    $location = new class
    {
        public $time_zone = ['name' => 'America/New_York'];

        public $timezone;
    };

    GeoIP::swap(new class($location)
    {
        public function __construct(private $location) {}

        public function getLocation($ip)
        {
            return $this->location;
        }
    });

    $timezone = app(GetTimezoneFromIp::class)->execute('192.168.1.1');

    expect($timezone)->toBe('America/New_York');
});

it('falls back to timezone property when time_zone name is not available', function (): void {
    $location = new class
    {
        public $time_zone;

        public $timezone = 'America/Chicago';
    };

    GeoIP::swap(new class($location)
    {
        public function __construct(private $location) {}

        public function getLocation($ip)
        {
            return $this->location;
        }
    });

    $timezone = app(GetTimezoneFromIp::class)->execute('192.168.1.1');

    expect($timezone)->toBe('America/Chicago');
});

it('falls back to default config when both time_zone and timezone are not available', function (): void {
    $location = new class
    {
        public $time_zone;

        public $timezone;
    };

    GeoIP::swap(new class($location)
    {
        public function __construct(private $location) {}

        public function getLocation($ip)
        {
            return $this->location;
        }
    });

    $timezone = app(GetTimezoneFromIp::class)->execute('192.168.1.1');

    expect($timezone)->toBe(config('app.default_user_timezone'));
});
