<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\ModuleType;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $modules = Module::all();
        return view('modules.index', compact('modules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $moduleTypes = ModuleType::cases();
        return view('modules.create', compact('moduleTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'assembly_time' => 'required|integer|min:1',
            'cost' => 'required|numeric|min:0',
            'image' => 'required|image|max:2048',
            // Additional validation rules for specific module types
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('modules', 'public');
            $validated['image'] = $path;
        }

        $module = Module::create($validated);

        // Handle specific module type data
        switch ($validated['type']) {
            case ModuleType::CHASSIS->value:
                // Create chassis module
                break;
            case ModuleType::ENGINE->value:
                // Create engine module
                break;
            // Handle other module types
        }

        return redirect()->route('modules.index')
            ->with('success', 'Module created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Module $module)
    {
        return view('modules.show', compact('module'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Module $module)
    {
        $moduleTypes = ModuleType::cases();
        return view('modules.edit', compact('module', 'moduleTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Module $module)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'assembly_time' => 'required|integer|min:1',
            'cost' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            // Additional validation rules for specific module types
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('modules', 'public');
            $validated['image'] = $path;
        }

        $module->update($validated);

        // Update specific module type data
        switch ($module->type->value) {
            case ModuleType::CHASSIS->value:
                // Update chassis module
                break;
            case ModuleType::ENGINE->value:
                // Update engine module
                break;
            // Handle other module types
        }

        return redirect()->route('modules.index')
            ->with('success', 'Module updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Module $module)
    {
        $module->delete();
        return redirect()->route('modules.index')
            ->with('success', 'Module deleted successfully.');
    }
}
