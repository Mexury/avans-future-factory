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

        $robots = Robot::withCount(['schedules as schedules_on_date_count' => function ($query) use ($date) {
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

        return view('calendar.create', compact(
            'robots',
            'slots',
            'vehicles',
            'year',
            'month',
            'day'
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

        $robotSchedules = RobotSchedule::where([
            'date' => $year . '-' . $month . '-' . $day
        ])->get();

        return view('calendar.show', compact(
            'robotSchedules',
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
    public function update(Request $request, Module $module)
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
