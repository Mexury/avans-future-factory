<?php

namespace App\Models;

use App\ModuleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $robot_schedule_id
 * @property int $vehicle_id
 * @property int $module_id
 * @property ModuleType $module_type
 * @property-read \App\Models\Module $module
 * @property-read \App\Models\RobotSchedule $robotSchedule
 * @property-read \App\Models\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehiclePlanning newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehiclePlanning newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehiclePlanning query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehiclePlanning whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehiclePlanning whereModuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehiclePlanning whereModuleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehiclePlanning whereRobotScheduleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehiclePlanning whereVehicleId($value)
 * @mixin \Eloquent
 */
class VehiclePlanning extends Model
{
    protected $table = 'vehicle_planning';
    protected $fillable = [
        'robot_schedule_id',
        'vehicle_id',
        'module_id',
        'module_type'
    ];

    public function robotSchedule(): BelongsTo {
        return $this->belongsTo(RobotSchedule::class);
    }

    public function vehicle(): BelongsTo {
        return $this->belongsTo(Vehicle::class);
    }

    public function module(): BelongsTo {
        return $this->belongsTo(Module::class);
    }

    protected $casts = [
        'module_type' => ModuleType::class,
    ];
}
