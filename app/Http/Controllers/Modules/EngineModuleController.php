<?php

namespace App\Http\Controllers\Modules;

use App\EngineType;
use App\Http\Controllers\Controller;
use App\Models\Module;
use App\ModuleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EngineModuleController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $engineTypes = EngineType::values();
        return view('modules.engine.create', compact(
            'engineTypes'
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

            'type' => 'required|string|in:' . implode(',', EngineType::values()),
            'horse_power' => 'required|integer|min:1',
        ]);

        $module = Module::create([
            'name' => $validated['name'],
            'type' => ModuleType::ENGINE,
            'cost' => $validated['cost'],
            'assembly_time' => $validated['assembly_time']
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('modules/engine', 'public');
            $module->image = $imagePath;
            $module->save();
        }

        $module->engineModule()->create([
            'type' => $validated['type'],
            'horse_power' => $validated['horse_power']
        ]);

        return redirect()->route('modules.index')
            ->with('success', 'Module created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Module $module)
    {
       //
    }
}
