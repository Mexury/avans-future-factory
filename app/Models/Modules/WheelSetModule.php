<?php

namespace App\Models\Modules;

use App\IsModule;
use App\Models\Module;
use App\ModuleType;
use App\WheelType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 *
 * @property int $id
 * @property int $module_id
 * @property WheelType $type
 * @property int $diameter
 * @property int $wheel_quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Modules\ChassisModule|null $chassisModule
 * @property-read Collection<int, \App\Models\Modules\ChassisModule> $compatibleChassisModules
 * @property-read int|null $compatible_chassis_modules_count
 * @property-read \App\Models\Modules\EngineModule|null $engineModule
 * @property-read Module $module
 * @property-read \App\Models\Modules\SeatingModule|null $seatingModule
 * @property-read \App\Models\Modules\SteeringWheelModule|null $steeringWheelModule
 * @property-read WheelSetModule|null $wheelSetModule
 * @method static \Database\Factories\Modules\WheelSetModuleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WheelSetModule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WheelSetModule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WheelSetModule onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WheelSetModule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WheelSetModule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WheelSetModule whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WheelSetModule whereDiameter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WheelSetModule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WheelSetModule whereModuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WheelSetModule whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WheelSetModule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WheelSetModule whereWheelQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WheelSetModule withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WheelSetModule withoutTrashed()
 * @mixin \Eloquent
 */
class WheelSetModule extends Module
{
    use IsModule, HasFactory, SoftDeletes;

    // Override table name manually for inherited Eloquent model
    protected $table = 'wheel_set_modules';

    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'type',
            'diameter',
            'wheel_quantity'
        ]);

        parent::__construct($attributes);
    }

    // Get all chassis modules with the wheel quantity as this module
    public function compatibleChassisModules(): Collection
    {
        return ChassisModule::where(['wheel_quantity' => $this->wheel_quantity])->get();
    }

    protected $casts = [
        'type' => WheelType::class,
    ];
}
