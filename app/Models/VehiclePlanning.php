<?php

namespace App\Models;

use App\ModuleType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $vehicle_id
 * @property int $robot_id
 * @property int $module_id
 * @property \Illuminate\Support\Carbon $date
 * @property int $slot_start
 * @property int $slot_end
 * @property int $force_completed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Module $module
 * @property-read \App\Models\Robot $robot
 * @property-read \App\Models\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehiclePlanning newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehiclePlanning newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehiclePlanning query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehiclePlanning whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehiclePlanning whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehiclePlanning whereForceCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehiclePlanning whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehiclePlanning whereModuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehiclePlanning whereRobotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehiclePlanning whereSlotEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehiclePlanning whereSlotStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehiclePlanning whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehiclePlanning whereVehicleId($value)
 * @mixin \Eloquent
 */
class VehiclePlanning extends Model
{
    protected $table = 'vehicle_planning';

    protected $fillable = [
        'vehicle_id',
        'module_id',
        'robot_id',
        'date',
        'slot_start',
        'slot_end',
        'force_completed'
    ];

    public function vehicle(): BelongsTo {
        return $this->belongsTo(Vehicle::class);
    }

    public function module(): BelongsTo {
        return $this->belongsTo(Module::class);
    }

    public function robot(): BelongsTo {
        return $this->belongsTo(Robot::class);
    }

    public function isCompleted(): bool {
            $scheduledEndTime = Carbon::parse($this->date)
                ->setHour(9 + ($this->slot_end * 2));

            return $this->force_completed || now()->greaterThan($scheduledEndTime);
    }

    protected $casts = [
        'date' => 'datetime',
    ];
}
