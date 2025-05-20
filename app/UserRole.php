<?php

namespace App;

enum UserRole: string
{
    use EnumTrait;

    case MECHANIC = 'mechanic';
    case PLANNER = 'planner';
    case CUSTOMER = 'customer';
    case BUYER = 'buyer';
    case ADMIN = 'admin';
}
