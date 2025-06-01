<?php

namespace Database\Factories;

use App\Models\Vehicle;
use App\ModuleType;
use App\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->streetName() . '+' . $this->faker->colorName(),
            'type' => $this->faker->randomElement(VehicleType::values())
        ];
    }
}
