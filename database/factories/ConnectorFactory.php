<?php

namespace Database\Factories;

use App\Contracts\AccessToken;
use App\Enums\Service;
use App\Models\Connector;
use App\Models\User;
use App\Services\Calendar\GoogleCalendar\Auth\AccessToken as GoogleCalendarAccessToken;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Connector>
 */
class ConnectorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'provider_id' => fake()->randomElement(Service::cases())->id(),
            'external_id' => fake()->uuid(),
            'access_details' => fn (array $attributes): AccessToken => match (Service::fromId($attributes['provider_id'])) {
                Service::GOOGLE_CALENDAR,
                Service::MICROSOFT_OUTLOOK => new GoogleCalendarAccessToken(
                    fake()->uuid,
                    fake()->regexify('[A-Za-z0-9-_]{411}'),
                    fake()->regexify('[A-Za-z0-9-_]{411}'),
                    (int) now()->diffInSeconds(now()->addHour()),
                ),
            },
            'enabled' => true,
        ];
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'enabled' => false,
        ]);
    }
}
