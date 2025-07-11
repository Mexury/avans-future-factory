<?php

namespace App\Models;

use App\Support\VehicleStatus;
use App\VehicleStatusType;
use App\VehicleType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property VehicleType $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VehiclePlanning> $planning
 * @property-read int|null $planning_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereUserId($value)
 * @mixin \Eloquent
 */
class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function planning(): HasMany
    {
        return $this->hasMany(VehiclePlanning::class);
    }

    public function status(): VehicleStatus {
        $plannings = $this->planning;

        if ($plannings->isEmpty()) {
            return new VehicleStatus(VehicleStatusType::DANGER, 'Not scheduled');
        }

        $completedCount = $plannings->filter(fn ($planning) => $planning->isCompleted())->count();
        $totalCount = $plannings->count();

        if (($totalCount >= 4) && ($completedCount === $totalCount)) {
            return new VehicleStatus(VehicleStatusType::SUCCESS, "Completed $completedCount/$totalCount");
        }

        return new VehicleStatus(VehicleStatusType::WARNING, "Assembly $completedCount/$totalCount");
    }

    protected $casts = [
        'type' => VehicleType::class,
    ];
}
