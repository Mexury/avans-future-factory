<?php
<?php

namespace App\Http\Controllers;

use App\Models\Robot;
use App\Models\RobotSchedule;
use App\Models\Vehicle;
use App\Models\VehiclePlanning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehiclePlanningController extends Controller
{
    /**
     * Display a listing of vehicle plannings.
     */
    public function index()
    {
        $plannings = VehiclePlanning::with('vehicle', 'module', 'robotSchedule.robot')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('vehicle_plannings.index', compact('plannings'));
    }

    /**
     * Show the form for creating a new vehicle planning.
     */
    public function create()
    {
        $robots = Robot::all();
        $vehicles = Vehicle::has('compositions')
            ->whereDoesntHave('plannings', function ($query) {
                $query->whereHas('robotSchedule', function ($q) {
                    $q->where('date', '>=', now()->format('Y-m-d'));
                });
            })
            ->get();
        
        return view('vehicle_plannings.create', compact('robots', 'vehicles'));
    }

    /**
     * Display the specified vehicle planning.
     */
    public function show($id)
    {
        $vehicle = Vehicle::with(['compositions.module', 'plannings.robotSchedule.robot'])
            ->findOrFail($id);
            
        // Group scheduled modules by date
        $scheduledModules = $vehicle->plannings()
            ->join('robot_schedules', 'vehicle_planning.robot_schedule_id', '=', 'robot_schedules.id')
            ->join('modules', 'vehicle_planning.module_id', '=', 'modules.id')
            ->join('robots', 'robot_schedules.robot_id', '=', 'robots.id')
            ->select('vehicle_planning.*', 'robot_schedules.date', 'robot_schedules.time_slot', 
                    'modules.name as module_name', 'robots.name as robot_name')
            ->orderBy('robot_schedules.date')
            ->orderBy('robot_schedules.time_slot')
            ->get()
            ->groupBy('date');
            
        // Get unscheduled modules
        $scheduledModuleIds = $vehicle->plannings->pluck('module_id')->toArray();
        $unscheduledModules = $vehicle->compositions()
            ->whereNotIn('module_id', $scheduledModuleIds)
            ->with('module')
            ->get();
            
        return view('vehicle_plannings.show', compact('vehicle', 'scheduledModules', 'unscheduledModules'));
    }

    /**
     * Show the form for planning a specific module.
     */
    public function planModule($vehicleId, $moduleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        $composition = $vehicle->compositions()
            ->where('module_id', $moduleId)
            ->with('module')
            ->firstOrFail();
            
        // Get available robots for this module type
        $robots = Robot::whereHas('vehicleTypes', function ($query) use ($vehicle) {
            $query->whereHas('compositions', function ($q) use ($vehicle) {
                $q->where('vehicle_id', $vehicle->id);
            });
        })->get();
        
        // Get available dates (next 5 days)
        $dates = [];
        $currentDate = now();
        for ($i = 0; $i < 5; $i++) {
            $dates[] = $currentDate->copy()->addDays($i)->format('Y-m-d');
        }
        
        $timeSlots = [1, 2, 3, 4];
        
        return view('vehicle_plannings.plan_module', compact(
            'vehicle', 
            'composition', 
            'robots', 
            'dates', 
            'timeSlots'
        ));
    }

    /**
     * Store a planned module in storage.
     */
    public function storePlannedModule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|exists:vehicles,id',
            'module_id' => 'required|exists:modules,id',
            'robot_id' => 'required|exists:robots,id',
            'date' => 'required|date|after_or_equal:today',
            'time_slot' => 'required|integer|min:1|max:4',
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
        
        // Get the composition to determine module type
        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $composition = $vehicle->compositions()
            ->where('module_id', $request->module_id)
            ->firstOrFail();
            
        // Create robot schedule
        $schedule = RobotSchedule::create([
            'robot_id' => $request->robot_id,
            'date' => $request->date,
            'time_slot' => $request->time_slot,
        ]);
        
        // Create vehicle planning
        VehiclePlanning::create([
            'robot_schedule_id' => $schedule->id,
            'vehicle_id' => $request->vehicle_id,
            'module_id' => $request->module_id,
            'module_type' => $composition->module_type
        ]);
        
        return redirect()->route('vehicle_plannings.show', $request->vehicle_id)
            ->with('success', 'Module scheduled successfully.');
    }
}
namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Robot;
use App\Models\RobotSchedule;
use App\Models\Vehicle;
use App\Models\VehiclePlanning;
use App\ModuleType;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VehiclePlanningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plannings = VehiclePlanning::with(['vehicle', 'module', 'robotSchedule'])->get();
        return view('planning.index', compact('plannings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vehicles = Vehicle::where('status', 'pending')->get();
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

        return view('planning.create', compact('vehicles', 'robots', 'dates', 'slots'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'robot_id' => 'required|exists:robots,id',
            'date' => 'required|date|after_or_equal:today',
            'slot' => 'required|integer|min:1|max:4',
            'module_id' => 'required|exists:modules,id',
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

        // Get module type
        $module = Module::findOrFail($validated['module_id']);

        // Create planning
        VehiclePlanning::create([
            'robot_schedule_id' => $schedule->id,
            'vehicle_id' => $validated['vehicle_id'],
            'module_id' => $validated['module_id'],
            'module_type' => $module->type,
        ]);

        // Update vehicle status
        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        $vehicle->status = 'in_production';
        $vehicle->save();

        return redirect()->route('planning.index')
            ->with('success', 'Vehicle planning created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(VehiclePlanning $planning)
    {
        $planning->load(['vehicle', 'module', 'robotSchedule']);
        return view('planning.show', compact('planning'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehiclePlanning $planning)
    {
        $planning->load(['vehicle', 'module', 'robotSchedule']);
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

        return view('planning.edit', compact('planning', 'robots', 'dates', 'slots'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VehiclePlanning $planning)
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
        ->where('id', '!=', $planning->robot_schedule_id)
        ->exists();

        if ($existingSchedule) {
            return back()->withErrors(['slot' => 'This slot is already booked for the selected robot.']);
        }

        // Update schedule
        $schedule = RobotSchedule::findOrFail($planning->robot_schedule_id);
        $schedule->update([
            'robot_id' => $validated['robot_id'],
            'date' => $validated['date'],
            'slot' => $validated['slot'],
        ]);

        return redirect()->route('planning.index')
            ->with('success', 'Vehicle planning updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehiclePlanning $planning)
    {
        // Delete the schedule
        RobotSchedule::findOrFail($planning->robot_schedule_id)->delete();

        // The planning will be deleted via cascade delete defined in the migration

        return redirect()->route('planning.index')
            ->with('success', 'Vehicle planning deleted successfully.');
    }
}
