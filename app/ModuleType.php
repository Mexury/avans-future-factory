<?php

namespace App;

enum ModuleType: string
{
    use EnumTrait;

    case CHASSIS = 'chassis';
    case ENGINE = 'engine';
    case SEATING = 'seating';
    case STEERING_WHEEL = 'steering_wheel';
    case WHEEL_SET = 'wheel_set';

}
