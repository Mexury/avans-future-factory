<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Modules\ChassisModule;
use App\ModuleType;
use App\SteeringWheelShape;
use App\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SteeringWheelModuleController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $steeringWheelShapes = SteeringWheelShape::values();
        return view('modules.steering_wheel.create', compact(
            'steeringWheelShapes'
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

            'special_adjustments' => 'nullable|string|max:255',
            'shape' => 'required|string|in:' . implode(',', SteeringWheelShape::values()),
        ]);

        $module = Module::create([
            'name' => $validated['name'],
            'type' => ModuleType::STEERING_WHEEL,
            'cost' => $validated['cost'],
            'assembly_time' => $validated['assembly_time']
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('modules/steering_wheel', 'public');
            $module->image = $imagePath;
            $module->save();
        }

        $module->steeringWheelModule()->create([
            'special_adjustments' => $validated['special_adjustments'],
            'shape' => $validated['shape']
        ]);

        return redirect()->route('modules.index')
            ->with('success', 'Module created successfully.');
    }
}
