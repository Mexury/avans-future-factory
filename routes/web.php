<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RobotScheduleController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleCompositionController;
use App\Http\Controllers\VehiclePlanningController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

/** Dashboard view */
Route::view('/dashboard', 'dashboard')
    ->middleware(['auth', 'role:admin,customer'])
    ->name('dashboard');

/** Assembly view */
Route::view('/assembly', 'assembly')
    ->middleware(['auth', 'role:admin,mechanic'])
    ->name('assembly');

/** Planning view */
Route::get('/calendar/{year?}/{month?}', [CalendarController::class, 'index'])
    ->where([
        'year' => '[0-9]{4}',
        'month' => '[0-9]{1,2}'
    ])
    ->middleware(['auth', 'role:admin,planner'])
    ->name('calendar.index');

/** Planning view */
Route::get('/calendar/{year}/{month}/{day}', [CalendarController::class, 'show'])
    ->where([
        'year' => '[0-9]{4}',
        'month' => '[0-9]{1,2}',
        'day' => '[0-9]{1,2}'
    ])
    ->middleware(['auth', 'role:admin,planner'])
    ->name('calendar.show');

Route::get('/calendar/{year}/{month}/{day}/create', [CalendarController::class, 'create'])
    ->where([
        'year' => '[0-9]{4}',
        'month' => '[0-9]{1,2}',
        'day' => '[0-9]{1,2}'
    ])
    ->middleware(['auth', 'role:admin,planner'])
    ->name('calendar.create');

/** Module resource */
Route::resource('modules', ModuleController::class)
    ->middleware(['auth', 'role:admin,purchaser']);

/** Vehicle resource */
Route::resource('vehicles', VehicleController::class)
    ->middleware(['auth', 'role:admin,mechanic']);

/** Vehicle Composition resource */
Route::resource('vehicle_compositions', VehicleCompositionController::class)
    ->middleware(['auth', 'role:admin,mechanic']);

Route::post('/vehicle_compositions/check-compatibility', [VehicleCompositionController::class, 'checkCompatibility'])
    ->middleware(['auth', 'role:admin,mechanic'])
    ->name('vehicle_compositions.check_compatibility');

/** Robot Schedule resource */
Route::resource('robot_schedules', RobotScheduleController::class)
    ->middleware(['auth', 'role:admin,planner']);

Route::get('/robot_schedules/vehicle/{id}/completion', [RobotScheduleController::class, 'getVehicleCompletionDate'])
    ->middleware(['auth', 'role:admin,planner,customer'])
    ->name('robot_schedules.vehicle_completion');

/** Customer views */
Route::get('/customer/vehicles', [CustomerController::class, 'index'])
    ->middleware(['auth', 'role:admin,customer'])
    ->name('customer.vehicles.index');

Route::get('/customer/vehicles/{id}', [CustomerController::class, 'show'])
    ->middleware(['auth', 'role:admin,customer'])
    ->name('customer.vehicles.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__.'/auth.php';
