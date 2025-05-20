<?php

namespace App;

enum WheelType: string
{
    use EnumTrait;

    case WINTER = 'winter';
    case SUMMER = 'summer';
    case ALL_SEASON = 'all_season';
}
