<?php

namespace App\Http\Controllers;

use App\EngineType;
use App\Models\Robot;
use App\Models\RobotVehicleType;
use App\Models\RobotEngineType;
use App\VehicleType;
use Illuminate\Http\Request;

class RobotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $robots = Robot::all();
        return view('robots.index', compact('robots'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vehicleTypes = VehicleType::values();
        $engineTypes = EngineType::values();
        return view('robots.create', compact('vehicleTypes', 'engineTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:robots,name|string|min:4|max:255',
        ]);

        // Extract selected vehicle types
        $vehicleTypeSelections = $request->input('vehicle_type', []);
        $selectedVehicleTypes = [];

        foreach ($vehicleTypeSelections as $type => $value) {
            if ($value === 'true') {
                $selectedVehicleTypes[] = $type;
            }
        }

        // Extract selected engine types
        $engineTypeSelections = $request->input('engine_type', []);
        $selectedEngineTypes = [];

        foreach ($engineTypeSelections as $type => $value) {
            if ($value === 'true') {
                $selectedEngineTypes[] = $type;
            }
        }

        // Check if either vehicle types or engine types are selected
        if (empty($selectedVehicleTypes) && empty($selectedEngineTypes)) {
            return back()
                ->withInput()
                ->withErrors([
                    'selection_error' => 'You must select at least one vehicle type or one engine type.'
                ]);
        }

        // Validate that all selected vehicle types are valid
        if (!empty($selectedVehicleTypes)) {
            if (array_any($selectedVehicleTypes, fn($type) => !in_array($type, VehicleType::values()))) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'vehicle_type' => 'One or more selected vehicle types are invalid.'
                    ]);
            }
        }

        // Validate that all selected engine types are valid
        if (!empty($selectedEngineTypes)) {
            if (array_any($selectedEngineTypes, fn($type) => !in_array($type, EngineType::values()))) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'engine_type' => 'One or more selected engine types are invalid.'
                    ]);
            }
        }

        // Create the robot
        $robot = Robot::create([
            'name' => $validated['name']
        ]);

        // Create the vehicle type associations
        foreach ($selectedVehicleTypes as $vehicleType) {
            RobotVehicleType::create([
                'robot_id' => $robot->id,
                'vehicle_type' => $vehicleType
            ]);
        }

        // Create the engine type associations
        foreach ($selectedEngineTypes as $engineType) {
            RobotEngineType::create([
                'robot_id' => $robot->id,
                'engine_type' => $engineType
            ]);
        }

        return redirect()->route('robots.index')->with('success', 'Robot created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Robot $robot)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Robot $robot)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Robot $robot)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Robot $robot)
    {
        // Check if the robot is used in any planning
        if ($robot->planning()->exists()) {
            return redirect()->route('robots.index')
                ->with('error', "Could not delete robot '{$robot->name}'. It is used in vehicle planning.");
        }

        $robot->vehicleTypes()->delete();
        $robot->engineTypes()->delete();
        $robotName = $robot->name;
        $robot->delete();

        return redirect()->route('robots.index')
            ->with('success', "Robot '{$robotName}' was deleted successfully.");
    }
}
