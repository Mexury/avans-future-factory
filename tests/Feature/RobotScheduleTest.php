<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Robot;
use App\Models\RobotSchedule;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleComposition;
use App\Models\VehiclePlanning;
use App\ModuleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RobotScheduleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test robot schedule creation.
     */
    public function test_planner_can_create_robot_schedule(): void
    {
        $user = User::factory()->create(['role' => 'planner']);

        // Create a robot
        $robot = Robot::create(['name' => 'Test Robot']);

        // Create a vehicle with modules
        $this->createTestVehicle();
        $vehicle = Vehicle::first();

        $response = $this->actingAs($user)->post(route('robot_schedules.store'), [
            'robot_id' => $robot->id,
            'date' => now()->addDay()->format('Y-m-d'),
            'time_slot' => 1,
            'vehicle_id' => $vehicle->id,
            'module_type' => ModuleType::CHASSIS->value,
        ]);

        $response->assertRedirect(route('robot_schedules.index'));

        $this->assertDatabaseHas('robot_schedules', [
            'robot_id' => $robot->id,
            'date' => now()->addDay()->format('Y-m-d'),
            'time_slot' => 1,
        ]);

        $schedule = RobotSchedule::first();

        $this->assertDatabaseHas('vehicle_planning', [
            'robot_schedule_id' => $schedule->id,
            'vehicle_id' => $vehicle->id,
            'module_type' => ModuleType::CHASSIS->value,
        ]);
    }

    /**
     * Test scheduling conflict detection.
     */
    public function test_cannot_schedule_same_time_slot_twice(): void
    {
        $user = User::factory()->create(['role' => 'planner']);

        // Create a robot
        $robot = Robot::create(['name' => 'Test Robot']);

        // Create two vehicles with modules
        $this->createTestVehicle('Vehicle 1');
        $this->createTestVehicle('Vehicle 2');
        $vehicle1 = Vehicle::where('name', 'Vehicle 1')->first();
        $vehicle2 = Vehicle::where('name', 'Vehicle 2')->first();

        // Schedule first vehicle
        $scheduleDate = now()->addDay()->format('Y-m-d');
        $scheduleTimeSlot = 2;

        $this->actingAs($user)->post(route('robot_schedules.store'), [
            'robot_id' => $robot->id,
            'date' => $scheduleDate,
            'time_slot' => $scheduleTimeSlot,
            'vehicle_id' => $vehicle1->id,
            'module_type' => ModuleType::CHASSIS->value,
        ]);

        // Try to schedule second vehicle at same time
        $response = $this->actingAs($user)->post(route('robot_schedules.store'), [
            'robot_id' => $robot->id,
            'date' => $scheduleDate,
            'time_slot' => $scheduleTimeSlot,
            'vehicle_id' => $vehicle2->id,
            'module_type' => ModuleType::CHASSIS->value,
        ]);

        $response->assertSessionHasErrors();

        // Verify only one schedule exists for this time slot
        $this->assertEquals(
            1,
            RobotSchedule::where('robot_id', $robot->id)
                ->where('date', $scheduleDate)
                ->where('time_slot', $scheduleTimeSlot)
                ->count()
        );
    }

    /**
     * Test vehicle completion date calculation.
     */
    public function test_calculates_vehicle_completion_date(): void
    {
        $user = User::factory()->create(['role' => 'planner']);

        // Create a robot
        $robot = Robot::create(['name' => 'Test Robot']);

        // Create a vehicle with modules
        $this->createTestVehicle();
        $vehicle = Vehicle::first();

        // Schedule all modules on consecutive days
        $startDate = now()->addDay();
        $moduleTypes = [
            ModuleType::CHASSIS,
            ModuleType::ENGINE,
            ModuleType::WHEEL_SET,
            ModuleType::STEERING_WHEEL,
            ModuleType::SEATING
        ];

        foreach ($moduleTypes as $index => $moduleType) {
            $scheduleDate = $startDate->copy()->addDays($index)->format('Y-m-d');
            $scheduleTimeSlot = ($index % 4) + 1;

            // Create robot schedule
            $schedule = RobotSchedule::create([
                'robot_id' => $robot->id,
                'date' => $scheduleDate,
                'time_slot' => $scheduleTimeSlot,
            ]);

            // Get the composition for this module type
            $composition = $vehicle->compositions()
                ->where('module_type', $moduleType)
                ->first();

            // Create vehicle planning
            VehiclePlanning::create([
                'robot_schedule_id' => $schedule->id,
                'vehicle_id' => $vehicle->id,
                'module_id' => $composition->module_id,
                'module_type' => $moduleType
            ]);
        }

        // Get completion date
        $response = $this->actingAs($user)->getJson(route('robot_schedules.vehicle_completion', $vehicle->id));

        $response->assertOk();
        $response->assertJson([
            'complete' => true,
            'completion_date' => $startDate->copy()->addDays(4)->format('Y-m-d'),
            'time_slot' => 1,
        ]);
    }

    /**
     * Helper method to create a test vehicle with all modules
     */
    private function createTestVehicle($name = 'Test Vehicle'): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        // Create test modules
        $chassisModule = Module::factory()->create(['type' => ModuleType::CHASSIS]);
        $engineModule = Module::factory()->create(['type' => ModuleType::ENGINE]);
        $wheelSetModule = Module::factory()->create(['type' => ModuleType::WHEEL_SET]);
        $steeringWheelModule = Module::factory()->create(['type' => ModuleType::STEERING_WHEEL]);
        $seatingModule = Module::factory()->create(['type' => ModuleType::SEATING]);

        // Create a vehicle
        $vehicle = Vehicle::create([
            'name' => $name,
            'user_id' => $user->id
        ]);

        // Add compositions
        VehicleComposition::create([
            'vehicle_id' => $vehicle->id,
            'module_id' => $chassisModule->id,
            'module_type' => ModuleType::CHASSIS,
            'installation_order' => 1
        ]);

        VehicleComposition::create([
            'vehicle_id' => $vehicle->id,
            'module_id' => $engineModule->id,
            'module_type' => ModuleType::ENGINE,
            'installation_order' => 2
        ]);

        VehicleComposition::create([
            'vehicle_id' => $vehicle->id,
            'module_id' => $wheelSetModule->id,
            'module_type' => ModuleType::WHEEL_SET,
            'installation_order' => 3
        ]);

        VehicleComposition::create([
            'vehicle_id' => $vehicle->id,
            'module_id' => $steeringWheelModule->id,
            'module_type' => ModuleType::STEERING_WHEEL,
            'installation_order' => 4
        ]);

        VehicleComposition::create([
            'vehicle_id' => $vehicle->id,
            'module_id' => $seatingModule->id,
            'module_type' => ModuleType::SEATING,
            'installation_order' => 5
        ]);
    }
}
