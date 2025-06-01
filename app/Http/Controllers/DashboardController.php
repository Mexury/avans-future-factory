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

        $completedVehicles = Vehicle::with('planning.module')
            ->get()
            ->filter(function ($vehicle) {
                $plannings = $vehicle->planning;

                if ($plannings->count() < 4) return false;
                $completedCount = $plannings->filter(fn ($planning) => $planning->isCompleted())->count();

                return $completedCount === $plannings->count();
            });

        return view('dashboard.index', compact('vehicles', 'completedVehicles'));
    }
}
