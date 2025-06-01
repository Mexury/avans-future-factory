<?php

namespace Database\Factories\Modules;

use App\Models\Module;
use App\Models\Modules\SeatingModule;
use App\ModuleType;
use App\UpholsteryType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SeatingModule>
 */
class SeatingModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $module = Module::factory()->create([
            'type' => ModuleType::SEATING
        ]);
        return [
            'module_id' => $module->id,
            'quantity' => $this->faker->randomELement([1, 2, 4, 6, 8]),
            'upholstery' => $this->faker->randomElement(UpholsteryType::values())
        ];
    }
}
