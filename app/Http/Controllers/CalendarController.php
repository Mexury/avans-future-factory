<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Modules\ChassisModule;
use App\Models\Modules\EngineModule;
use App\Models\Modules\SeatingModule;
use App\Models\Modules\SteeringWheelModule;
use App\Models\Modules\WheelSetModule;
use App\Models\Robot;
use App\Models\RobotSchedule;
use App\Models\Vehicle;
use App\Models\VehiclePlanning;
use App\Support\Calendar;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(?string $year = null, ?string $month = null)
    {
        $now = Carbon::now();

        // Assign default values if parameters are missing
        $year = $year ?? $now->year;
        $month = $month ?? $now->month;

        // Validate the month parameter
        if (!is_numeric($month) || $month < 1 || $month > 12) {
            $month = $now->month;
        }

        // Ensure month is two digits
        $month = str_pad($month, 2, '0', STR_PAD_LEFT);

        // If the original URL was missing year or month, redirect to the full URL
        if (is_null(request()->route('year')) || is_null(request()->route('month'))) {
            return redirect()->route('calendar.index', [
                'year' => $year,
                'month' => $month
            ]);
        }

        // Proceed with handling the request
        $calendar = Calendar::buildMonth($year, $month);
        return view('calendar.index', compact('calendar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $year, string $month, string $day)
    {
        // Validate the date
        if (!checkdate((int)$month, (int)$day, (int)$year)) {
            abort(404);
        }

        $date = $year . '-' . $month . '-' . $day;

        $robots = Robot::withCount(['planning as schedules_on_date_count' => function ($query) use ($date) {
            $query->whereDate('date', $date);
        }])->get()->map(function ($robot) {
            return [
                'id' => $robot->id,
                'name' => $robot->name,
                'is_available' => $robot->schedules_on_date_count < 4,
            ];
        });

        $vehicles = Vehicle::all();

        $slots = [];
        $startTime = Carbon::createFromTime(9);
        for ($i = 0; $i < 4; $i++) {
            $slotStart = $startTime->copy()->addHours($i * 2);
            $slotEnd = $slotStart->copy()->addHours(2);
            $slots[$i] = [
                'start_time' => $slotStart->format('H:i'),
                'end_time' => $slotEnd->format('H:i')
            ];
        }

        $modules = Module::with([
            'chassisModule',
            'engineModule',
            'seatingModule',
            'steeringWheelModule',
            'wheelSetModule'
        ])->get();

        return view('calendar.create', compact(
            'robots',
            'slots',
            'vehicles',
            'modules',
            'year',
            'month',
            'day'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $year, string $month, string $day)
    {
        // Validate the date
        if (!checkdate((int)$month, (int)$day, (int)$year)) {
            abort(404);
        }

        // First validate basic fields
        $validated = $request->validate([
            'module_id' => 'required|numeric|exists:modules,id',
            'robot_id' => 'required|numeric|exists:robots,id',
            'vehicle_id' => 'required|numeric|exists:vehicles,id',
            'slot' => 'required|array',
            'slot.*' => 'string|in:true,false'
        ]);

        // Check if this module is already planned for this vehicle
        $existingPlanning = VehiclePlanning::where([
            'vehicle_id' => $validated['vehicle_id'],
            'module_id' => $validated['module_id']
        ])->first();

        if ($existingPlanning) {
            $module = Module::findOrFail($validated['module_id']);
            $moduleType = snakeToSentenceCase($module->type->value);

            return back()->withInput()->withErrors([
                'module_id' => "This vehicle already has a {$moduleType} module scheduled for assembly."
            ]);
        }

        // Check if the robot supports the vehicle type
        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        $robot = Robot::findOrFail($validated['robot_id']);

        if (!$robot->supports($vehicle->type)) {
            $vehicleType = ucfirst($vehicle->type->value);

            return back()
                ->withInput()
                ->withErrors([
                    'robot_id' => "This robot does not support vehicles with type '{$vehicleType}'."
                ]);
        }

        // Get the module to check its assembly time
        $module = Module::findOrFail($validated['module_id']);
        $requiredSlots = $module->assembly_time;

        // Check which slots were selected (slot numbers are 1-based)
        $selectedSlots = [];
        foreach ($validated['slot'] as $slotNumber => $isSelected) {
            if ($isSelected === 'true') {
                $selectedSlots[] = (int)$slotNumber;
            }
        }

        // Sort slots to ensure they're in order
        sort($selectedSlots);

        // Validate number of selected slots
        if (count($selectedSlots) !== $requiredSlots) {
            return back()
                ->withInput()
                ->withErrors([
                    'slot' => "This module requires exactly {$requiredSlots} time slot(s). You selected " . count($selectedSlots) . "."
                ]);
        }

        // Validate that slots are consecutive
        for ($i = 1; $i < count($selectedSlots); $i++) {
            if ($selectedSlots[$i] !== $selectedSlots[$i-1] + 1) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'slot' => "Selected slots must be consecutive."
                    ]);
            }
        }

        // Check if the robot is already scheduled for any of the selected slots on this date
        $formattedDate = $year . '-' . $month . '-' . $day;
        $robotId = $validated['robot_id'];

        // Find any existing planning for this robot on this date that overlaps with selected slots
        $existingRobotSchedules = VehiclePlanning::where('robot_id', $robotId)
            ->where('date', $formattedDate)
            ->where(function($query) use ($selectedSlots) {
                $slotStart = min($selectedSlots);
                $slotEnd = max($selectedSlots);

                // Check for any overlap between existing and requested slots
                // Overlap occurs when:
                // - The existing schedule starts during our requested time frame, OR
                // - The existing schedule ends during our requested time frame, OR
                // - The existing schedule completely contains our requested time frame
                $query->where(function($q) use ($slotStart, $slotEnd) {
                    $q->whereBetween('slot_start', [$slotStart, $slotEnd])
                      ->orWhereBetween('slot_end', [$slotStart, $slotEnd])
                      ->orWhere(function($q2) use ($slotStart, $slotEnd) {
                          $q2->where('slot_start', '<=', $slotStart)
                             ->where('slot_end', '>=', $slotEnd);
                      });
                });
            })
            ->first();

        if ($existingRobotSchedules) {
            $robotName = Robot::findOrFail($robotId)->name;
            $conflictSlots = "slots {$existingRobotSchedules->slot_start} to {$existingRobotSchedules->slot_end}";

            return back()
                ->withInput()
                ->withErrors([
                    'robot_id' => "This robot is already scheduled for {$conflictSlots} on this date."
                ]);
        }

        try {
            // Create the vehicle planning entry
            $planning = VehiclePlanning::create([
                'vehicle_id' => $validated['vehicle_id'],
                'module_id' => $validated['module_id'],
                'robot_id' => $validated['robot_id'],
                'date' => $formattedDate,
                'slot_start' => $selectedSlots[0],
                'slot_end' => $selectedSlots[count($selectedSlots) - 1]
            ]);

            return redirect()->route('calendar.show', [
                'year' => $year,
                'month' => $month,
                'day' => $day
            ])->with('success', 'Schedule created successfully');
        } catch (\Illuminate\Database\QueryException $e) {
            // Check if it's a duplicate entry error
            if ($e->errorInfo[1] === 1062) {
                // Get module details for a more informative message
                $module = Module::findOrFail($validated['module_id']);
                $moduleType = ucfirst(str_replace('_', ' ', $module->type->value));

                return back()
                    ->withInput()
                    ->withErrors([
                        'module_id' => "This vehicle already has a {$moduleType} module scheduled for assembly. Each vehicle can only have one of each module type."
                    ]);
            }

            // If it's another database error, rethrow it
            throw $e;
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $year, string $month, string $day)
    {
        // Validate the date
        if (!checkdate((int)$month, (int)$day, (int)$year)) {
            abort(404);
        }

        // Ensure month and day are two digits
        $month = str_pad($month, 2, '0', STR_PAD_LEFT);
        $day = str_pad($day, 2, '0', STR_PAD_LEFT);

        $vehiclePlanning = VehiclePlanning::where([
            'date' => $year . '-' . $month . '-' . $day
        ])->get();

        return view('calendar.show', compact(
            'vehiclePlanning',
            'year',
            'month',
            'day'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Module $module)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VehiclePlanning $schedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehiclePlanning $schedule)
    {
        $date = $schedule->date;
        $year = $date->year;
        $month = $date->month;
        $day = $date->day;

        $schedule->delete();

        return redirect()->route('calendar.show', [$year, $month, $day])
            ->with('success', "Schedule was deleted successfully.");
    }
}
