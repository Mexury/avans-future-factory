<?php

use App\Models\Module;
use App\ModuleType;
use App\UpholsteryType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function() {
    Storage::fake('public');
});

test('create shows form with upholstery types', function () {
    $response = $this->get(route('modules.seating.create'));

    $response->assertStatus(200);
    $response->assertViewIs('modules.seating.create');
    $response->assertViewHas('upholsteryTypes');
});

test('store validates required fields', function () {
    $response = $this->post(route('modules.seating.store'), []);

    $response->assertSessionHasErrors([
        'name', 'cost', 'image',
        'quantity', 'upholstery'
    ]);
});

test('store creates seating module successfully', function () {
    $data = [
        'name' => 'Luxury Seats',
        'cost' => 3500,
        'assembly_time' => 1,
        'image' => UploadedFile::fake()->image('seats.jpg'),
        'quantity' => 5,
        'upholstery' => UpholsteryType::LEATHER->value,
    ];

    $response = $this->post(route('modules.seating.store'), $data);

    $response->assertRedirect(route('modules.index'));
    $response->assertSessionHas('success');

    // Verify base module was created
    $this->assertEquals(1, Module::count());
    $module = Module::first();
    $this->assertEquals('Luxury Seats', $module->name);
    $this->assertEquals(3500, $module->cost);
    $this->assertEquals(1, $module->assembly_time);
    $this->assertEquals(ModuleType::SEATING, $module->type);
    $this->assertNotNull($module->image);

    // Verify image was stored
    Storage::disk('public')->assertExists($module->image);

    // Verify seating module was created
    $this->assertNotNull($module->seatingModule);
    $this->assertEquals(5, $module->seatingModule->quantity);
    $this->assertEquals(UpholsteryType::LEATHER, $module->seatingModule->upholstery);
});

test('store validates image file', function () {
    $data = [
        'name' => 'Luxury Seats',
        'cost' => 3500,
        'assembly_time' => 1,
        'image' => 'not-a-file', // Invalid file
        'quantity' => 5,
        'upholstery' => UpholsteryType::LEATHER->value,
    ];

    $response = $this->post(route('modules.seating.store'), $data);

    $response->assertSessionHasErrors('image');
    $this->assertEquals(0, Module::count());
});

test('store validates numeric constraints', function () {
    $data = [
        'name' => 'Luxury Seats',
        'cost' => -1000, // Invalid negative cost
        'assembly_time' => 5, // Invalid assembly time (> 4)
        'image' => UploadedFile::fake()->image('seats.jpg'),
        'quantity' => 0, // Invalid quantity (< 1)
        'upholstery' => UpholsteryType::LEATHER->value,
    ];

    $response = $this->post(route('modules.seating.store'), $data);

    $response->assertSessionHasErrors([
        'cost', 'assembly_time', 'quantity'
    ]);
    $this->assertEquals(0, Module::count());
});

test('store validates upholstery type', function () {
    $data = [
        'name' => 'Luxury Seats',
        'cost' => 3500,
        'assembly_time' => 1,
        'image' => UploadedFile::fake()->image('seats.jpg'),
        'quantity' => 5,
        'upholstery' => 'invalid-upholstery', // Invalid upholstery type
    ];

    $response = $this->post(route('modules.seating.store'), $data);

    $response->assertSessionHasErrors('upholstery');
    $this->assertEquals(0, Module::count());
});
