<?php

namespace App\Models\Modules;

use App\IsModule;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read ChassisModule|null $chassisModule
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modules\WheelSetModule> $compatibleWheelSetModules
 * @property-read int|null $compatible_wheel_set_modules_count
 * @property-read \App\Models\Modules\EngineModule|null $engineModule
 * @property-read Module|null $module
 * @property-read \App\Models\Modules\SeatingModule|null $seatingModule
 * @property-read \App\Models\Modules\SteeringWheelModule|null $steeringWheelModule
 * @property-read \App\Models\Modules\WheelSetModule|null $wheelSetModule
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChassisModule whereUpdatedAt($value)
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
}
