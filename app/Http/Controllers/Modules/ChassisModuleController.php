<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Modules\ChassisModule;
use App\ModuleType;
use App\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChassisModuleController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vehicleTypes = VehicleType::values();
        return view('modules.chassis.create', compact(
            'vehicleTypes'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'assembly_time' => 'nullable|integer|min:1|max:4',
            'name' => 'required|string|max:255',
            'cost' => 'required|numeric|min:0|max:100000',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',

            'vehicle_type' => 'required|string|in:' . implode(',', VehicleType::values()),
            'wheel_quantity' => 'required|integer|min:1|max:16',
            'length' => 'required|integer|min:1|max:1000',
            'width' => 'required|integer|min:1|max:1000',
            'height' => 'required|integer|min:1|max:1000',
        ]);

        $module = Module::create([
            'name' => $validated['name'],
            'type' => ModuleType::CHASSIS,
            'cost' => $validated['cost'],
            'assembly_time' => $validated['assembly_time']
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('modules/chassis', 'public');
            $module->image = $imagePath;
            $module->save();
        }

        $module->chassisModule()->create([
            'vehicle_type' => $validated['vehicle_type'],
            'wheel_quantity' => $validated['wheel_quantity'],
            'length' => $validated['length'],
            'width' => $validated['width'],
            'height' => $validated['height'],
        ]);

        return redirect()->route('modules.index')
            ->with('success', 'Module created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Module $module)
    {
        if ($module->type !== ModuleType::CHASSIS) {
            return redirect()->route('modules.index')
                ->with('error', 'Invalid module type.');
        }

        try {
            // Delete the associated chassis module using the relationship
            if ($module->chassisModule) {
                $module->chassisModule->delete();
            }

            // Delete the image if it exists
            if ($module->image) {
                Storage::disk('public')->delete($module->image);
            }

            // Delete the base module
            $module->delete();

            return redirect()->route('modules.index')
                ->with('success', 'Chassis module deleted successfully.');

        } catch (\Exception $e) {
            \Log::error('Failed to delete chassis module: ' . $e->getMessage());

            return redirect()->route('modules.index')
                ->with('error', 'Failed to delete chassis module. Please try again.');
        }
    }
}
