<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ModuleController;
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
Route::view('/planning', 'planning')
    ->middleware(['auth', 'role:admin,planner'])
    ->name('planning');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__.'/auth.php';
