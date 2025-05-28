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
        Schema::create('vehicle_compositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('total_assembly_time');
            $table->decimal('total_cost', 10, 2);
            $table->timestamps();
        });

        Schema::create('composition_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_composition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->string('module_type');
            $table->timestamps();

            $table->unique(['vehicle_composition_id', 'module_type'], 'unique_module_type_per_composition');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('composition_modules');
        Schema::dropIfExists('vehicle_compositions');
    }
};
