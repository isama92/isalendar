<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Torann\GeoIP\Facades\GeoIP;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function (): void {
        Log::shouldReceive('emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug', 'log')
            ->never();
        GeoIP::shouldReceive('getLocation')
            ->zeroOrMoreTimes()
            ->andReturn(['timezone' => 'Europe/Amsterdam']);
        test()->user = User::factory()->create();
    })
    ->in('Feature');

pest()->extend(
    TestCase::class,
)->in('Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

// expect()->extend('toBeOne', function () {
//    return $this->toBe(1);
// });

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

// function something()
// {
//    // ..
// }
