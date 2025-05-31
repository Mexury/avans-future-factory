<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Modules\ChassisModule;
use App\ModuleType;
use App\UpholsteryType;
use App\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SeatingModuleController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $upholsteryTypes = UpholsteryType::values();
        return view('modules.seating.create', compact(
            'upholsteryTypes'
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

            'quantity' => 'required|integer|min:1',
            'upholstery' => 'required|string|in:' . implode(',', UpholsteryType::values()),
        ]);

        $module = Module::create([
            'name' => $validated['name'],
            'type' => ModuleType::SEATING,
            'cost' => $validated['cost'],
            'assembly_time' => $validated['assembly_time']
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('modules/seating', 'public');
            $module->image = $imagePath;
            $module->save();
        }

        $module->seatingModule()->create([
            'quantity' => $validated['quantity'],
            'upholstery' => $validated['upholstery']
        ]);

        return redirect()->route('modules.index')
            ->with('success', 'Module created successfully.');
    }
}
