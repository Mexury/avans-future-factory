<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Modules\ChassisModule;
use App\ModuleType;
use App\VehicleType;
use App\WheelType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WheelSetModuleController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $wheelTypes = WheelType::values();
        return view('modules.wheel_set.create', compact(
            'wheelTypes'
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

            'type' => 'required|string|in:' . implode(',', WheelType::values()),
            'diameter' => 'required|integer|min:1|max:100',
            'wheel_quantity' => 'required|integer|min:2'
        ]);

        $module = Module::create([
            'name' => $validated['name'],
            'type' => ModuleType::WHEEL_SET,
            'cost' => $validated['cost'],
            'assembly_time' => $validated['assembly_time']
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('modules/wheel_set', 'public');
            $module->image = $imagePath;
            $module->save();
        }

        $module->wheelSetModule()->create([
            'type' => $validated['type'],
            'diameter' => $validated['diameter'],
            'wheel_quantity' => $validated['wheel_quantity']
        ]);

        return redirect()->route('modules.index')
            ->with('success', 'Module created successfully.');
    }
}
