<?php

namespace Database\Factories;

use App\Models\Calendar;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'calendar_id' => Calendar::factory(),
            'external_id' => fake()->uuid(),
            'title' => fake()->sentence(3),
            'timezone' => fn (array $attributes) => $attributes['all_day'] ? null : fake()->timezone(),
            'all_day' => fake()->boolean(20),
            'starts_at' => function (array $attributes) {
                $date = new Carbon(fake()->dateTimeBetween('now', '+1 month'));

                return $attributes['all_day'] ? $date->startOfDay() : $date;
            },
            'ends_at' => fn (array $attributes) => $attributes['all_day'] ? null : fake()
                ->dateTimeBetween($attributes['starts_at'], $attributes['starts_at']->format('Y-m-d H:i:s').' +16 hours'),
            'description' => fake()->paragraph(),
        ];
    }
}
