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
 * @property-read EngineModule|null $engineModule
 * @property-read Module|null $module
 * @property-read \App\Models\Modules\SeatingModule|null $seatingModule
 * @property-read \App\Models\Modules\SteeringWheelModule|null $steeringWheelModule
 * @property-read \App\Models\Modules\WheelSetModule|null $wheelSetModule
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngineModule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngineModule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngineModule onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngineModule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngineModule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngineModule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngineModule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngineModule withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngineModule withoutTrashed()
 * @mixin \Eloquent
 */
class EngineModule extends Module
{
    use IsModule, HasFactory, SoftDeletes;

    // Override table name manually for inherited Eloquent model
    protected $table = 'engine_modules';

    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'type',
            'horse_power'
        ]);

        parent::__construct($attributes);
    }

}
