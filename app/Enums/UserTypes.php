<?php

namespace App\Enums;

enum UserTypes: int
{
    case ADMIN = 1;
    case MANAGER = 2;
    case REGULAR = 3;
}
