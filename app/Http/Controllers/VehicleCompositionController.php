<?php
<?php

namespace App\Http\Controllers;

use App\ModuleType;
use App\Models\Module;
use App\Models\Vehicle;
use App\Models\VehicleComposition;
use App\Models\Modules\ChassisModule;
use App\Models\Modules\EngineModule;
use App\Models\Modules\WheelSetModule;
use App\Models\Modules\SteeringWheelModule;
use App\Models\Modules\SeatingModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VehicleCompositionController extends Controller
{
    /**
     * Display a listing of vehicle compositions.
     */
    public function index()
    {
        $compositions = VehicleComposition::with('vehicle', 'modules')->paginate(10);
        return view('vehicle_compositions.index', compact('compositions'));
    }

    /**
     * Show the form for creating a new vehicle composition.
     */
    public function create()
    {
        $chassisModules = Module::where('type', ModuleType::CHASSIS)->get();
        $engineModules = Module::where('type', ModuleType::ENGINE)->get();
        $wheelSetModules = Module::where('type', ModuleType::WHEEL_SET)->get();
        $steeringWheelModules = Module::where('type', ModuleType::STEERING_WHEEL)->get();
        $seatingModules = Module::where('type', ModuleType::SEATING)->get();

        return view('vehicle_compositions.create', compact(
            'chassisModules',
            'engineModules',
            'wheelSetModules',
            'steeringWheelModules',
            'seatingModules'
        ));
    }

    /**
     * Store a newly created vehicle composition in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'chassis_id' => 'required|exists:modules,id',
            'engine_id' => 'required|exists:modules,id',
            'wheel_set_id' => 'required|exists:modules,id',
            'steering_wheel_id' => 'required|exists:modules,id',
            'seating_id' => 'nullable|exists:modules,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create vehicle
        $vehicle = Vehicle::create([
            'name' => $request->name,
            'user_id' => Auth::id()
        ]);

        // Attach modules to vehicle composition
        $chassisModule = Module::find($request->chassis_id);
        $engineModule = Module::find($request->engine_id);
        $wheelSetModule = Module::find($request->wheel_set_id);
        $steeringWheelModule = Module::find($request->steering_wheel_id);

        // Create composition records
        VehicleComposition::create([
            'vehicle_id' => $vehicle->id,
            'module_id' => $chassisModule->id,
            'module_type' => ModuleType::CHASSIS,
            'installation_order' => 1
        ]);

        VehicleComposition::create([
            'vehicle_id' => $vehicle->id,
            'module_id' => $engineModule->id,
            'module_type' => ModuleType::ENGINE,
            'installation_order' => 2
        ]);

        VehicleComposition::create([
            'vehicle_id' => $vehicle->id,
            'module_id' => $wheelSetModule->id,
            'module_type' => ModuleType::WHEEL_SET,
            'installation_order' => 3
        ]);

        VehicleComposition::create([
            'vehicle_id' => $vehicle->id,
            'module_id' => $steeringWheelModule->id,
            'module_type' => ModuleType::STEERING_WHEEL,
            'installation_order' => 4
        ]);

        // Add seating module if provided
        if ($request->seating_id) {
            $seatingModule = Module::find($request->seating_id);
            VehicleComposition::create([
                'vehicle_id' => $vehicle->id,
                'module_id' => $seatingModule->id,
                'module_type' => ModuleType::SEATING,
                'installation_order' => 5
            ]);
        }

        return redirect()->route('vehicle_compositions.show', $vehicle->id)
            ->with('success', 'Vehicle composition created successfully.');
    }

    /**
     * Display the specified vehicle composition.
     */
    public function show($id)
    {
        $vehicle = Vehicle::with(['compositions.module'])->findOrFail($id);
        $totalCost = $vehicle->compositions->sum(function ($composition) {
            return $composition->module->cost;
        });

        return view('vehicle_compositions.show', compact('vehicle', 'totalCost'));
    }

    /**
     * Check compatibility between selected modules
     */
    public function checkCompatibility(Request $request)
    {
        $chassisId = $request->input('chassis_id');
        $wheelSetId = $request->input('wheel_set_id');

        $chassisModule = ChassisModule::whereHas('module', function ($query) use ($chassisId) {
            $query->where('id', $chassisId);
        })->first();

        $wheelSetModule = WheelSetModule::whereHas('module', function ($query) use ($wheelSetId) {
            $query->where('id', $wheelSetId);
        })->first();

        if (!$chassisModule || !$wheelSetModule) {
            return response()->json(['compatible' => false, 'message' => 'One or more modules not found']);
        }

        $compatible = $chassisModule->compatibleWheelSetModules->contains($wheelSetModule->id);

        return response()->json([
            'compatible' => $compatible,
            'message' => $compatible
                ? 'Modules are compatible'
                : 'The selected wheel set is not compatible with this chassis'
        ]);
    }
}
namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Vehicle;
use App\Models\VehicleComposition;
use App\ModuleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehicleCompositionController extends Controller
{
    /**
     * Display a listing of the vehicle compositions
     */
    public function index()
    {
        $compositions = VehicleComposition::with(['vehicle', 'modules'])
            ->where('user_id', Auth::id())
            ->get();

        return view('compositions.index', compact('compositions'));
    }

    /**
     * Show the form for creating a new vehicle composition
     */
    public function create()
    {
        // Get user's vehicles
        $vehicles = Vehicle::where('user_id', Auth::id())->get();

        if ($vehicles->isEmpty()) {
            return redirect()->route('vehicles.create')
                ->with('info', 'Please create a vehicle first before composing it.');
        }

        // Get all module types and modules for selection
        $moduleTypes = ModuleType::cases();
        $modules = [];

        foreach ($moduleTypes as $type) {
            $modules[$type->value] = Module::where('type', $type->value)->get();
        }

        return view('compositions.create', compact('vehicles', 'moduleTypes', 'modules'));
    }

    /**
     * Store a newly created vehicle composition
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'modules' => 'required|array',
            'modules.*' => 'required|exists:modules,id'
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);

        // Check if the vehicle belongs to the authenticated user
        if ($vehicle->user_id !== Auth::id()) {
            return back()->with('error', 'You can only compose your own vehicles.');
        }

        // Get the selected modules
        $moduleIds = array_values($validated['modules']);
        $modules = Module::whereIn('id', $moduleIds)->get();

        // Calculate total assembly time and cost
        $totalAssemblyTime = $modules->sum('assembly_time');
        $totalCost = $modules->sum('cost');

        // Create the composition in a transaction
        DB::transaction(function() use ($vehicle, $validated, $totalAssemblyTime, $totalCost) {
            // Create the composition
            $composition = VehicleComposition::create([
                'user_id' => Auth::id(),
                'vehicle_id' => $vehicle->id,
                'total_assembly_time' => $totalAssemblyTime,
                'total_cost' => $totalCost,
                'name' => $vehicle->name . ' Composition'
            ]);

            // Attach modules to the composition
            foreach ($validated['modules'] as $moduleType => $moduleId) {
                $composition->modules()->attach($moduleId, [
                    'module_type' => $moduleType
                ]);
            }
        });

        return redirect()->route('compositions.index')
            ->with('success', 'Vehicle composition created successfully!');
    }

    /**
     * Display the specified vehicle composition
     */
    public function show(VehicleComposition $composition)
    {
        // Check if the composition belongs to the authenticated user
        if ($composition->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $composition->load(['vehicle', 'modules']);

        // Group modules by type for structured display
        $modulesByType = [];
        foreach ($composition->modules as $module) {
            $modulesByType[$module->type->value][] = $module;
        }

        return view('compositions.show', compact('composition', 'modulesByType'));
    }

    /**
     * Remove the specified composition
     */
    public function destroy(VehicleComposition $composition)
    {
        // Check if the composition belongs to the authenticated user
        if ($composition->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $composition->modules()->detach();
        $composition->delete();

        return redirect()->route('compositions.index')
            ->with('success', 'Vehicle composition deleted successfully!');
    }
}
