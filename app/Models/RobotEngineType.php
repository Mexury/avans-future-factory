<?php

namespace App\Models;

use App\EngineType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $robot_id
 * @property string $engine_type
 * @property-read \App\Models\Robot $robot
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RobotEngineType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RobotEngineType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RobotEngineType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RobotEngineType whereRobotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RobotEngineType whereEngineType($value)
 * @mixin \Eloquent
 */
class RobotEngineType extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'robot_id',
        'engine_type'
    ];

    public function robot(): BelongsTo
    {
        return $this->belongsTo(Robot::class);
    }

    protected $casts = [
        'engine_type' => EngineType::class
    ];
}
