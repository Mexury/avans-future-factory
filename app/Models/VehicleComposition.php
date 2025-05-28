<?php
<?php

namespace App\Models;

use App\ModuleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * VehicleComposition model representing a module that is part of a vehicle
 */
class VehicleComposition extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'module_id',
        'module_type',
        'installation_order'
    ];

    protected $casts = [
        'module_type' => ModuleType::class,
    ];

    /**
     * Get the vehicle that owns the composition.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the module for this composition.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
namespace App\Models;

use App\ModuleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VehicleComposition extends Model
{
    protected $fillable = [
        'user_id',
        'vehicle_id',
        'name',
        'total_assembly_time',
        'total_cost',
    ];

    /**
     * Get the user that owns the composition
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vehicle for this composition
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the modules for this composition
     */
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'composition_modules')
            ->withPivot('module_type')
            ->withTimestamps();
    }

    /**
     * Check if the composition has a module of the specified type
     */
    public function hasModuleType(ModuleType $type): bool
    {
        return $this->modules()->where('type', $type->value)->exists();
    }

    /**
     * Get module of a specific type from this composition
     */
    public function getModuleByType(ModuleType $type)
    {
        return $this->modules()->where('type', $type->value)->first();
    }

    /**
     * Check if the composition is complete with all required modules
     */
    public function isComplete(): bool
    {
        // A complete vehicle needs at least chassis, engine, steering wheel, and wheel set
        $requiredTypes = [
            ModuleType::CHASSIS,
            ModuleType::ENGINE,
            ModuleType::STEERING_WHEEL,
            ModuleType::WHEEL_SET
        ];

        foreach ($requiredTypes as $type) {
            if (!$this->hasModuleType($type)) {
                return false;
            }
        }

        return true;
    }
}
