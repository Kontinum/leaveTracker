<?php

namespace App\Services;

use App\Enums\LeaveStatuses;
use App\Enums\LeaveType;
use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Period\Period;
use Spatie\Period\Precision;

class LeaveService
{
    /**
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return int
     */
    public function calculateNumberOfLeaveDays(Carbon $startDate, Carbon $endDate): int
    {
        return (int) ceil($startDate->diffInDaysFiltered(function (Carbon $date) {
            return !$date->isWeekend();
        }, $endDate));
    }

    public function checkTeamLeavesData(\Illuminate\Support\Collection $teamLeavesData, array $currentLeave, User $currentUser): array
    {
        $leaveCheck = [
            'available' => false,
            'message' => '',
            'no_days' => 0
        ];
        $startDate = Carbon::parse($currentLeave['start_date'])->startOfDay();
        $endDate = Carbon::parse($currentLeave['end_date'])->endOfDay();

        $currentLeaveNoDays = $this->calculateNumberOfLeaveDays($startDate, $endDate);

        $leaveType = LeaveType::from($currentLeave['leave_type_id']);

        //Check if current user is applying leave for himself.
        if ($currentUser->id !== (int) $currentLeave['user_id']) {
            $leaveCheck['message'] = 'You cannot create leave for another user.';

            return $leaveCheck;
        }

        //Check if new number of days + old days exceed available days
        $usedDays = $teamLeavesData
            ->where('user_id', $currentLeave['user_id'])
            ->where('leave_type_id', $leaveType->value)
            ->sum('no_days');

        if ($usedDays + $currentLeaveNoDays > $leaveType->availableDays()) {
            $leaveCheck['message'] = 'The maximum amount of available days will be exceeded.';

            return $leaveCheck;
        }

        //Check if current leave period overlaps with some other leave period
        $currentLeavePeriod = Period::make($currentLeave['start_date'], $currentLeave['end_date'], Precision::DAY());
        foreach ($teamLeavesData as $leaveData) {
            $leaveDataPeriod = Period::make($leaveData->start_date, $leaveData->end_date);
            if ($currentLeavePeriod->overlapsWith($leaveDataPeriod)) {
                $leaveCheck['message'] = 'This leave period overlaps with yours or some other team members leaves.';

                return $leaveCheck;
            }
        }

        $leaveCheck['available'] = true;
        $leaveCheck['no_days'] = $currentLeaveNoDays;

        return $leaveCheck;
    }

    /**
     * @param User $currentUser
     * @param Leave $leave
     * @param int $leaveStatusId
     * @param Collection $userTeamLeavesData
     * @return bool
     */
    public function checkChangeLeaveStatus(User $currentUser, Leave $leave, int $leaveStatusId, Collection $userTeamLeavesData): bool
    {
        if ($currentUser->isRegular()) {
            return !($leave->isRejected() ||
                $leave->user_id !== $currentUser->id ||
                $leaveStatusId === LeaveStatuses::APPROVED->value);
        }
        if ($currentUser->isManager()) {
            $leaves = $userTeamLeavesData->pluck('leaves')->flatten();
            $teamMembersIds = $userTeamLeavesData->pluck('id')->toArray();

            return !($leave->isRejected() ||
                count($leaves) === 0 ||
                !in_array($leave->user_id, $teamMembersIds)
            );
        }

        return false;
    }

    /**
     * @param Collection $leaves
     * @return array
     */
    public function calculateAvailableDays(Collection $leaves): array
    {
        $leaveTypes = LeaveType::cases();

        $availableDays = [];
        foreach ($leaveTypes as $leaveType) {
            $usedDays = $leaves
                ->where('leave_status_id', LeaveStatuses::APPROVED->value)
                ->where('leave_type_id', $leaveType->value)
                ->sum('no_days');

            $availableDays[$leaveType->name()] = $leaveType->availableDays() - $usedDays;
        }

        return $availableDays;
    }
}
