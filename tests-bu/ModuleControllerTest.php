<?php

use App\Models\Module;
use App\Models\Modules\ChassisModule;
use App\Models\VehiclePlanning;
use App\ModuleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('index displays modules', function () {
    // Create some modules
    $modules = Module::factory()->count(3)->create();

    $response = $this->get(route('modules.index'));

    $response->assertStatus(200);
    $response->assertViewIs('modules.index');
    $response->assertViewHas('modules');
    $response->assertViewHas('moduleTypes');
});

test('create returns 404', function () {
    $response = $this->get(route('modules.create'));
    $response->assertStatus(404);
});

test('store returns 404', function () {
    $response = $this->post(route('modules.store'));
    $response->assertStatus(404);
});

test('show returns 404', function () {
    $module = Module::factory()->create();
    $response = $this->get(route('modules.show', $module));
    $response->assertStatus(404);
});

test('edit returns 404', function () {
    $module = Module::factory()->create();
    $response = $this->get(route('modules.edit', $module));
    $response->assertStatus(404);
});

test('update returns 404', function () {
    $module = Module::factory()->create();
    $response = $this->put(route('modules.update', $module));
    $response->assertStatus(404);
});

test('destroy deletes module and related data', function () {
    // Create a module with chassis submodule
    $module = Module::factory()->create([
        'type' => ModuleType::CHASSIS
    ]);

    $chassisModule = $module->chassisModule()->create([
        'wheel_quantity' => 4,
        'vehicle_type' => 'car',
        'length' => 100,
        'width' => 50,
        'height' => 30
    ]);

    // Create a planning that uses this module
    $planning = VehiclePlanning::create([
        'vehicle_id' => 1,
        'module_id' => $module->id,
        'robot_id' => 1,
        'date' => now(),
        'slot_start' => 0,
        'slot_end' => 1
    ]);

    // Confirm initial state
    $this->assertEquals(1, Module::count());
    $this->assertEquals(1, ChassisModule::count());
    $this->assertEquals(1, VehiclePlanning::count());

    $response = $this->delete(route('modules.destroy', $module));

    $response->assertRedirect(route('modules.index'));
    $response->assertSessionHas('success');

    // Verify all related records are deleted
    $this->assertEquals(0, Module::count());
    $this->assertEquals(0, ChassisModule::count());
    $this->assertEquals(0, VehiclePlanning::count());
});
