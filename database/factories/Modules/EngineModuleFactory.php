<?php

namespace Database\Factories\Modules;

use App\EngineType;
use App\Models\Module;
use App\Models\Modules\EngineModule;
use App\ModuleType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EngineModule>
 */
class EngineModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $module = Module::factory()->create([
            'type' => ModuleType::ENGINE
        ]);
        return [
            'module_id' => $module->id,
            'type' => $this->faker->randomElement(EngineType::values()),
            'horse_power' => $this->faker->numberBetween(10, 50)
        ];
    }
}
