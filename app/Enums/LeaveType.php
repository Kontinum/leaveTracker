<?php

namespace App\Enums;

enum LeaveType :int
{
    case ANNUAL = 1;
    case DAY_OFF = 2;

    public function availableDays(): int
    {
        return match ($this) {
            LeaveType::ANNUAL => 20,
            LeaveType::DAY_OFF => 5,
        };
    }

    public function name(): string
    {
        return match ($this) {
            LeaveType::ANNUAL => 'Annual',
            LeaveType::DAY_OFF => 'Days off',
        };
    }

    public static function all(): array
    {
        return [
          self::ANNUAL->value,
          self::DAY_OFF->value,
        ];
    }
}
