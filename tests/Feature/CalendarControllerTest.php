<?php

use App\Models\Module;
use App\Models\Modules\ChassisModule;
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
    $this->actingAs($this->users['admin']);

    $vehicle = Vehicle::factory()->create([
        'user_id' => $this->users['admin']->id,
        'type' => VehicleType::CAR
    ]);
    $robot = Robot::factory()->create();

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

    $response->assertSessionHasErrors(['module_id']);
    $this->assertEquals(1, VehiclePlanning::count());
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
test('as admin, calendar.store enforces module order', function () {
    $this->actingAs($this->users['admin']);

    $vehicle = Vehicle::factory()->create([
        'user_id' => $this->users['admin']->id,
        'type' => VehicleType::CAR
    ]);
    $robot = Robot::factory()->create();

    $wheelSetModule = Module::factory()->create([
        'type' => ModuleType::WHEEL_SET,
        'assembly_time' => 1
    ]);

    // Try to add wheel set before chassis
    $this->post(route('calendar.store', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]), [
        'module_id' => $wheelSetModule->id,
        'robot_id' => $robot->id,
        'vehicle_id' => $vehicle->id,
        'slot' => ['1' => 'true']
    ]);

    $this->assertEquals(0, VehiclePlanning::count());
});
test('as admin, calendar.store validates robot compatibility with vehicle type', function () {
    $this->actingAs($this->users['admin']);

    $vehicle = Vehicle::factory()->create([
        'user_id' => $this->users['admin']->id,
        'type' => VehicleType::CAR
    ]);
    // Robot doesn't support car type
    $robot = Robot::factory()->create();
    $module = Module::factory()->create([
        'type' => ModuleType::CHASSIS,
        'assembly_time' => 1
    ]);

    $this->post(route('calendar.store', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]), [
        'module_id' => $module->id,
        'robot_id' => $robot->id,
        'vehicle_id' => $vehicle->id,
        'slot' => ['1' => 'true']
    ]);

    $this->assertEquals(0, VehiclePlanning::count());
});
test('as admin, calendar.store creates planning successfully', function () {
    $this->actingAs($this->users['admin']);

    $vehicle = Vehicle::factory()->create([
        'user_id' => $this->users['admin']->id,
        'type' => VehicleType::CAR
    ]);
    $robot = Robot::factory()->create();
    $robot->vehicleTypes()->create([
        'vehicle_type' => VehicleType::CAR
    ]);

    $chassisModule = ChassisModule::factory()->create([
        'vehicle_type' => VehicleType::CAR
    ]);
    $chassisModule->module->assembly_time = 2;
    $chassisModule->module->save();

    $response = $this->post(route('calendar.store', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]), [
        'module_id' => $chassisModule->module->id,
        'robot_id' => $robot->id,
        'vehicle_id' => $vehicle->id,
        'slot' => ['1' => 'true', '2' => 'true']
    ]);

    $response->assertRedirect(route('calendar.show', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]));
    $response->assertSessionHas('success');

    $this->assertEquals(1, VehiclePlanning::count());
    $planning = VehiclePlanning::first();
    $this->assertEquals($vehicle->id, $planning->vehicle_id);
    $this->assertEquals($chassisModule->module->id, $planning->module_id);
    $this->assertEquals($robot->id, $planning->robot_id);
    $this->assertEquals($this->date, $planning->date->format('Y-m-d'));
    $this->assertEquals(1, $planning->slot_start);
    $this->assertEquals(2, $planning->slot_end);
});
test('as admin, calendar.store creates planning unsuccessfully, due to incorrect timeslot count', function () {
    $this->actingAs($this->users['admin']);

    $vehicle = Vehicle::factory()->create([
        'user_id' => $this->users['admin']->id,
        'type' => VehicleType::CAR
    ]);
    $robot = Robot::factory()->create();
    $robot->vehicleTypes()->create([
        'vehicle_type' => VehicleType::CAR
    ]);

    $chassisModule = ChassisModule::factory()->create([
        'vehicle_type' => VehicleType::CAR
    ]);
    $chassisModule->module->assembly_time = 1;
    $chassisModule->module->save();

    $response = $this->post(route('calendar.store', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]), [
        'module_id' => $chassisModule->module->id,
        'robot_id' => $robot->id,
        'vehicle_id' => $vehicle->id,
        'slot' => ['1' => 'true', '2' => 'true']
    ]);

    $response->assertSessionHasErrors('slot');
    $this->assertEquals(0, VehiclePlanning::count());
});
test('as admin, calendar.destroy deletes planning and redirects', function () {
    $this->actingAs($this->users['admin']);

    $vehicle = Vehicle::factory()->create([
        'user_id' => $this->users['admin']->id,
        'type' => VehicleType::CAR
    ]);
    $robot = Robot::factory()->create();
    $robot->vehicleTypes()->create([
        'vehicle_type' => VehicleType::CAR
    ]);

    $chassisModule = ChassisModule::factory()->create([
        'vehicle_type' => VehicleType::CAR
    ]);
    $chassisModule->module->assembly_time = 1;
    $chassisModule->module->save();

    $schedule = VehiclePlanning::create([
        'vehicle_id' => $vehicle->id,
        'module_id' => $chassisModule->module->id,
        'robot_id' => $robot->id,
        'date' => $this->date,
        'slot_start' => 1,
        'slot_end' => 2
    ]);

    $this->assertEquals(1, VehiclePlanning::count());
    $response = $this->delete(route('calendar.destroy', [$schedule]));

    $response->assertRedirect(route('calendar.show', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]));
    $response->assertSessionHas('success');
    $this->assertEquals(0, VehiclePlanning::count());
});
