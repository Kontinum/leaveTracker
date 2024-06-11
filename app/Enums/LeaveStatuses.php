<?php

namespace App\Enums;

enum LeaveStatuses :int
{
    case APPROVED = 1;
    case REJECTED = 2;
    case DAY_OFF = 3;
}
