<?php

namespace App;

enum UpholsteryType: string
{
    use EnumTrait;

    case LEATHER = 'leather';
    case FABRIC = 'fabric';
    case SHEEPSKIN = 'sheepskin';
    case FAUX_LEATHER = 'faux_leather';
    case METAL = 'metal';
}
