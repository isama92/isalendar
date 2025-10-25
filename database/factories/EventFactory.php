<?php

namespace Database\Factories;

use App\Models\Calendar;
use App\Models\Connector;
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
            'created_by_connector_id' => function (array $attributes) {
                $calendar = Calendar::find($attributes['calendar_id']);
                $connector = $calendar->connectors()->first();

                if (! $connector) {
                    $connector = Connector::factory()->create();
                    $calendar->connectors()->attach($connector);
                }

                return $connector->id;
            },
            'external_id' => fake()->uuid(),
            'title' => fake()->sentence(3),
            'timezone' => fn (array $attributes) => $attributes['all_day'] ? null : fake()->timezone(),
            'all_day' => fake()->boolean(20),
            'starts_at' => function (array $attributes) {
                $date = new Carbon(fake()->dateTimeBetween('now', '+1 month'));

                return $attributes['all_day'] ? $date->startOfDay() : $date;
            },
            'ends_at' => function (array $attributes) {
                $startsAt = $attributes['starts_at']->toImmutable();

                if ($attributes['all_day']) {
                    return $startsAt->addDays(fake()->numberBetween(1, 3));
                }

                return fake()->dateTimeBetween($startsAt, $startsAt->format('Y-m-d H:i:s').' +16 hours');
            },
            'description' => fake()->paragraph(),
        ];
    }
}
