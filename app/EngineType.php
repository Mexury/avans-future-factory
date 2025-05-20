<?php

namespace App;

enum EngineType: string
{
    use EnumTrait;

    case HYDROGEN = 'hydrogen';
    case ELECTRIC = 'electric';
}
