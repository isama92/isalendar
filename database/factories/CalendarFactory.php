<?php

namespace Database\Factories;

use App\Models\Calendar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Calendar>
 */
class CalendarFactory extends Factory
{
    public function definition(): array
    {
        return [
            'external_id' => fake()->uuid(),
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'enabled' => true,
            'synchronized_at' => null,
        ];
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'enabled' => false,
        ]);
    }

    public function synchronized(): static
    {
        return $this->state(fn (array $attributes): array => [
            'synchronized_at' => now(),
        ]);
    }
}
