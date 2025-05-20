<?php

namespace App;

enum SteeringWheelShape: string
{
    use EnumTrait;

    case ROUND = 'round';
    case OVAL = 'oval';
    case STADIUM = 'stadium';
    case HEXAGONAL = 'hexagonal';
}
