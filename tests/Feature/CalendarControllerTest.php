<?php

use App\Models\Module;
use App\Models\Robot;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehiclePlanning;
use App\ModuleType;
use App\UserRole;
use App\VehicleType;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->year = '2024';
    $this->month = '06';
    $this->day = '15';
    $this->date = "$this->year-$this->month-$this->day";

    $this->users = [];
    foreach (UserRole::cases() as $role) {
        $this->users[$role->value] = User::factory()->create([
            'role' => $role
        ]);
    }
});

test('as admin, calendar.index redirects when year and month are missing', function () {
    $this->actingAs($this->users['admin']);

    $now = Carbon::now();
    $expectedYear = $now->year;
    $expectedMonth = str_pad($now->month, 2, '0', STR_PAD_LEFT);

    $response = $this->get(route('calendar.index'));
    $response->assertRedirect(route('calendar.index', [
        'year' => $expectedYear,
        'month' => $expectedMonth
    ]));
});
test('as admin, calendar.index shows calendar with valid year and month', function () {
    $this->actingAs($this->users['admin']);

    $response = $this->get(route('calendar.index', [
        'year' => $this->year,
        'month' => $this->month,
    ]));

    $response->assertStatus(200);
    $response->assertViewIs('calendar.index');
    $response->assertViewHas('calendar');
});
test('as admin, calendar.create returns 404 for invalid date', function () {
    $this->actingAs($this->users['admin']);

    $response = $this->get(route('calendar.create', [
        'year' => $this->year,
        'month' => '13',
        'day' => $this->day
    ]));
    $response->assertStatus(404);
});
test('as admin, calendar.create shows form with valid date', function () {
    $this->actingAs($this->users['admin']);

    $response = $this->get(route('calendar.create', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]));

    $response->assertStatus(200);
    $response->assertViewIs('calendar.create');
    $response->assertViewHas(['robots', 'slots', 'vehicles', 'modules', 'year', 'month', 'day']);
});
test('as admin, calendar.store returns 404 for invalid date', function () {
    $this->actingAs($this->users['admin']);

    $response = $this->post(route('calendar.store', [
        'year' => $this->year,
        'month' => '13',
        'day' => $this->day
    ]));

    $response->assertStatus(404);
});
test('as admin, calendar.store validates required fields', function () {
    $this->actingAs($this->users['admin']);

    $response = $this->post(route('calendar.store', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]));

    $response->assertSessionHasErrors(['module_id', 'robot_id', 'vehicle_id', 'slot']);
});
test('as admin, calendar.store prevents duplicate module for vehicle', function () {
    $vehicle = Vehicle::factory()->create(['type' => VehicleType::CAR]);
    $robot = Robot::factory()->create();
    $robot->vehicleTypes()->attach(VehicleType::CAR);

    $module = Module::factory()->create([
        'type' => ModuleType::CHASSIS,
        'assembly_time' => 1
    ]);

    VehiclePlanning::create([
        'vehicle_id' => $vehicle->id,
        'module_id' => $module->id,
        'robot_id' => $robot->id,
        'date' => $this->date,
        'slot_start' => 0,
        'slot_end' => 0
    ]);

    // Attempt to create another planning with the same module and vehicle
    $response = $this->post(route('calendar.store', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]), [
        'module_id' => $module->id,
        'robot_id' => $robot->id,
        'vehicle_id' => $vehicle->id,
        'slot' => ['0' => 'true']
    ]);

    $response->assertSessionHasErrors('module_id');
    $this->assertEquals(1, VehiclePlanning::count());
});

//test('store enforces module order', function () {
//    // Setup
//    $vehicle = Vehicle::factory()->create(['type' => VehicleType::CAR]);
//    $robot = Robot::factory()->create();
//    $robot->vehicleTypes()->attach(VehicleType::CAR);
//
//    $chassisModule = Module::factory()->create([
//        'type' => ModuleType::CHASSIS,
//        'assembly_time' => 1
//    ]);
//
//    $wheelSetModule = Module::factory()->create([
//        'type' => ModuleType::WHEEL_SET,
//        'assembly_time' => 1
//    ]);
//
//    // Try to add wheel set before chassis
//    $response = $this->post(route('calendar.store', [
//        'year' => $this->year,
//        'month' => $this->month,
//        'day' => $this->day
//    ]), [
//        'module_id' => $wheelSetModule->id,
//        'robot_id' => $robot->id,
//        'vehicle_id' => $vehicle->id,
//        'slot' => ['0' => 'true']
//    ]);
//
//    $response->assertSessionHasErrors('module_id');
//    $this->assertEquals(0, VehiclePlanning::count());
//});
//
//test('store validates robot compatibility with vehicle type', function () {
//    // Setup
//    $vehicle = Vehicle::factory()->create(['type' => VehicleType::CAR]);
//    $robot = Robot::factory()->create();
//    // Robot doesn't support car type
//    $robot->vehicleTypes()->attach(VehicleType::TRUCK);
//
//    $module = Module::factory()->create([
//        'type' => ModuleType::CHASSIS,
//        'assembly_time' => 1
//    ]);
//
//    $response = $this->post(route('calendar.store', [
//        'year' => $this->year,
//        'month' => $this->month,
//        'day' => $this->day
//    ]), [
//        'module_id' => $module->id,
//        'robot_id' => $robot->id,
//        'vehicle_id' => $vehicle->id,
//        'slot' => ['0' => 'true']
//    ]);
//
//    $response->assertSessionHasErrors('robot_id');
//    $this->assertEquals(0, VehiclePlanning::count());
//});
//
//test('store validates slot count matches module assembly time', function () {
//    // Setup
//    $vehicle = Vehicle::factory()->create(['type' => VehicleType::CAR]);
//    $robot = Robot::factory()->create();
//    $robot->vehicleTypes()->attach(VehicleType::CAR);
//
//    $module = Module::factory()->create([
//        'type' => ModuleType::CHASSIS,
//        'assembly_time' => 2 // Requires 2 slots
//    ]);
//
//    $response = $this->post(route('calendar.store', [
//        'year' => $this->year,
//        'month' => $this->month,
//        'day' => $this->day
//    ]), [
//        'module_id' => $module->id,
//        'robot_id' => $robot->id,
//        'vehicle_id' => $vehicle->id,
//        'slot' => ['0' => 'true'] // Only 1 slot selected
//    ]);
//
//    $response->assertSessionHasErrors('slot');
//    $this->assertEquals(0, VehiclePlanning::count());
//});
//
//test('store validates slots are consecutive', function () {
//    // Setup
//    $vehicle = Vehicle::factory()->create(['type' => VehicleType::CAR]);
//    $robot = Robot::factory()->create();
//    $robot->vehicleTypes()->attach(VehicleType::CAR);
//
//    $module = Module::factory()->create([
//        'type' => ModuleType::CHASSIS,
//        'assembly_time' => 2
//    ]);
//
//    $response = $this->post(route('calendar.store', [
//        'year' => $this->year,
//        'month' => $this->month,
//        'day' => $this->day
//    ]), [
//        'module_id' => $module->id,
//        'robot_id' => $robot->id,
//        'vehicle_id' => $vehicle->id,
//        'slot' => ['0' => 'true', '2' => 'true'] // Not consecutive
//    ]);
//
//    $response->assertSessionHasErrors('slot');
//    $this->assertEquals(0, VehiclePlanning::count());
//});
//
//test('store detects robot scheduling conflicts', function () {
//    // Setup
//    $vehicle1 = Vehicle::factory()->create(['type' => VehicleType::CAR]);
//    $vehicle2 = Vehicle::factory()->create(['type' => VehicleType::CAR]);
//    $robot = Robot::factory()->create();
//    $robot->vehicleTypes()->attach(VehicleType::CAR);
//
//    $module1 = Module::factory()->create([
//        'type' => ModuleType::CHASSIS,
//        'assembly_time' => 1
//    ]);
//
//    $module2 = Module::factory()->create([
//        'type' => ModuleType::CHASSIS,
//        'assembly_time' => 1
//    ]);
//
//    // Create an existing schedule
//    VehiclePlanning::create([
//        'vehicle_id' => $vehicle1->id,
//        'module_id' => $module1->id,
//        'robot_id' => $robot->id,
//        'date' => $this->date,
//        'slot_start' => 0,
//        'slot_end' => 0
//    ]);
//
//    // Try to create a conflicting schedule
//    $response = $this->post(route('calendar.store', [
//        'year' => $this->year,
//        'month' => $this->month,
//        'day' => $this->day
//    ]), [
//        'module_id' => $module2->id,
//        'robot_id' => $robot->id,
//        'vehicle_id' => $vehicle2->id,
//        'slot' => ['0' => 'true'] // Same time slot
//    ]);
//
//    $response->assertSessionHasErrors('robot_id');
//    $this->assertEquals(1, VehiclePlanning::count());
//});
//
//test('store creates planning successfully', function () {
//    // Setup
//    $vehicle = Vehicle::factory()->create(['type' => VehicleType::CAR]);
//    $robot = Robot::factory()->create();
//    $robot->vehicleTypes()->attach(VehicleType::CAR);
//
//    $module = Module::factory()->create([
//        'type' => ModuleType::CHASSIS,
//        'assembly_time' => 2
//    ]);
//
//    $response = $this->post(route('calendar.store', [
//        'year' => $this->year,
//        'month' => $this->month,
//        'day' => $this->day
//    ]), [
//        'module_id' => $module->id,
//        'robot_id' => $robot->id,
//        'vehicle_id' => $vehicle->id,
//        'slot' => ['0' => 'true', '1' => 'true'] // 2 consecutive slots
//    ]);
//
//    $response->assertRedirect(route('calendar.show', [
//        'year' => $this->year,
//        'month' => $this->month,
//        'day' => $this->day
//    ]));
//    $response->assertSessionHas('success');
//
//    $this->assertEquals(1, VehiclePlanning::count());
//    $planning = VehiclePlanning::first();
//    $this->assertEquals($vehicle->id, $planning->vehicle_id);
//    $this->assertEquals($module->id, $planning->module_id);
//    $this->assertEquals($robot->id, $planning->robot_id);
//    $this->assertEquals($this->date, $planning->date->format('Y-m-d'));
//    $this->assertEquals(0, $planning->slot_start);
//    $this->assertEquals(1, $planning->slot_end);
//});
//
//test('show returns 404 for invalid date', function () {
//    $response = $this->get(route('calendar.show', [
//        'year' => $this->year,
//        'month' => '13', // Invalid month
//        'day' => $this->day
//    ]));
//
//    $response->assertStatus(404);
//});
//
//test('show displays vehicle planning for date', function () {
//    // Setup
//    $vehicle = Vehicle::factory()->create();
//    $robot = Robot::factory()->create();
//    $module = Module::factory()->create();
//
//    VehiclePlanning::create([
//        'vehicle_id' => $vehicle->id,
//        'module_id' => $module->id,
//        'robot_id' => $robot->id,
//        'date' => $this->date,
//        'slot_start' => 0,
//        'slot_end' => 1
//    ]);
//
//    $response = $this->get(route('calendar.show', [
//        'year' => $this->year,
//        'month' => $this->month,
//        'day' => $this->day
//    ]));
//
//    $response->assertStatus(200);
//    $response->assertViewIs('calendar.show');
//    $response->assertViewHas('vehiclePlanning');
//    $this->assertEquals(1, $response->viewData('vehiclePlanning')->count());
//});
//
//test('destroy deletes planning and redirects', function () {
//    // Setup
//    $vehicle = Vehicle::factory()->create();
//    $robot = Robot::factory()->create();
//    $module = Module::factory()->create();
//
//    $planning = VehiclePlanning::create([
//        'vehicle_id' => $vehicle->id,
//        'module_id' => $module->id,
//        'robot_id' => $robot->id,
//        'date' => $this->date,
//        'slot_start' => 0,
//        'slot_end' => 1
//    ]);
//
//    $response = $this->delete(route('calendar.destroy', $planning));
//
//    $response->assertRedirect(route('calendar.show', [
//        'year' => $this->year,
//        'month' => $this->month,
//        'day' => $this->day
//    ]));
//    $response->assertSessionHas('success');
//    $this->assertEquals(0, VehiclePlanning::count());
//});
