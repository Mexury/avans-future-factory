<?php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * RobotSchedule model representing a time slot assigned to a robot
 */
class RobotSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'robot_id',
        'date',
        'time_slot'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the robot that owns the schedule.
     */
    public function robot(): BelongsTo
    {
        return $this->belongsTo(Robot::class);
    }

    /**
     * Get the vehicle plannings for this schedule.
     */
    public function vehiclePlannings(): HasMany
    {
        return $this->hasMany(VehiclePlanning::class);
    }

    /**
     * Format the time slot as a readable time range.
     */
    public function getTimeRangeAttribute()
    {
        $timeRanges = [
            1 => '08:00 - 10:00',
            2 => '10:00 - 12:00',
            3 => '12:00 - 14:00',
            4 => '14:00 - 16:00',
        ];

        return $timeRanges[$this->time_slot] ?? 'Unknown time slot';
    }
}
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $robot_id
 * @property string $date
 * @property int $slot
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Robot $robot
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RobotSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RobotSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RobotSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RobotSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RobotSchedule whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RobotSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RobotSchedule whereRobotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RobotSchedule whereSlot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RobotSchedule whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RobotSchedule extends Model
{
    protected $fillable = [
        'robot_id',
        'date',
        'slot'
    ];

    public function robot(): BelongsTo {
        return $this->belongsTo(Robot::class);
    }
}
