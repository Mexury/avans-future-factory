<?php

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
        Schema::create('vehicle_planning', function (Blueprint $table) {
            $table->id();
            $table->foreignId('robot_schedule_id')->constrained();
            $table->foreignId('vehicle_id')->constrained();
            $table->foreignId('module_id')->constrained();
            $table->string('module_type');
            $table->timestamps();

            // Each module can only be scheduled once
            $table->unique(['vehicle_id', 'module_id']);

            // Each robot schedule can only have one planning
            $table->unique(['robot_schedule_id']);
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
