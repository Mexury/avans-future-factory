<?php

namespace App\Support;

use App\VehicleStatusType;

class VehicleStatus {
    public string $message = 'No status set';
    public VehicleStatusType $type;

    public function __construct(VehicleStatusType $type, string $message) {
        $this->type = $type;
        $this->message = $message;
    }
}
