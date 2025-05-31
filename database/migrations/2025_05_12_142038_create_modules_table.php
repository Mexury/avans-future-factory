<?php

use App\EngineType;
use App\Models\Module;
use App\Models\Modules\ChassisModule;
use App\Models\Modules\WheelSetModule;
use App\ModuleType;
use App\SteeringWheelShape;
use App\UpholsteryType;
use App\VehicleType;
use App\WheelType;
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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->integer('assembly_time');
            $table->decimal('cost', 10, 2);
            $table->string('name');
            $table->string('image')->default('placeholder.jpg');
            $table->enum('type', ModuleType::values());
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('chassis_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Module::class)->constrained()->cascadeOnDelete();
            $table->integer('wheel_quantity');
            $table->enum('vehicle_type', VehicleType::values());
            $table->integer('length');
            $table->integer('width');
            $table->integer('height');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('engine_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Module::class)->constrained()->cascadeOnDelete();
            $table->enum('type', EngineType::values());
            $table->integer('horse_power');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('wheel_set_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Module::class)->constrained()->cascadeOnDelete();
            $table->enum('type', WheelType::values());
            $table->integer('diameter');
            $table->integer('wheel_quantity');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('steering_wheel_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Module::class)->constrained()->cascadeOnDelete();
            $table->string('special_adjustments');
            $table->enum('shape', SteeringWheelShape::values());
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('seating_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Module::class)->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->enum('upholstery', UpholsteryType::values());
            $table->timestamps();
            $table->softDeletes();
        });

//        Schema::create('compatible_wheel_set_modules', function (Blueprint $table) {
//            $table->foreignIdFor(ChassisModule::class)->constrained()->cascadeOnDelete();
//            $table->foreignIdFor(WheelSetModule::class)->constrained()->cascadeOnDelete();
//            $table->primary(['chassis_module_id', 'wheel_set_module_id']);
//        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
        Schema::dropIfExists('chassis_modules');
        Schema::dropIfExists('engine_modules');
        Schema::dropIfExists('wheel_set_modules');
        Schema::dropIfExists('steering_wheel_modules');
        Schema::dropIfExists('seating_modules');
//        Schema::dropIfExists('compatible_wheel_set_modules');
    }
};
