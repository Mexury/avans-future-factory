<?php

use App\Models\Robot;
use App\VehicleType;
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
        Schema::create('robots', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('robot_vehicle_types', function (Blueprint $table) {
            $table->foreignIdFor(Robot::class)->constrained()->cascadeOnDelete();
            $table->enum('vehicle_type', VehicleType::values());
            $table->primary(['robot_id', 'vehicle_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('robots');
        Schema::dropIfExists('robot_vehicle_types');
    }
};
