<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Vehicle model representing a composed vehicle that can be scheduled for production
 */
class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'user_id'
    ];

    /**
     * Get the user that owns the vehicle.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the compositions for the vehicle.
     */
    public function compositions(): HasMany
    {
        return $this->hasMany(VehicleComposition::class);
    }

    /**
     * Get the plannings for the vehicle.
     */
    public function plannings(): HasMany
    {
        return $this->hasMany(VehiclePlanning::class);
    }

    /**
     * Get total assembly time for all modules in this vehicle.
     */
    public function getTotalAssemblyTimeAttribute()
    {
        return $this->compositions()
            ->join('modules', 'vehicle_compositions.module_id', '=', 'modules.id')
            ->sum('modules.assembly_time');
    }

    /**
     * Get total cost for all modules in this vehicle.
     */
    public function getTotalCostAttribute()
    {
        return $this->compositions()
            ->join('modules', 'vehicle_compositions.module_id', '=', 'modules.id')
            ->sum('modules.cost');
    }
}
