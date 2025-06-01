<?php

namespace Database\Factories\Modules;

use App\Models\Module;
use App\Models\Modules\SteeringWheelModule;
use App\ModuleType;
use App\SteeringWheelShape;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SteeringWheelModule>
 */
class SteeringWheelModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $module = Module::factory()->create([
            'type' => ModuleType::STEERING_WHEEL
        ]);
        return [
            'module_id' => $module->id,
            'special_adjustments' => $this->faker->sentence(),
            'shape' => $this->faker->randomElement(SteeringWheelShape::values())
        ];
    }
}
