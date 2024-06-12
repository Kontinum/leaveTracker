<?php

namespace App\Models;

use App\Enums\LeaveStatuses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function leaveStatus(): BelongsTo
    {
        return $this->belongsTo(LeaveStatus::class);
    }

    public function isRejected(): bool
    {
        return $this->leave_status_id === LeaveStatuses::REJECTED->value;
    }
}
