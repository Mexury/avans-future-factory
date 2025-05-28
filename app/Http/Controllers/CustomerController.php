<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customer's vehicles.
     */
    public function myVehicles()
    {
        $vehicles = Vehicle::where('user_id', auth()->id())->get();
        return view('customer.my-vehicles', compact('vehicles'));
    }

    /**
     * Display the specified vehicle details.
     */
    public function show(Vehicle $vehicle)
    {
        // Ensure the customer can only view their own vehicles
        if ($vehicle->user_id !== auth()->id()) {
            abort(403);
        }

        $vehicle->load(['modules', 'planning.robotSchedule']);
        return view('customer.vehicle-details', compact('vehicle'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Display a listing of vehicles for the customer.
     */
    public function index()
    {
        $vehicles = Vehicle::where('user_id', Auth::id())
            ->with(['compositions.module', 'plannings.robotSchedule.robot'])
            ->paginate(10);
            
        return view('customer.index', compact('vehicles'));
    }

    /**
     * Display the specified vehicle details with progress.
     */
    public function show($id)
    {
        $vehicle = Vehicle::where('user_id', Auth::id())
            ->with(['compositions.module', 'plannings.robotSchedule.robot'])
            ->findOrFail($id);
            
        // Calculate progress percentage
        $totalModules = $vehicle->compositions->count();
        $scheduledModules = $vehicle->plannings->count();
        $completedModules = $vehicle->plannings()
            ->whereHas('robotSchedule', function ($query) {
                $query->where('date', '<', now()->format('Y-m-d'))
                    ->orWhere(function ($q) {
                        $q->where('date', now()->format('Y-m-d'))
                            ->where('time_slot', '<', $this->getCurrentTimeSlot());
                    });
            })
            ->count();
            
        $progress = $totalModules > 0 ? round(($completedModules / $totalModules) * 100) : 0;
        $schedulingProgress = $totalModules > 0 ? round(($scheduledModules / $totalModules) * 100) : 0;
        
        // Determine vehicle status
        $status = 'Not scheduled';
        
        if ($scheduledModules > 0) {
            $status = 'In production';
            
            if ($scheduledModules >= $totalModules) {
                if ($completedModules >= $totalModules) {
                    $status = 'Completed';
                } else {
                    $status = 'Fully scheduled';
                }
            }
        }
        
        // Get estimated completion date
        $completionDate = null;
        if ($scheduledModules >= $totalModules) {
            $lastSchedule = $vehicle->plannings()
                ->join('robot_schedules', 'vehicle_planning.robot_schedule_id', '=', 'robot_schedules.id')
                ->orderBy('robot_schedules.date', 'desc')
                ->orderBy('robot_schedules.time_slot', 'desc')
                ->first();
                
            if ($lastSchedule) {
                $completionDate = $lastSchedule->date;
            }
        }
        
        return view('customer.show', compact(
            'vehicle', 
            'progress', 
            'schedulingProgress', 
            'status', 
            'completionDate'
        ));
    }
    
    /**
     * Get the current time slot based on the time of day.
     */
    private function getCurrentTimeSlot()
    {
        $hour = now()->hour;
        
        if ($hour < 10) {
            return 1;
        } elseif ($hour < 12) {
            return 2;
        } elseif ($hour < 14) {
            return 3;
        } else {
            return 4;
        }
    }
}