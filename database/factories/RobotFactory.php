<?php

namespace Database\Factories;

use App\Models\Robot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Robot>
 */
class RobotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->streetName() . '+' . $this->faker->colorName()
        ];
    }
}
