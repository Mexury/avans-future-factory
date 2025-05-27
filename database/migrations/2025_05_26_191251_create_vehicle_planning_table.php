<?php

use App\Models\Module;
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
            $table->foreignIdFor(RobotSchedule::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Vehicle::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Module::class)->constrained()->cascadeOnDelete();
            $table->enum('module_type', ModuleType::values());
            $table->unique(['robot_schedule_id', 'vehicle_id', 'module_id', 'module_type'], 'vehicle_planning_unique');
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
