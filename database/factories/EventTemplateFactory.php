<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Calendar;
use App\Models\EventTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventTemplate>
 */
class EventTemplateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'calendar_id' => Calendar::factory(),
            'name' => fake()->words(2, true),
            'event_title' => fake()->sentence(3),
            'event_all_day' => fake()->boolean(20),
            'event_timezone' => fn (array $attributes) => $attributes['event_all_day'] ? null : fake()->timezone(),
            'event_description' => fake()->paragraph(),
        ];
    }
}
