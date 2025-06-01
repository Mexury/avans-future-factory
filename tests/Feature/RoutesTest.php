<?php

use App\EngineType;
use App\Models\Module;
use App\Models\Modules\ChassisModule;
use App\Models\Robot;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehiclePlanning;
use App\ModuleType;
use App\UserRole;
use App\VehicleType;
beforeEach(function () {
    $this->adminUser = User::factory()->create([
        'role' => UserRole::ADMIN,
    ]);

    $this->plannerUser = User::factory()->create([
        'role' => UserRole::PLANNER,
    ]);

    $this->mechanicUser = User::factory()->create([
        'role' => UserRole::MECHANIC,
    ]);

    $this->customerUser = User::factory()->create([
        'role' => UserRole::CUSTOMER,
    ]);

    $this->buyerUser = User::factory()->create([
        'role' => UserRole::BUYER,
    ]);

    $this->year = '2025';
    $this->month = '01';
    $this->day = '01';
});

//Calendar routes
test('as admin, calendar.index, calendar.show, calendar.create is accessible', function () {
    $this->actingAs($this->adminUser);

    $responseIndex = $this->get(route('calendar.index', [
        'year' => $this->year,
        'month' => $this->month
    ]));
    $responseIndex->assertOk();

    $responseShow = $this->get(route('calendar.show', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]));
    $responseShow->assertOk();

    $responseCreate = $this->get(route('calendar.create', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]));
    $responseCreate->assertOk();
});

test('as planner, calendar.index, calendar.show, calendar.create is accessible', function () {
    $this->actingAs($this->plannerUser);

    $responseIndex = $this->get(route('calendar.index', [
        'year' => $this->year,
        'month' => $this->month
    ]));
    $responseIndex->assertOk();

    $responseShow = $this->get(route('calendar.show', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]));
    $responseShow->assertOk();

    $responseCreate = $this->get(route('calendar.create', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]));
    $responseCreate->assertOk();
});

test('as mechanic, calendar.index, calendar.show, calendar.create is accessible', function () {
    $this->actingAs($this->mechanicUser);

    $responseIndex = $this->get(route('calendar.index', [
        'year' => $this->year,
        'month' => $this->month
    ]));
    $responseIndex->assertOk();

    $responseShow = $this->get(route('calendar.show', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]));
    $responseShow->assertOk();

    $responseCreate = $this->get(route('calendar.create', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]));
    $responseCreate->assertOk();
});

test('as customer, calendar.index, calendar.show, calendar.create is not accessible', function () {
    $this->actingAs($this->customerUser);

    $responseIndex = $this->get(route('calendar.index', [
        'year' => $this->year,
        'month' => $this->month
    ]));
    $responseIndex->assertStatus(403);

    $responseShow = $this->get(route('calendar.show', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]));
    $responseShow->assertStatus(403);

    $responseCreate = $this->get(route('calendar.create', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]));
    $responseCreate->assertStatus(403);
});

test('as planner, I can create planning for vehicle', function () {
    $this->actingAs($this->plannerUser);

    $chassisModule = ChassisModule::factory()->create([
        'vehicle_type' => VehicleType::CAR,
    ]);
    $chassisModule->module->assembly_time = 1;
    $chassisModule->module->save();

    $robot = Robot::factory()->create();
    $robot->vehicleTypes()->create([
        'vehicle_type' => VehicleType::CAR,
    ]);

    $vehicle = Vehicle::factory()->create([
        'user_id' => $this->customerUser->id,
        'type' => VehicleType::CAR,
    ]);

    $response = $this->post(route('calendar.store', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]), [
        'module_id' => $chassisModule->module->id,
        'robot_id' => $robot->id,
        'vehicle_id' => $vehicle->id,
        'slot' => ['1' => 'true']
    ]);

    $response->assertRedirect(route('calendar.show', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]));
});

test('as planner, I can delete planning for vehicle', function () {
    $this->actingAs($this->plannerUser);

    $chassisModule = ChassisModule::factory()->create([
        'vehicle_type' => VehicleType::CAR,
    ]);
    $chassisModule->module->assembly_time = 1;
    $chassisModule->module->save();

    $robot = Robot::factory()->create();
    $robot->vehicleTypes()->create([
        'vehicle_type' => VehicleType::CAR,
    ]);

    $vehicle = Vehicle::factory()->create([
        'user_id' => $this->customerUser->id,
        'type' => VehicleType::CAR,
    ]);

    $response = $this->post(route('calendar.store', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]), [
        'module_id' => $chassisModule->module->id,
        'robot_id' => $robot->id,
        'vehicle_id' => $vehicle->id,
        'slot' => ['1' => 'true']
    ]);

    $response->assertRedirect(route('calendar.show', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]));

    $vehiclePlanning = VehiclePlanning::first();

    $responseDelete = $this->delete(route('calendar.destroy', [
        'schedule' => $vehiclePlanning->id
    ]));

    $responseDelete->assertRedirect(route('calendar.show', [
        'year' => $this->year,
        'month' => $this->month,
        'day' => $this->day
    ]));
    $this->assertEquals(0, VehiclePlanning::count());
});

//Module routes
test('as buyer, chassis.create, engine.create, seating.create, steering_wheel.create, wheel_set.create is accessible', function () {
    $this->actingAs($this->buyerUser);

    // Test for Chassis creation
    $responseChassis = $this->get(route('chassis.create'));
    $responseChassis->assertOk();
    $responseChassis->assertViewIs('modules.chassis.create');

    // Test for Engine creation
    $responseEngine = $this->get(route('engine.create'));
    $responseEngine->assertOk();
    $responseEngine->assertViewIs('modules.engine.create');

    // Test for Seating creation
    $responseSeating = $this->get(route('seating.create'));
    $responseSeating->assertOk();
    $responseSeating->assertViewIs('modules.seating.create');

    // Test for Steering Wheel creation
    $responseSteeringWheel = $this->get(route('steering_wheel.create'));
    $responseSteeringWheel->assertOk();
    $responseSteeringWheel->assertViewIs('modules.steering_wheel.create');

    // Test for Wheel Set creation
    $responseWheelSet = $this->get(route('wheel_set.create'));
    $responseWheelSet->assertOk();
    $responseWheelSet->assertViewIs('modules.wheel_set.create');
});

test('as mechanic, I can create a vehicle', function () {
    $this->actingAs($this->mechanicUser);


    // Define params
    $vehicleData = [
        'name' => 'Test Vehicle',
        'user_id' => $this->customerUser->id,
        'type' => VehicleType::CAR
    ];

    // post req
    $response = $this->post(route('vehicles.store', $vehicleData));

    $response->assertRedirect(route('vehicles.index'));
    $response->assertSessionHas('success', 'Vehicle created successfully.');

    $this->assertDatabaseHas('vehicles', [
        'name' => 'Test Vehicle',
        'user_id' => $this->customerUser->id,
        'type' => VehicleType::CAR
    ]);
});

test('as mechanic, I can create a robot', function () {
    $this->actingAs($this->mechanicUser);

    // post req
    $response = $this->post(route('robots.store', [
        'name' => 'Test Robot',
        'vehicle_type' => ['car' => 'true'],
        'engine_type' => ['electric' => 'true'],
    ]));

    dump($response);

    $response->assertRedirect(route('robots.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('robots', [
        'name' => 'Test Robot'
    ]);
});

