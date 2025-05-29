<?php

namespace App;

enum VehicleStatusType: string
{
    use EnumTrait;

    case SUCCESS = 'success';
    case WARNING = 'warning';
    case DANGER = 'danger';
}
