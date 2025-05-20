<?php

namespace App;

/**
 * @method static cases()
 */
trait EnumTrait
{
    static function values(): array {
        return array_column(self::cases(), 'value');
    }
}
