<?php

namespace App\Models;

use App\Models\Modules\ChassisModule;
use App\Models\Modules\EngineModule;
use App\Models\Modules\SeatingModule;
use App\Models\Modules\SteeringWheelModule;
use App\Models\Modules\WheelSetModule;
use App\ModuleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 
 *
 * @property int $id
 * @property int $assembly_time
 * @property string $cost
 * @property string $name
 * @property string $image
 * @property ModuleType $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read ChassisModule|null $chassisModule
 * @property-read EngineModule|null $engineModule
 * @property-read SeatingModule|null $seatingModule
 * @property-read SteeringWheelModule|null $steeringWheelModule
 * @property-read WheelSetModule|null $wheelSetModule
 * @method static \Database\Factories\ModuleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereAssemblyTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module withoutTrashed()
 * @mixin \Eloquent
 */
class Module extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'assembly_time',
        'cost',
        'name',
        'image',
        'type'
    ];

    public function chassisModule(): HasOne {
        return $this->hasOne(ChassisModule::class);
    }

    public function engineModule(): HasOne {
        return $this->hasOne(EngineModule::class);
    }

    public function seatingModule(): HasOne {
        return $this->hasOne(SeatingModule::class);
    }

    public function steeringWheelModule(): HasOne {
        return $this->hasOne(SteeringWheelModule::class);
    }

    public function wheelSetModule(): HasOne {
        return $this->hasOne(WheelSetModule::class);
    }

    protected $casts = [
        'type' => ModuleType::class,
    ];

}
