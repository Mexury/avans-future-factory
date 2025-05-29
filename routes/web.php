<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\Modules\ChassisModuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\RobotController;
use App\Http\Controllers\VehicleController;
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

Route::post('/calendar/{year}/{month}/{day}', [CalendarController::class, 'store'])
    ->where([
        'year' => '[0-9]{4}',
        'month' => '[0-9]{1,2}',
        'day' => '[0-9]{1,2}'
    ])
    ->middleware(['auth', 'role:admin,planner'])
    ->name('calendar.store');

Route::delete('/calendar/{schedule}', [CalendarController::class, 'destroy'])
    ->middleware(['auth', 'role:admin,planner'])
    ->name('calendar.destroy');

/** Vehicle resource */
Route::resource('vehicles', VehicleController::class)
    ->middleware(['auth', 'role:admin,mechanic']);

/** Robots resource */
Route::resource('robots', RobotController::class)
    ->middleware(['auth', 'role:admin,mechanic']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin,planner'])->group(function () {
   Route::get('/modules', [ModuleController::class, 'index'])->name('modules.index');

   Route::resource('modules/chassis', ChassisModuleController::class);
//   Route::resource('/modules/engine', ChassisModuleController::class);
//   Route::resource('/modules/seating', ChassisModuleController::class);
//   Route::resource('/modules/steering_wheel', ChassisModuleController::class);
//   Route::resource('/modules/wheel_set', ChassisModuleController::class);
});

require __DIR__.'/auth.php';
