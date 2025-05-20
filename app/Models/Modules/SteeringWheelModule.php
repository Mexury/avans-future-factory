<?php

namespace App\Models\Modules;

use App\IsModule;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Modules\ChassisModule|null $chassisModule
 * @property-read \App\Models\Modules\EngineModule|null $engineModule
 * @property-read Module|null $module
 * @property-read \App\Models\Modules\SeatingModule|null $seatingModule
 * @property-read SteeringWheelModule|null $steeringWheelModule
 * @property-read \App\Models\Modules\WheelSetModule|null $wheelSetModule
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SteeringWheelModule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SteeringWheelModule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SteeringWheelModule onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SteeringWheelModule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SteeringWheelModule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SteeringWheelModule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SteeringWheelModule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SteeringWheelModule withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SteeringWheelModule withoutTrashed()
 * @mixin \Eloquent
 */
class SteeringWheelModule extends Module
{
    use IsModule, HasFactory, SoftDeletes;

    // Override table name manually for inherited Eloquent model
    protected $table = 'steering_wheel_modules';

    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'special_adjustments',
            'shape'
        ]);

        parent::__construct($attributes);
    }

}
