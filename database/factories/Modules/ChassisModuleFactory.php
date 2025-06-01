<?php

namespace Database\Factories\Modules;

use App\Models\Module;
use App\Models\Modules\ChassisModule;
use App\ModuleType;
use App\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChassisModule>
 */
class ChassisModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $module = Module::factory()->create([
            'type' => ModuleType::CHASSIS
        ]);
        return [
            'module_id' => $module->id,
            'wheel_quantity' => $this->faker->numberBetween(1, 4) * 2,
            'vehicle_type' => $this->faker->randomElement(VehicleType::values()),
            'length' => $this->faker->numberBetween(1, 10) * 100,
            'width' => $this->faker->numberBetween(1, 10) * 100,
            'height' => $this->faker->numberBetween(1, 10) * 100
        ];
    }
}
