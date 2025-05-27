<?php

namespace App\Models;

use App\VehicleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $robot_id
 * @property VehicleType $vehicle_type
 * @property-read \App\Models\Robot $robot
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RobotVehicleType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RobotVehicleType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RobotVehicleType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RobotVehicleType whereRobotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RobotVehicleType whereVehicleType($value)
 * @mixin \Eloquent
 */
class RobotVehicleType extends Model
{
    protected $fillable = [
        'robot_id',
        'vehicle_type'
    ];

    public function robot(): BelongsTo
    {
        return $this->belongsTo(Robot::class);
    }

    protected $casts = [
        'vehicle_type' => VehicleType::class
    ];
}
