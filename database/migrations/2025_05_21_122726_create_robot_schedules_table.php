<?php

use App\Models\Module;
use App\Models\Robot;
use App\Models\RobotSchedule;
use App\Models\Vehicle;
use App\RobotType;
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
        Schema::create('robot_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Robot::class)->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->integer('slot');
            $table->timestamps();
            $table->unique(['date', 'slot', 'robot_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('robot_schedules');
    }
};
