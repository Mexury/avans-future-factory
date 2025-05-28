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
        Schema::create('robot_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('robot_id')->constrained();
            $table->date('date');
            $table->integer('time_slot'); // 1-4 (representing 2-hour blocks)
            $table->timestamps();

            // Make sure a robot can only be scheduled once per time slot
            $table->unique(['robot_id', 'date', 'time_slot']);
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
