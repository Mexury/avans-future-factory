<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicles = Vehicle::where([
            'user_id' => auth()->id()
        ])->get();

        return view('dashboard.index', compact('vehicles'));
    }
}
