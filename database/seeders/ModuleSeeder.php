<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Modules\ChassisModule;
use App\Models\Modules\WheelSetModule;
use App\ModuleType;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var Module $moduleForChassis */
        $moduleForChassis = Module::factory()->create([
            'assembly_time' => 2,
            'cost' => 4400,
            'name' => 'Nikinella',
            'image' => 'placeholder.jpg',
            'type' => ModuleType::CHASSIS
        ]);
        /** @var Module $moduleForWheelSet */
        $moduleForWheelSet = Module::factory()->create([
            'assembly_time' => 1,
            'cost' => 1200,
            'name' => 'Z15-4',
            'image' => 'placeholder.jpg',
            'type' => ModuleType::WHEEL_SET
        ]);

        /** @var ChassisModule $chassisModule */
        $chassisModule = ChassisModule::factory()->create([
            'module_id' => $moduleForChassis->id,
            'wheel_quantity' => 4,
            'vehicle_type' => 'car',
            'length' => 400,
            'width' => 186,
            'height' => 165
        ]);
        /** @var WheelSetModule $wheelSetModule */
        $wheelSetModule = WheelSetModule::factory()->create([
            'module_id' => $moduleForWheelSet->id,
            'type' => 'summer',
            'diameter' => 15,
            'wheel_quantity' => 4
        ]);

        $chassisModule->compatibleWheelSetModules()->attach($wheelSetModule->id);
    }
}
