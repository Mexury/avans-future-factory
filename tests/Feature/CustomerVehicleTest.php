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

class CustomerVehicleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test customer can view their own vehicles.
     */
    public function test_customer_can_view_own_vehicles(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $otherCustomer = User::factory()->create(['role' => 'customer']);

        // Create vehicles for both customers
        $customerVehicle = Vehicle::create([
            'name' => 'Customer Vehicle',
            'user_id' => $customer->id
        ]);

        $otherVehicle = Vehicle::create([
            'name' => 'Other Customer Vehicle',
            'user_id' => $otherCustomer->id
        ]);

        // Customer should see their own vehicle
        $response = $this->actingAs($customer)->get(route('customer.vehicles.index'));
        $response->assertOk();
        $response->assertSee('Customer Vehicle');
        $response->assertDontSee('Other Customer Vehicle');

        // Other customer should see their own vehicle
        $response = $this->actingAs($otherCustomer)->get(route('customer.vehicles.index'));
        $response->assertOk();
        $response->assertSee('Other Customer Vehicle');
        $response->assertDontSee('Customer Vehicle');
    }

    /**
     * Test customer can view vehicle details with progress.
     */
    public function test_customer_can_view_vehicle_progress(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        // Create modules
        $chassisModule = Module::factory()->create(['type' => ModuleType::CHASSIS]);
        $engineModule = Module::factory()->create(['type' => ModuleType::ENGINE]);
        $wheelSetModule = Module::factory()->create(['type' => ModuleType::WHEEL_SET]);

        // Create vehicle with modules
        $vehicle = Vehicle::create([
            'name' => 'Progress Test Vehicle',
            'user_id' => $customer->id
        ]);

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

        // Create robot and schedule
        $robot = Robot::create(['name' => 'Progress Test Robot']);

        // Schedule the chassis module
        $schedule = RobotSchedule::create([
            'robot_id' => $robot->id,
            'date' => now()->format('Y-m-d'),
            'time_slot' => 1,
        ]);

        VehiclePlanning::create([
            'robot_schedule_id' => $schedule->id,
            'vehicle_id' => $vehicle->id,
            'module_id' => $chassisModule->id,
            'module_type' => ModuleType::CHASSIS
        ]);

        // View vehicle details
        $response = $this->actingAs($customer)->get(route('customer.vehicles.show', $vehicle->id));
        $response->assertOk();

        // Should show progress and status
        $response->assertSee('Progress Test Vehicle');
        $response->assertSee('In production'); // Status

        // Progress should be approximately 33% (1 of 3 modules scheduled)
        $response->assertSee('33%');
    }

    /**
     * Test customer cannot view other customer's vehicle details.
     */
    public function test_customer_cannot_view_other_customers_vehicle(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $otherCustomer = User::factory()->create(['role' => 'customer']);

        $otherVehicle = Vehicle::create([
            'name' => 'Other Customer Vehicle',
            'user_id' => $otherCustomer->id
        ]);

        // Try to view other customer's vehicle
        $response = $this->actingAs($customer)->get(route('customer.vehicles.show', $otherVehicle->id));
        $response->assertStatus(404); // Should return not found
    }
}
