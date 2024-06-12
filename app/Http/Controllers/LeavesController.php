<?php

namespace App\Http\Controllers;

use App\Api\ApiResponse;
use App\Enums\HttpStatus;
use App\Enums\LeaveStatuses;
use App\Exceptions\ApiResponseException;
use App\Http\Requests\Leaves\ChangeLeaveStatusRequest;
use App\Http\Requests\Leaves\CreateLeaveRequest;
use App\Http\Resources\LeavesResource;
use App\Models\Leave;
use App\Repositories\LeavesRepository;
use App\Repositories\UsersRepository;
use App\Services\LeaveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LeavesController extends Controller
{
    use ApiResponse;
    public function __construct(
        private readonly LeavesRepository $leavesRepository,
        private readonly UsersRepository $usersRepository,
        private readonly LeaveService $leaveService
    )
    {
    }

    /**
     * @param CreateLeaveRequest $createLeaveRequest
     * @param LeaveService $leaveService
     * @return JsonResponse
     * @throws ApiResponseException
     */
    public function store(CreateLeaveRequest $createLeaveRequest): JsonResponse
    {
        try {
            DB::beginTransaction();

            $createData = $createLeaveRequest->validated();
            $userTeamLeavesData = $this->usersRepository->getUserTeamLeavesData($createData['user_id'], [LeaveStatuses::APPROVED->value, LeaveStatuses::ON_HOLD->value]);
            $teamLeavesData = $userTeamLeavesData->pluck('leaves')->flatten();

            $leaveCheck = $this->leaveService->checkTeamLeavesData($teamLeavesData, $createData, request()->user());
            if (!$leaveCheck['available']) {
                return $this->sendResponse($leaveCheck['message'], HttpStatus::UNPROCESSABLE->value, 'message');
            }
            $createData['no_days'] = $leaveCheck['no_days'];
            $createData['leave_status_id'] = LeaveStatuses::ON_HOLD->value;

            $newLeave = $this->leavesRepository->create($createData);

            DB::commit();

            return $this->sendResponse(new LeavesResource($newLeave));
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ApiResponseException($e->getMessage());
        }
    }

    /**
     * @param ChangeLeaveStatusRequest $changeLeaveStatusRequest
     * @param Leave $leave
     * @return JsonResponse
     * @throws ApiResponseException
     */
    public function changeStatus(ChangeLeaveStatusRequest $changeLeaveStatusRequest, Leave $leave): JsonResponse
    {
        try {
            $currentUser = request()->user();
            $changeLeaveStatusData = $changeLeaveStatusRequest->validated();
            $leaveStatusId = $changeLeaveStatusData['leave_status_id'];
            $userTeamLeavesData = $this->usersRepository->getUserTeamLeavesData($currentUser->id, LeaveStatuses::nonRejected());

            $leaveStatusCanBeChanged = $this->leaveService->checkChangeLeaveStatus($currentUser, $leave, $leaveStatusId, $userTeamLeavesData);

            if ($leaveStatusCanBeChanged) {
                $updatedLeave = $this->leavesRepository->updateLeaveStatus($leave, $leaveStatusId);

                return $this->sendResponse(new LeavesResource($updatedLeave));
            }

            return $this->sendResponse('You cannot change this leave.', HttpStatus::UNPROCESSABLE->value, 'message');
        } catch (\Exception $e) {
            throw new ApiResponseException($e->getMessage());
        }
    }

    /**
     * @throws ApiResponseException
     */
    public function getActiveLeavesData(): JsonResponse
    {
        try {
            $currentUser = request()->user();

            $userTeamLeavesData = $this->usersRepository->getUserTeamLeavesData($currentUser->id, LeaveStatuses::nonRejected());
            $teamLeavesData = $userTeamLeavesData->pluck('leaves')->flatten();

            return $this->sendResponse(LeavesResource::collection($teamLeavesData));
        } catch (\Exception $e) {
            throw new ApiResponseException($e->getMessage());
        }
    }

    /**
     * @return JsonResponse
     * @throws ApiResponseException
     */
    public function getLeavesHistory(): JsonResponse
    {
        try {
            $currentUser = request()->user();
            $leavesHistory = $this->leavesRepository->getHistory(['user_id' => $currentUser->id], false);

            $availableDays = $this->leaveService->calculateAvailableDays($leavesHistory);

            $data['leavesHistory'] = LeavesResource::collection($leavesHistory);
            $data['availableDays'] = $availableDays;

            return $this->sendResponse($data);
        } catch (\Exception $e) {
            throw new ApiResponseException($e->getMessage());
        }
    }
}
