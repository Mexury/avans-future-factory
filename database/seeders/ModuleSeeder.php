<?php

namespace Database\Seeders;

use App\EngineType;
use App\Models\Module;
use App\Models\Modules\ChassisModule;
use App\Models\Modules\WheelSetModule;
use App\ModuleType;
use App\SteeringWheelShape;
use App\UpholsteryType;
use App\WheelType;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Module::factory()->create([
            'assembly_time' => 1,
            'cost' => 4400,
            'name' => 'Nikinella',
            'image' => 'seeded/modules/chassis.png',
            'type' => ModuleType::CHASSIS
        ])->chassisModule()->create([
            'wheel_quantity' => 4,
            'vehicle_type' => 'car',
            'length' => 400,
            'width' => 186,
            'height' => 165
        ]);

        Module::factory()->create([
            'assembly_time' => 2,
            'cost' => 32000,
            'name' => 'waterstof138',
            'image' => 'seeded/modules/engine.jpg',
            'type' => ModuleType::ENGINE
        ])->engineModule()->create([
            'type' => EngineType::HYDROGEN,
            'horse_power' => 138
        ]);

        Module::factory()->create([
            'assembly_time' => 1,
            'cost' => 1200,
            'name' => 'Z15-4',
            'image' => 'seeded/modules/wheel_set.jpg',
            'type' => ModuleType::WHEEL_SET
        ])->wheelSetModule()->create([
            'type' => WheelType::SUMMER,
            'diameter' => 15,
            'wheel_quantity' => 4
        ]);

        Module::factory()->create([
            'assembly_time' => 1,
            'cost' => 400,
            'name' => 'schapenstadium',
            'image' => 'seeded/modules/steering_wheel.png',
            'type' => ModuleType::STEERING_WHEEL
        ])->steeringWheelModule()->create([
            'special_adjustments' => 'schapenvacht',
            'shape' => SteeringWheelShape::STADIUM
        ]);

        Module::factory()->create([
            'assembly_time' => 1,
            'cost' => 1600,
            'name' => 'stoelen/zadel',
            'image' => 'seeded/modules/seating.jpg',
            'type' => ModuleType::SEATING
        ])->seatingModule()->create([
            'quantity' => 5,
            'upholstery' => UpholsteryType::LEATHER
        ]);
    }
}
