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
       //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Module $module)
    {
       //
    }
}
