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
 * @property-read SeatingModule|null $seatingModule
 * @property-read \App\Models\Modules\SteeringWheelModule|null $steeringWheelModule
 * @property-read \App\Models\Modules\WheelSetModule|null $wheelSetModule
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeatingModule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeatingModule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeatingModule onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeatingModule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeatingModule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeatingModule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeatingModule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeatingModule withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeatingModule withoutTrashed()
 * @mixin \Eloquent
 */
class SeatingModule extends Module
{
    use IsModule, HasFactory, SoftDeletes;

    // Override table name manually for inherited Eloquent model
    protected $table = 'seating_modules';

    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'quantity',
            'upholstery'
        ]);

        parent::__construct($attributes);
    }

}
