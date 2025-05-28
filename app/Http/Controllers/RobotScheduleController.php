<?php
<?php

namespace App\Http\Controllers;

use App\Models\Robot;
use App\Models\RobotSchedule;
use App\Models\Vehicle;
use App\Models\VehiclePlanning;
use App\ModuleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RobotScheduleController extends Controller
{
    /**
     * Display a listing of the robot schedules.
     */
    public function index()
    {
        $schedules = RobotSchedule::with('robot', 'vehiclePlannings.vehicle')
            ->orderBy('date')
            ->orderBy('time_slot')
            ->paginate(20);
            
        return view('robot_schedules.index', compact('schedules'));
    }

    /**
     * Show the form for creating a new robot schedule.
     */
    public function create()
    {
        $robots = Robot::all();
        $vehicles = Vehicle::whereDoesntHave('plannings')->get();
        $timeSlots = [1, 2, 3, 4];
        
        return view('robot_schedules.create', compact('robots', 'vehicles', 'timeSlots'));
    }

    /**
     * Store a newly created robot schedule in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'robot_id' => 'required|exists:robots,id',
            'date' => 'required|date|after_or_equal:today',
            'time_slot' => 'required|integer|min:1|max:4',
            'vehicle_id' => 'required|exists:vehicles,id',
            'module_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Check if the time slot is available
        $existingSchedule = RobotSchedule::where('robot_id', $request->robot_id)
            ->where('date', $request->date)
            ->where('time_slot', $request->time_slot)
            ->first();
            
        if ($existingSchedule) {
            return redirect()->back()
                ->with('error', 'This time slot is already scheduled for the selected robot.')
                ->withInput();
        }
        
        // Create robot schedule
        $schedule = RobotSchedule::create([
            'robot_id' => $request->robot_id,
            'date' => $request->date,
            'time_slot' => $request->time_slot,
        ]);
        
        // Get the vehicle and its module for the selected module type
        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $moduleType = ModuleType::from($request->module_type);
        
        $composition = $vehicle->compositions()
            ->where('module_type', $moduleType)
            ->first();
            
        if (!$composition) {
            return redirect()->back()
                ->with('error', 'No module found for the selected vehicle and module type.')
                ->withInput();
        }
        
        // Create vehicle planning
        VehiclePlanning::create([
            'robot_schedule_id' => $schedule->id,
            'vehicle_id' => $vehicle->id,
            'module_id' => $composition->module_id,
            'module_type' => $moduleType
        ]);
        
        return redirect()->route('robot_schedules.index')
            ->with('success', 'Robot schedule created successfully.');
    }
    
    /**
     * Display the specified robot schedule.
     */
    public function show($id)
    {
        $schedule = RobotSchedule::with('robot', 'vehiclePlannings.vehicle', 'vehiclePlannings.module')
            ->findOrFail($id);
            
        return view('robot_schedules.show', compact('schedule'));
    }
    
    /**
     * Get completion date for a vehicle.
     */
    public function getVehicleCompletionDate($vehicleId)
    {
        $vehicle = Vehicle::with(['compositions', 'plannings.robotSchedule'])->findOrFail($vehicleId);
        
        // Count total modules in vehicle composition
        $totalModules = $vehicle->compositions->count();
        
        // Count scheduled modules
        $scheduledModules = $vehicle->plannings->count();
        
        if ($scheduledModules < $totalModules) {
            return response()->json([
                'complete' => false,
                'message' => 'Vehicle is not fully scheduled yet. ' . 
                            $scheduledModules . ' of ' . $totalModules . ' modules are scheduled.'
            ]);
        }
        
        // Find the last scheduled module installation date
        $lastSchedule = $vehicle->plannings()
            ->join('robot_schedules', 'vehicle_planning.robot_schedule_id', '=', 'robot_schedules.id')
            ->orderBy('robot_schedules.date', 'desc')
            ->orderBy('robot_schedules.time_slot', 'desc')
            ->first();
            
        if (!$lastSchedule) {
            return response()->json([
                'complete' => false,
                'message' => 'No schedule found for this vehicle.'
            ]);
        }
        
        return response()->json([
            'complete' => true,
            'completion_date' => $lastSchedule->date,
            'time_slot' => $lastSchedule->time_slot,
            'message' => 'Vehicle will be completed on ' . $lastSchedule->date . 
                        ' during time slot ' . $lastSchedule->time_slot
        ]);
    }
}
namespace App\Http\Controllers;

use App\Models\Robot;
use App\Models\RobotSchedule;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RobotScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedules = RobotSchedule::with('robot')->get();
        return view('schedules.index', compact('schedules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $robots = Robot::all();
        $vehicles = Vehicle::where('status', 'pending')->get();

        // Get available dates
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->addDays(30)->endOfDay();
        $dates = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }

        // Get available slots
        $slots = [1, 2, 3, 4];

        return view('schedules.create', compact('robots', 'vehicles', 'dates', 'slots'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'robot_id' => 'required|exists:robots,id',
            'date' => 'required|date|after_or_equal:today',
            'slot' => 'required|integer|min:1|max:4',
            'vehicle_id' => 'nullable|exists:vehicles,id',
        ]);

        // Check if the slot is available for this robot and date
        $existingSchedule = RobotSchedule::where([
            'robot_id' => $validated['robot_id'],
            'date' => $validated['date'],
            'slot' => $validated['slot'],
        ])->exists();

        if ($existingSchedule) {
            return back()->withErrors(['slot' => 'This slot is already booked for the selected robot.']);
        }

        // Create schedule
        $schedule = RobotSchedule::create([
            'robot_id' => $validated['robot_id'],
            'date' => $validated['date'],
            'slot' => $validated['slot'],
        ]);

        // If a vehicle is selected, update its status
        if (!empty($validated['vehicle_id'])) {
            $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
            $vehicle->status = 'in_production';
            $vehicle->save();
        }

        return redirect()->route('schedules.index')
            ->with('success', 'Schedule created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RobotSchedule $schedule)
    {
        $schedule->load('robot');
        return view('schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RobotSchedule $schedule)
    {
        $robots = Robot::all();

        // Get available dates
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->addDays(30)->endOfDay();
        $dates = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }

        // Get available slots
        $slots = [1, 2, 3, 4];

        return view('schedules.edit', compact('schedule', 'robots', 'dates', 'slots'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RobotSchedule $schedule)
    {
        $validated = $request->validate([
            'robot_id' => 'required|exists:robots,id',
            'date' => 'required|date|after_or_equal:today',
            'slot' => 'required|integer|min:1|max:4',
        ]);

        // Check if the slot is available for this robot and date (excluding current schedule)
        $existingSchedule = RobotSchedule::where([
            'robot_id' => $validated['robot_id'],
            'date' => $validated['date'],
            'slot' => $validated['slot'],
        ])
        ->where('id', '!=', $schedule->id)
        ->exists();

        if ($existingSchedule) {
            return back()->withErrors(['slot' => 'This slot is already booked for the selected robot.']);
        }

        // Update schedule
        $schedule->update([
            'robot_id' => $validated['robot_id'],
            'date' => $validated['date'],
            'slot' => $validated['slot'],
        ]);

        return redirect()->route('schedules.index')
            ->with('success', 'Schedule updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RobotSchedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('schedules.index')
            ->with('success', 'Schedule deleted successfully.');
    }
}
