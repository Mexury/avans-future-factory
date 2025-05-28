<?php

namespace App;

enum VehicleStatus: string
{
    use EnumTrait;

    case IN_PRODUCTION = 'in_production';
    case READY_FOR_DELIVERY = 'ready_for_delivery';
    case DELIVERED = 'delivered';
}
