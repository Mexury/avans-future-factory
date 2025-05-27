<?php

namespace App\Models\Modules;

use App\IsModule;
use App\Models\Module;
use App\VehicleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 
 *
 * @property int $id
 * @property int $module_id
 * @property int $wheel_quantity
 * @property VehicleType $vehicle_type
 * @property int $length
 * @property int $width
 * @property int $height
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read ChassisModule|null $chassisModule
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\WheelSetModule> $compatibleWheelSetModules
 * @property-read int|null $compatible_wheel_set_modules_count
 * @property-read \App\Models\Modules\EngineModule|null $engineModule
 * @property-read Module $module
 * @property-read \App\Models\Modules\SeatingModule|null $seatingModule
 * @property-read \App\Models\Modules\SteeringWheelModule|null $steeringWheelModule
 * @property-read \App\Models\Modules\WheelSetModule|null $wheelSetModule
 * @method static \Database\Factories\Modules\ChassisModuleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule whereLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule whereModuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule whereVehicleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule whereWheelQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule whereWidth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule withoutTrashed()
 * @mixin \Eloquent
 */
class ChassisModule extends Module
{
    use IsModule, HasFactory, SoftDeletes;

    // Override table name manually for inherited Eloquent model
    protected $table = 'chassis_modules';

    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'wheel_quantity',
            'vehicle_type',
            'length',
            'width',
            'height'
        ]);

        parent::__construct($attributes);
    }

    public function compatibleWheelSetModules(): BelongsToMany {
        return $this->belongsToMany(WheelSetModule::class, 'compatible_wheel_set_modules');
    }

    protected $casts = [
        'vehicle_type' => VehicleType::class,
    ];
}
