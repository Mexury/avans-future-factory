<?php

namespace Database\Factories;

use App\Models\Module;
use App\ModuleType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Module>
 */
class ModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'assembly_time' => $this->faker->numberBetween(1, 4),
            'cost' => $this->faker->numberBetween(1, 25) * 100,
            'name' => $this->faker->streetName() . '+' . $this->faker->colorName(),
            'image' => 'placeholder.jpg',
            'type' => $this->faker->randomElement(ModuleType::values())
        ];
    }
}
