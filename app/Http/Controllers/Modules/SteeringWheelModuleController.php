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
