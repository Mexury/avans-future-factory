<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vehicle;
use App\VehicleType;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicles = Vehicle::all();
        return view('vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = User::where(['role' => 'customer'])->get();
        $vehicleTypes = VehicleType::values();
        return view('vehicles.create', compact('customers', 'vehicleTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:4|max:255',
            'customer_id' => 'required|exists:users,id',
            'type' => 'required|string|in:' . implode(',', VehicleType::values())
        ]);

        Vehicle::create([
            'user_id' => $validated['customer_id'],
            'name' => $validated['name'],
            'type' => $validated['type']
        ]);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
//        // Check if the $vehicle is used in any planning
//        if ($robot->planning()->exists()) {
//            return redirect()->route('robots.index')
//                ->with('error', "Could not delete robot '{$robot->name}'. It is used in vehicle planning.");
//        }

        $vehicleName = $vehicle->name;
        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', "Vehicle '{$vehicleName}' was deleted successfully.");
    }
}
