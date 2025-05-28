<?php
<?php

namespace App\Models\Modules;

use App\IsModule;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * EngineModule represents a vehicle engine component
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
            'power'
        ]);

        parent::__construct($attributes);
    }

    protected $casts = [
        'power' => 'integer',
    ];
}
namespace App\Models\Modules;

use App\EngineType;
use App\IsModule;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 *
 * @property int $id
 * @property int $module_id
 * @property EngineType $type
 * @property int $horse_power
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Modules\ChassisModule|null $chassisModule
 * @property-read EngineModule|null $engineModule
 * @property-read Module $module
 * @property-read \App\Models\Modules\SeatingModule|null $seatingModule
 * @property-read \App\Models\Modules\SteeringWheelModule|null $steeringWheelModule
 * @property-read \App\Models\Modules\WheelSetModule|null $wheelSetModule
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngineModule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngineModule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngineModule onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngineModule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngineModule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngineModule whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngineModule whereHorsePower($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngineModule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngineModule whereModuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngineModule whereType($value)
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

    protected $casts = [
        'type' => EngineType::class,
    ];
}
