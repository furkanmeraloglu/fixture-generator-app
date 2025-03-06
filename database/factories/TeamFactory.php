<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name . ' FC',
            'strength' => $this->faker->numberBetween(70, 100),
            'points' => 0,
            'goals_scored' => 0,
            'goals_conceded' => 0,
            'wins' => 0,
            'losses' => 0,
            'draws' => 0,
            'played_matches' => 0,
        ];
    }
}
