<?php

namespace Database\Factories\Modules;

use App\Models\Module;
use App\Models\Modules\WheelSetModule;
use App\ModuleType;
use App\WheelType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WheelSetModule>
 */
class WheelSetModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $module = Module::factory()->create([
            'type' => ModuleType::WHEEL_SET
        ]);
        return [
            'module_id' => $module->id,
            'type' => $this->faker->randomElement(WheelType::values()),
            'diameter' => $this->faker->numberBetween(24, 35),
            'wheel_quantity' => $this->faker->numberBetween(1, 4) * 2
        ];
    }
}
