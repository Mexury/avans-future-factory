<?php

use App\Models\Module;
use App\Models\Robot;
use App\Models\RobotSchedule;
use App\Models\Vehicle;
use App\ModuleType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_planning', function (BluePrint $table) {
            $table->id();
            $table->foreignIdFor(Vehicle::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Robot::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Module::class)->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->integer('slot_start');
            $table->integer('slot_end');
            $table->boolean('force_completed')->default(false);
            $table->timestamps();
            $table->unique(['vehicle_id', 'module_id'], 'vehicle_planning_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_planning');
    }
};
