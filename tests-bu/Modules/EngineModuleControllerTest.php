<?php

use App\EngineType;
use App\Models\Module;
use App\ModuleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function() {
    Storage::fake('public');
});

test('create shows form with engine types', function () {
    $response = $this->get(route('modules.engine.create'));

    $response->assertStatus(200);
    $response->assertViewIs('modules.engine.create');
    $response->assertViewHas('engineTypes');
});

test('store validates required fields', function () {
    $response = $this->post(route('modules.engine.store'), []);

    $response->assertSessionHasErrors([
        'name', 'cost', 'image',
        'type', 'horse_power'
    ]);
});

test('store creates engine module successfully', function () {
    $data = [
        'name' => 'Test Engine',
        'cost' => 8000,
        'assembly_time' => 3,
        'image' => UploadedFile::fake()->image('engine.jpg'),
        'type' => EngineType::PETROL->value,
        'horse_power' => 250,
    ];

    $response = $this->post(route('modules.engine.store'), $data);

    $response->assertRedirect(route('modules.index'));
    $response->assertSessionHas('success');

    // Verify base module was created
    $this->assertEquals(1, Module::count());
    $module = Module::first();
    $this->assertEquals('Test Engine', $module->name);
    $this->assertEquals(8000, $module->cost);
    $this->assertEquals(3, $module->assembly_time);
    $this->assertEquals(ModuleType::ENGINE, $module->type);
    $this->assertNotNull($module->image);

    // Verify image was stored
    Storage::disk('public')->assertExists($module->image);

    // Verify engine module was created
    $this->assertNotNull($module->engineModule);
    $this->assertEquals(EngineType::PETROL, $module->engineModule->type);
    $this->assertEquals(250, $module->engineModule->horse_power);
});

test('store validates image file', function () {
    $data = [
        'name' => 'Test Engine',
        'cost' => 8000,
        'assembly_time' => 3,
        'image' => 'not-a-file', // Invalid file
        'type' => EngineType::PETROL->value,
        'horse_power' => 250,
    ];

    $response = $this->post(route('modules.engine.store'), $data);

    $response->assertSessionHasErrors('image');
    $this->assertEquals(0, Module::count());
});

test('store validates numeric constraints', function () {
    $data = [
        'name' => 'Test Engine',
        'cost' => 200000, // Invalid cost (> 100000)
        'assembly_time' => 0, // Invalid assembly time (< 1)
        'image' => UploadedFile::fake()->image('engine.jpg'),
        'type' => EngineType::PETROL->value,
        'horse_power' => 0, // Invalid horse power (< 1)
    ];

    $response = $this->post(route('modules.engine.store'), $data);

    $response->assertSessionHasErrors([
        'cost', 'assembly_time', 'horse_power'
    ]);
    $this->assertEquals(0, Module::count());
});

test('store validates engine type', function () {
    $data = [
        'name' => 'Test Engine',
        'cost' => 8000,
        'assembly_time' => 3,
        'image' => UploadedFile::fake()->image('engine.jpg'),
        'type' => 'invalid-type', // Invalid engine type
        'horse_power' => 250,
    ];

    $response = $this->post(route('modules.engine.store'), $data);

    $response->assertSessionHasErrors('type');
    $this->assertEquals(0, Module::count());
});
