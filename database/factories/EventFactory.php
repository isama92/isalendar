<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'all_day' => fake()->boolean(20),
            'starts_at' => fn(array $attributes) => $attributes['all_day']? null : fake()
                ->dateTimeBetween('now', '+1 month'),
            'ends_at' => fn(array $attributes) => $attributes['all_day']? null : fake()
                ->dateTimeBetween($attributes['starts_at'], $attributes['starts_at']->format('Y-m-d H:i:s').' +16 hours'),
            'description' => fake()->paragraph(),
        ];
    }
}
