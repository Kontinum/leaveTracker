<?php

namespace App\Repositories;

use App\Models\Leave;

class LeavesRepository
{
    private function getModel(): Leave
    {
        return new Leave();
    }

    /**
     * @param array $creteData
     * @return Leave
     */
    public function create(array $creteData): Leave
    {
        $leave = $this->getModel()->fill($creteData);
        $leave->save();

        return $leave;
    }

    /**
     * @param Leave $leave
     * @param int $leaveStatusId
     * @return Leave
     */
    public function updateLeaveStatus(Leave $leave, int $leaveStatusId): Leave
    {
        $leave->leave_status_id = $leaveStatusId;
        $leave->save();

        return $leave;
    }

    public function getHistory(array $attributes, bool $first = true)
    {
        $leaveBuilder = $this->getModel()->where($attributes);

        return $first ? $leaveBuilder->first() : $leaveBuilder->get();
    }
}
