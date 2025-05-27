<?php

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
