<?php

use App\Models\Module;
use App\ModuleType;
use App\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function() {
    Storage::fake('public');
});

test('create shows form with vehicle types', function () {
    $response = $this->get(route('modules.chassis.create'));

    $response->assertStatus(200);
    $response->assertViewIs('modules.chassis.create');
    $response->assertViewHas('vehicleTypes');
});

test('store validates required fields', function () {
    $response = $this->post(route('modules.chassis.store'), []);

    $response->assertSessionHasErrors([
        'name', 'cost', 'image',
        'vehicle_type', 'wheel_quantity',
        'length', 'width', 'height'
    ]);
});

test('store creates chassis module successfully', function () {
    $data = [
        'name' => 'Test Chassis',
        'cost' => 5000,
        'assembly_time' => 2,
        'image' => UploadedFile::fake()->image('chassis.jpg'),
        'vehicle_type' => VehicleType::CAR->value,
        'wheel_quantity' => 4,
        'length' => 300,
        'width' => 150,
        'height' => 100,
    ];

    $response = $this->post(route('modules.chassis.store'), $data);

    $response->assertRedirect(route('modules.index'));
    $response->assertSessionHas('success');

    // Verify base module was created
    $this->assertEquals(1, Module::count());
    $module = Module::first();
    $this->assertEquals('Test Chassis', $module->name);
    $this->assertEquals(5000, $module->cost);
    $this->assertEquals(2, $module->assembly_time);
    $this->assertEquals(ModuleType::CHASSIS, $module->type);
    $this->assertNotNull($module->image);

    // Verify image was stored
    Storage::disk('public')->assertExists($module->image);

    // Verify chassis module was created
    $this->assertNotNull($module->chassisModule);
    $this->assertEquals(VehicleType::CAR, $module->chassisModule->vehicle_type);
    $this->assertEquals(4, $module->chassisModule->wheel_quantity);
    $this->assertEquals(300, $module->chassisModule->length);
    $this->assertEquals(150, $module->chassisModule->width);
    $this->assertEquals(100, $module->chassisModule->height);
});

test('store validates image file', function () {
    $data = [
        'name' => 'Test Chassis',
        'cost' => 5000,
        'assembly_time' => 2,
        'image' => 'not-a-file', // Invalid file
        'vehicle_type' => VehicleType::CAR->value,
        'wheel_quantity' => 4,
        'length' => 300,
        'width' => 150,
        'height' => 100,
    ];

    $response = $this->post(route('modules.chassis.store'), $data);

    $response->assertSessionHasErrors('image');
    $this->assertEquals(0, Module::count());
});

test('store validates numeric constraints', function () {
    $data = [
        'name' => 'Test Chassis',
        'cost' => -100, // Invalid negative cost
        'assembly_time' => 10, // Invalid assembly time (> 4)
        'image' => UploadedFile::fake()->image('chassis.jpg'),
        'vehicle_type' => VehicleType::CAR->value,
        'wheel_quantity' => 20, // Invalid wheel quantity (> 16)
        'length' => 1500, // Invalid length (> 1000)
        'width' => 0, // Invalid width (< 1)
        'height' => -10, // Invalid height (negative)
    ];

    $response = $this->post(route('modules.chassis.store'), $data);

    $response->assertSessionHasErrors([
        'cost', 'assembly_time', 'wheel_quantity',
        'length', 'width', 'height'
    ]);
    $this->assertEquals(0, Module::count());
});
