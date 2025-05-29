<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\ModuleType;
use Request;

class ChassisModuleController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $moduleTypes = ModuleType::values();
        return view('modules.chassis.create', compact(
            'moduleTypes'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $request;
        return redirect()->route('modules.index')->with('success', 'Module created successfully.');
    }
}
