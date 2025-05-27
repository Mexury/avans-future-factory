<?php

namespace App;

enum RobotType: string
{
    use EnumTrait;

    case TWO_WHEELS = 'TwoWheels';
    case HYDRO_BOY = 'HydroBoy';
    case HEAVY_D = 'HeavyD';
}
