<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Modules\ChassisModule;
use App\Models\Modules\WheelSetModule;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class
        ]);

        /** @var Module $moduleForChassis */
        $moduleForChassis = Module::factory()->create([
            'assembly_time' => 2,
            'cost' => 4400,
            'name' => 'Nikinella',
            'image' => 'placeholder.jpg'
        ]);
        /** @var Module $moduleForWheelSet */
        $moduleForWheelSet = Module::factory()->create([
            'assembly_time' => 1,
            'cost' => 1200,
            'name' => 'Z15-4',
            'image' => 'placeholder.jpg'
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
        // Second attachment is redundant. Pivot table handles bidirectional association.
//        $wheelSetModule->compatibleChassisModules()->attach($chassisModule->id);
    }
}
