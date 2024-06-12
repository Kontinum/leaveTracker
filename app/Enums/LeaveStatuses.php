<?php

namespace App\Enums;

enum LeaveStatuses :int
{
    case APPROVED = 1;
    case REJECTED = 2;
    case ON_HOLD = 3;

    public static function all(): array
    {
        return [
            self::APPROVED->value,
            self::REJECTED->value,
            self::ON_HOLD->value
        ];
    }

    public static function nonRejected(): array
    {
        return [
            self::APPROVED->value,
            self::ON_HOLD->value
        ];
    }
}
