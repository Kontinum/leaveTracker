<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeavesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'no_days' => $this->no_days,
            'created_at' => Carbon::parse($this->created_at)->format('d-m-Y H:i:s'),
            'user' => new UserResource($this->user),
            'leave_type' => new LeaveTypesResource($this->leaveType),
            'leave_status' => new LeaveStatusesResource($this->leaveStatus),
        ];
    }

    public function with(Request $request)
    {
        return ['availableDays'];
    }
}
