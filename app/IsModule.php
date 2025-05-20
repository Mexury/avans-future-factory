<?php

namespace App;

use App\Models\Module;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method belongsTo(string $class)
 */
trait IsModule
{
    public function module(): BelongsTo {
        return $this->belongsTo(Module::class);
    }
}
