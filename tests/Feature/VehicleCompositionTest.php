<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\User;
use App\Models\Vehicle;
use App\ModuleType;
use App\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleCompositionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Vehicle $vehicle;
    protected array $modules = [];

    public function setUp(): void
    {
        parent::setUp();

        // Create a user
        $this->user = User::factory()->create();

        // Create a vehicle owned by the user
        $this->vehicle = Vehicle::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Test Vehicle',
            'type' => VehicleType::CAR->value
        ]);

        // Create a module of each type
        foreach (ModuleType::cases() as $type) {
            $this->modules[$type->value] = Module::factory()->create([
                'name' => "Test {$type->value} Module",
                'type' => $type->value,
                'assembly_time' => 1,
                'cost' => 1000,
                'image' => 'placeholder.jpg'
            ]);
        }
    }

    public function test_user_can_view_compositions_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('compositions.index'));

        $response->assertStatus(200);
        $response->assertViewIs('compositions.index');
    }

    public function test_user_can_view_create_composition_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('compositions.create'));

        $response->assertStatus(200);
        $response->assertViewIs('compositions.create');
        $response->assertSee('Create a new vehicle composition');
    }
<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleComposition;
use App\ModuleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleCompositionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test vehicle composition creation.
     */
    public function test_mechanic_can_create_vehicle_composition(): void
    {
        $user = User::factory()->create(['role' => 'mechanic']);

        // Create test modules
        $chassisModule = Module::factory()->create(['type' => ModuleType::CHASSIS]);
        $engineModule = Module::factory()->create(['type' => ModuleType::ENGINE]);
        $wheelSetModule = Module::factory()->create(['type' => ModuleType::WHEEL_SET]);
        $steeringWheelModule = Module::factory()->create(['type' => ModuleType::STEERING_WHEEL]);
        $seatingModule = Module::factory()->create(['type' => ModuleType::SEATING]);

        $response = $this->actingAs($user)->post(route('vehicle_compositions.store'), [
            'name' => 'Test Vehicle',
            'chassis_id' => $chassisModule->id,
            'engine_id' => $engineModule->id,
            'wheel_set_id' => $wheelSetModule->id,
            'steering_wheel_id' => $steeringWheelModule->id,
            'seating_id' => $seatingModule->id,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('vehicles', [
            'name' => 'Test Vehicle',
            'user_id' => $user->id,
        ]);

        $vehicle = Vehicle::where('name', 'Test Vehicle')->first();

        $this->assertDatabaseHas('vehicle_compositions', [
            'vehicle_id' => $vehicle->id,
            'module_id' => $chassisModule->id,
            'module_type' => ModuleType::CHASSIS->value,
            'installation_order' => 1,
        ]);

        $this->assertDatabaseHas('vehicle_compositions', [
            'vehicle_id' => $vehicle->id,
            'module_id' => $engineModule->id,
            'module_type' => ModuleType::ENGINE->value,
            'installation_order' => 2,
        ]);

        $this->assertDatabaseHas('vehicle_compositions', [
            'vehicle_id' => $vehicle->id,
            'module_id' => $wheelSetModule->id,
            'module_type' => ModuleType::WHEEL_SET->value,
            'installation_order' => 3,
        ]);

        $this->assertDatabaseHas('vehicle_compositions', [
            'vehicle_id' => $vehicle->id,
            'module_id' => $steeringWheelModule->id,
            'module_type' => ModuleType::STEERING_WHEEL->value,
            'installation_order' => 4,
        ]);

        $this->assertDatabaseHas('vehicle_compositions', [
            'vehicle_id' => $vehicle->id,
            'module_id' => $seatingModule->id,
            'module_type' => ModuleType::SEATING->value,
            'installation_order' => 5,
        ]);
    }

    /**
     * Test vehicle composition without optional seating.
     */
    public function test_can_create_vehicle_without_seating(): void
    {
        $user = User::factory()->create(['role' => 'mechanic']);

        // Create test modules
        $chassisModule = Module::factory()->create(['type' => ModuleType::CHASSIS]);
        $engineModule = Module::factory()->create(['type' => ModuleType::ENGINE]);
        $wheelSetModule = Module::factory()->create(['type' => ModuleType::WHEEL_SET]);
        $steeringWheelModule = Module::factory()->create(['type' => ModuleType::STEERING_WHEEL]);

        $response = $this->actingAs($user)->post(route('vehicle_compositions.store'), [
            'name' => 'Test Vehicle Without Seating',
            'chassis_id' => $chassisModule->id,
            'engine_id' => $engineModule->id,
            'wheel_set_id' => $wheelSetModule->id,
            'steering_wheel_id' => $steeringWheelModule->id,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('vehicles', [
            'name' => 'Test Vehicle Without Seating',
            'user_id' => $user->id,
        ]);

        $vehicle = Vehicle::where('name', 'Test Vehicle Without Seating')->first();

        // Should have 4 compositions, not 5 (without seating)
        $this->assertEquals(4, VehicleComposition::where('vehicle_id', $vehicle->id)->count());

        // Ensure no seating module was added
        $this->assertDatabaseMissing('vehicle_compositions', [
            'vehicle_id' => $vehicle->id,
            'module_type' => ModuleType::SEATING->value,
        ]);
    }

    /**
     * Test calculating total cost of a vehicle.
     */
    public function test_calculates_total_cost_correctly(): void
    {
        $user = User::factory()->create(['role' => 'mechanic']);

        // Create test modules with specific costs
        $chassisModule = Module::factory()->create([
            'type' => ModuleType::CHASSIS,
            'cost' => 1000
        ]);
        $engineModule = Module::factory()->create([
            'type' => ModuleType::ENGINE,
            'cost' => 2000
        ]);
        $wheelSetModule = Module::factory()->create([
            'type' => ModuleType::WHEEL_SET,
            'cost' => 500
        ]);
        $steeringWheelModule = Module::factory()->create([
            'type' => ModuleType::STEERING_WHEEL,
            'cost' => 300
        ]);
        $seatingModule = Module::factory()->create([
            'type' => ModuleType::SEATING,
            'cost' => 700
        ]);

        // Create a vehicle with these modules
        $vehicle = Vehicle::create([
            'name' => 'Cost Test Vehicle',
            'user_id' => $user->id
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

        // Calculate expected total cost
        $expectedTotalCost = 1000 + 2000 + 500 + 300 + 700; // 4500

        // Access the vehicle with its compositions and modules
        $vehicleWithModules = Vehicle::with('compositions.module')->find($vehicle->id);

        // Calculate total cost using the accessor on the model
        $actualTotalCost = $vehicleWithModules->total_cost;

        $this->assertEquals($expectedTotalCost, $actualTotalCost);
    }
}
    public function test_user_can_create_composition()
    {
        $modules = [];

        // Select one module of each type
        foreach ($this->modules as $type => $module) {
            $modules[$type] = $module->id;
        }

        $response = $this->actingAs($this->user)
            ->post(route('compositions.store'), [
                'vehicle_id' => $this->vehicle->id,
                'modules' => $modules
            ]);

        $response->assertRedirect(route('compositions.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('vehicle_compositions', [
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'total_assembly_time' => count($modules), // Each module has 1 hour assembly time
            'total_cost' => count($modules) * 1000 // Each module costs 1000
        ]);

        // Check that module relationships were created
        foreach ($modules as $type => $moduleId) {
            $this->assertDatabaseHas('composition_modules', [
                'module_id' => $moduleId,
                'module_type' => $type
            ]);
        }
    }

    public function test_user_cannot_compose_another_users_vehicle()
    {
        // Create another user and their vehicle
        $otherUser = User::factory()->create();
        $otherVehicle = Vehicle::factory()->create([
            'user_id' => $otherUser->id,
            'name' => 'Other Vehicle',
            'type' => VehicleType::CAR->value
        ]);

        $modules = [];
        foreach ($this->modules as $type => $module) {
            $modules[$type] = $module->id;
        }

        $response = $this->actingAs($this->user)
            ->post(route('compositions.store'), [
                'vehicle_id' => $otherVehicle->id,
                'modules' => $modules
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('vehicle_compositions', [
            'user_id' => $this->user->id,
            'vehicle_id' => $otherVehicle->id
        ]);
    }

    public function test_user_can_view_composition_details()
    {
        // Create a composition first
        $modules = [];
        foreach ($this->modules as $type => $module) {
            $modules[$type] = $module->id;
        }

        $this->actingAs($this->user)
            ->post(route('compositions.store'), [
                'vehicle_id' => $this->vehicle->id,
                'modules' => $modules
            ]);

        // Get the created composition
        $composition = \App\Models\VehicleComposition::first();

        $response = $this->actingAs($this->user)
            ->get(route('compositions.show', $composition));

        $response->assertStatus(200);
        $response->assertViewIs('compositions.show');
        $response->assertSee($composition->name);
        $response->assertSee('$' . number_format($composition->total_cost, 2));
    }

    public function test_user_can_delete_composition()
    {
        // Create a composition first
        $modules = [];
        foreach ($this->modules as $type => $module) {
            $modules[$type] = $module->id;
        }

        $this->actingAs($this->user)
            ->post(route('compositions.store'), [
                'vehicle_id' => $this->vehicle->id,
                'modules' => $modules
            ]);

        // Get the created composition
        $composition = \App\Models\VehicleComposition::first();

        $response = $this->actingAs($this->user)
            ->delete(route('compositions.destroy', $composition));

        $response->assertRedirect(route('compositions.index'));
        $this->assertDatabaseMissing('vehicle_compositions', ['id' => $composition->id]);
        $this->assertDatabaseMissing('composition_modules', ['vehicle_composition_id' => $composition->id]);
    }
}
