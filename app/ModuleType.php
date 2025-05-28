<?php
<?php

namespace App;

enum ModuleType: string
{
    case CHASSIS = 'chassis';
    case ENGINE = 'engine';
    case WHEEL_SET = 'wheel_set';
    case STEERING_WHEEL = 'steering_wheel';
    case SEATING = 'seating';
}
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
