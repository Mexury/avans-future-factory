<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Robot;
use App\Models\Vehicle;
use App\Models\VehiclePlanning;
use App\ModuleType;
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
        $year = $year ?? $now->year;
        $month = $month ?? $now->month;

        if (!is_numeric($month) || $month < 1 || $month > 12) {
            $month = $now->month;
        }

        $month = str_pad($month, 2, '0', STR_PAD_LEFT);

        if (is_null(request()->route('year')) || is_null(request()->route('month'))) {
            return redirect()->route('calendar.index', [
                'year' => $year,
                'month' => $month
            ]);
        }

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

        $date = "$year-$month-$day";

        $robots = Robot::withCount([
            'planning as schedules_on_date_count' => function ($query) use ($date) {
                $query->whereDate('date', $date);
            }
        ])
        ->get()
        ->map(fn($robot) => [
            'id' => $robot->id,
            'name' => $robot->name,
            'is_available' => $robot->schedules_on_date_count < 4,
        ]);

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
        if (!checkdate((int)$month, (int)$day, (int)$year)) {
            abort(404);
        }

        $validated = $request->validate([
            'module_id' => 'required|numeric|exists:modules,id',
            'robot_id' => 'required|numeric|exists:robots,id',
            'vehicle_id' => 'required|numeric|exists:vehicles,id',
            'slot' => 'required|array',
            'slot.*' => 'string|in:true,false'
        ]);

        $existingPlanning = VehiclePlanning::where([
            'vehicle_id' => $validated['vehicle_id'],
            'module_id' => $validated['module_id']
        ])->first();

        if ($existingPlanning) {
            $module = Module::findOrFail($validated['module_id']);
            $moduleType = snakeToSentenceCase($module->type->value);

            return back()->withInput()->withErrors([
                'module_id' => "This vehicle already has a $moduleType module scheduled for assembly."
            ]);
        }

        $module = Module::with([
            'chassisModule',
            'engineModule',
            'seatingModule',
            'steeringWheelModule',
            'wheelSetModule'
        ])->findOrFail($validated['module_id']);

        // Get existing modules
        $existingPlannings = VehiclePlanning::where('vehicle_id', $validated['vehicle_id'])
            ->with([
                'module.chassisModule',
                'module.engineModule',
                'module.seatingModule',
                'module.steeringWheelModule',
                'module.wheelSetModule'
            ])->get();

        $assemblyOrder = [
            ModuleType::CHASSIS,
            ModuleType::ENGINE,
            ModuleType::WHEEL_SET,
            ModuleType::STEERING_WHEEL,
            ModuleType::SEATING
        ];

        $currentModuleIndex = array_search($module->type, $assemblyOrder);

        // Check if all required previous modules exist
        for ($i = 0; $i < $currentModuleIndex; $i++) {
            $requiredType = $assemblyOrder[$i];
            $hasRequiredModule = $existingPlannings->contains(function ($planning) use ($requiredType) {
                return $planning->module->type === $requiredType;
            });

            if (!$hasRequiredModule) {
                $requiredTypeName = snakeToSentenceCase($requiredType->value);
                $currentTypeName = snakeToSentenceCase($module->type->value);

                return back()->withInput()->withErrors([
                    'module_id' => "You must add a $requiredTypeName module before adding a $currentTypeName module."
                ]);
            }
        }

        // When adding a wheel set, check compatibility with the chassis
        if ($module->type === ModuleType::WHEEL_SET) {
            $chassisPlanning = $existingPlannings->first(function($planning) {
                return $planning->module->type === ModuleType::CHASSIS;
            });

            if ($chassisPlanning) {
                $chassisModule = $chassisPlanning->module->chassisModule;
                $isCompatible = $chassisModule->compatibleWheelSetModules()
                    ->contains('id', $module->wheelSetModule->id);

                if (!$isCompatible) {
                    return back()->withInput()->withErrors([
                        'module_id' => "This wheel set is not compatible with the existing chassis. The chassis requires wheels with a quantity of $chassisModule->wheel_quantity."
                    ]);
                }
            }
        }

        // Check if the robot supports the vehicle type
        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        $robot = Robot::findOrFail($validated['robot_id']);

        if (!$robot->supports($vehicle->type)) {
            $vehicleType = ucfirst($vehicle->type->value);

            return back()->withInput()->withErrors([
                'robot_id' => "This robot does not support vehicles with type '$vehicleType'."
            ]);
        }

        // Get the module to check the assembly time
        $module = Module::findOrFail($validated['module_id']);
        $assemblyTime = $module->assembly_time;

        $selectedSlots = [];
        foreach ($validated['slot'] as $slot => $isSelected) {
            if ($isSelected === 'true') $selectedSlots[] = (int)$slot;
        }

        // TODO: is this actually necessary?
        // Sort slots to make sure they are in the correct order
        sort($selectedSlots);

        if (count($selectedSlots) !== $assemblyTime) {
            return back()->withInput()->withErrors([
                'slot' => "This module requires exactly $assemblyTime time slot(s). You selected " . count($selectedSlots) . "."
            ]);
        }

        for ($i = 1; $i < count($selectedSlots); $i++) {
            if ($selectedSlots[$i] !== $selectedSlots[$i-1] + 1) {
                return back()->withInput()->withErrors([
                    'slot' => "Selected slots must be consecutive."
                ]);
            }
        }

        $robotId = $validated['robot_id'];
        $date = "$year-$month-$day";
        $slotStart = $selectedSlots[0];
        $slotEnd = $selectedSlots[count($selectedSlots)-1];

        $conflictingSchedule = VehiclePlanning::where('robot_id', $robotId)
            ->where('date', $date)
            ->where(function($query) use ($slotStart, $slotEnd) {
                $query->where(function($overlapQuery) use ($slotStart, $slotEnd) {
                    $overlapQuery
                        ->whereBetween('slot_start', [$slotStart, $slotEnd])
                        ->orWhereBetween('slot_end', [$slotStart, $slotEnd])
                        // Complete overlap
                        ->orWhere(function($completeOverlapQuery) use ($slotStart, $slotEnd) {
                            $completeOverlapQuery
                                ->where('slot_start', '<=', $slotStart)
                                ->where('slot_end', '>=', $slotEnd);
                        });
                });
            })
            // TODO: try just using >= and <= query
            ->first();

        if ($conflictingSchedule) {
            return back()->withInput()->withErrors([
                'robot_id' => "This robot is already scheduled for slots $conflictingSchedule->slot_start to $conflictingSchedule->slot_end on this date."
            ]);
        }

        VehiclePlanning::create([
            'vehicle_id' => $validated['vehicle_id'],
            'module_id' => $validated['module_id'],
            'robot_id' => $validated['robot_id'],
            'date' => $date,
            'slot_start' => $selectedSlots[0],
            'slot_end' => $slotEnd
        ]);

        return redirect()
            ->route('calendar.show', compact('year', 'month', 'day'))
            ->with('success', 'Schedule created successfully');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $year, string $month, string $day)
    {
        // ValiDATE the date...
        if (!checkdate((int)$month, (int)$day, (int)$year)) {
            abort(404);
        }

        $month = str_pad($month, 2, '0', STR_PAD_LEFT);
        $day = str_pad($day, 2, '0', STR_PAD_LEFT);

        $vehiclePlanning = VehiclePlanning::where([
            'date' => "$year-$month-$day"
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

        return redirect()
            ->route('calendar.show', compact('year', 'month', 'day'))
            ->with('success', "Schedule was deleted successfully.");
    }
}
