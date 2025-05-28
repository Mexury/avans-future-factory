<?php

namespace Database\Seeders;

use App\Models\Robot;
use App\Models\RobotVehicleType;
use App\VehicleType;
use Illuminate\Database\Seeder;

class RobotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create TwoWheels robot
        $twoWheels = Robot::create([
            'name' => 'TwoWheels'
        ]);

        // TwoWheels handles bicycles and scooters
        RobotVehicleType::create([
            'robot_id' => $twoWheels->id,
            'vehicle_type' => VehicleType::BICYCLE->value
        ]);

        RobotVehicleType::create([
            'robot_id' => $twoWheels->id,
            'vehicle_type' => VehicleType::SCOOTER->value
        ]);

        // Create HydroBoy robot
        $hydroBoy = Robot::create([
            'name' => 'HydroBoy'
        ]);

        // HydroBoy handles hydrogen vehicles of all types
        // This would require checking the engine type in the application logic
        RobotVehicleType::create([
            'robot_id' => $hydroBoy->id,
            'vehicle_type' => VehicleType::CAR->value
        ]);

        RobotVehicleType::create([
            'robot_id' => $hydroBoy->id,
            'vehicle_type' => VehicleType::TRUCK->value
        ]);

        RobotVehicleType::create([
            'robot_id' => $hydroBoy->id,
            'vehicle_type' => VehicleType::BUS->value
        ]);

        // Create HeavyD robot
        $heavyD = Robot::create([
            'name' => 'HeavyD'
        ]);

        // HeavyD handles heavy vehicles
        RobotVehicleType::create([
            'robot_id' => $heavyD->id,
            'vehicle_type' => VehicleType::TRUCK->value
        ]);

        RobotVehicleType::create([
            'robot_id' => $heavyD->id,
            'vehicle_type' => VehicleType::BUS->value
        ]);
    }
}
