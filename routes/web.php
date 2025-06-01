<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\RobotController;
use App\Http\Controllers\VehicleController;
use App\ModuleType;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'role:admin,planner,mechanic'])->group(function () {
    Route::get('/calendar/{year?}/{month?}', [CalendarController::class, 'index'])
        ->where([
            'year' => '[0-9]{4}',
            'month' => '[0-9]{1,2}'
        ])
        ->name('calendar.index');
    Route::get('/calendar/{year}/{month}/{day}', [CalendarController::class, 'show'])
        ->where([
            'year' => '[0-9]{4}',
            'month' => '[0-9]{1,2}',
            'day' => '[0-9]{1,2}'
        ])
        ->name('calendar.show');
    Route::get('/calendar/{year}/{month}/{day}/create', [CalendarController::class, 'create'])
        ->where([
            'year' => '[0-9]{4}',
            'month' => '[0-9]{1,2}',
            'day' => '[0-9]{1,2}'
        ])
        ->name('calendar.create');
    Route::post('/calendar/{year}/{month}/{day}', [CalendarController::class, 'store'])
        ->where([
            'year' => '[0-9]{4}',
            'month' => '[0-9]{1,2}',
            'day' => '[0-9]{1,2}'
        ])
        ->name('calendar.store');
    Route::delete('/calendar/{schedule}', [CalendarController::class, 'destroy'])
        ->name('calendar.destroy');
});
Route::middleware(['auth', 'role:admin,mechanic'])->group(function () {
    Route::resource('vehicles', VehicleController::class);
    Route::resource('robots', RobotController::class);
});
Route::middleware(['auth', 'role:admin,buyer'])->group(function () {
   Route::resource('modules', ModuleController::class);

   foreach (ModuleType::values() as $moduleType) {
       Route::resource('modules/' . $moduleType, 'App\Http\Controllers\Modules\\' . snakeToPascalCase($moduleType) . 'ModuleController');
   }
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard.index');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';
