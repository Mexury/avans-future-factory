<?php

namespace App;

enum VehicleType: string
{
    use EnumTrait;

    case SCOOTER = 'scooter';
    case BICYCLE = 'bicycle';
    case ELECTRIC_SCOOTER = 'electric_scooter';
    case CAR = 'car';
    case TRUCK = 'truck';
    case BUS = 'bus';
}
