<?php

namespace App\Models;

use App\VehicleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RobotVehicleType> $vehicleTypes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RobotEngineType> $engineTypes
 * @property-read int|null $vehicle_types_count
 * @property-read int|null $engine_types_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Robot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Robot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Robot query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Robot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Robot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Robot whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Robot whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Robot extends Model
{
    protected $fillable = [
        'name'
    ];

    public function vehicleTypes(): HasMany
    {
        return $this->hasMany(RobotVehicleType::class);
    }

    public function engineTypes(): HasMany
    {
        return $this->hasMany(RobotEngineType::class);
    }

    public function planning(): HasMany
    {
        return $this->hasMany(VehiclePlanning::class);
    }

    public function supports(VehicleType $vehicleType): bool {
        return $this->vehicleTypes->pluck('vehicle_type')->contains($vehicleType);
    }
}
