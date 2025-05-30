<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Modules\ChassisModule;
use App\Models\Modules\EngineModule;
use App\Models\Modules\SeatingModule;
use App\Models\Modules\SteeringWheelModule;
use App\Models\Modules\WheelSetModule;
use App\ModuleType;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $modules = Module::with([
            'chassisModule',
            'engineModule',
            'seatingModule',
            'steeringWheelModule',
            'wheelSetModule'
        ])->get();

        $moduleTypes = ModuleType::values();

        return view('modules.index', compact('modules', 'moduleTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Display the specified resource.
     */
    public function show(Module $module)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Module $module)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Module $module)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Module $module)
    {
        $moduleName = $module->name;
        $module->chassisModule?->delete();
        $module->engineModule?->delete();
        $module->seatingModule?->delete();
        $module->steeringWheelModule?->delete();
        $module->wheelSetModule?->delete();
        $module->delete();

        return redirect()->route('modules.index')
            ->with('success', "Module '{$moduleName}' was deleted successfully.");
    }
}
