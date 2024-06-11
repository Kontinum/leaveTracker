<?php

namespace App\Http\Controllers;

use App\Api\ApiResponse;
use App\Enums\HttpStatus;
use App\Exceptions\ApiResponseException;
use App\Http\Requests\Teams\SaveTeamRequest;
use App\Http\Requests\Teams\UpdateTeamRequest;
use App\Http\Resources\TeamsResource;
use App\Models\Team;
use App\Repositories\TeamsRepository;
use App\Repositories\UsersRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class TeamsController extends Controller
{
    use ApiResponse;
    public function __construct(
        private readonly TeamsRepository $teamsRepository,
        private readonly UsersRepository $usersRepository,
    )
    {
    }

    /**
     * @param SaveTeamRequest $saveTeamRequest
     * @return JsonResponse
     * @throws ApiResponseException
     */
    public function store(SaveTeamRequest $saveTeamRequest): JsonResponse
    {
        try {
            DB::beginTransaction();
            $saveData = $saveTeamRequest->validated();
            $checkManagers = $this->usersRepository->checkManagers($saveData['user_ids']);

            if (count($checkManagers) === 0) {
                return $this->sendResponse('Team must have at least one manager', HttpStatus::UNPROCESSABLE->value, 'message');
            }

            $savedTeam = $this->teamsRepository->create($saveData['team_data']);

            $this->usersRepository->addToTeam($savedTeam, $saveData['user_ids']);

            DB::commit();

            return $this->sendResponse(new TeamsResource($savedTeam));
        } catch (Exception $e) {
            DB::rollBack();
            throw new ApiResponseException($e->getMessage());
        }
    }

    public function update(UpdateTeamRequest $updateTeamRequest, Team $team)
    {
        try {
            DB::beginTransaction();
            $updateData = $updateTeamRequest->validated();

            $updatedTeam = $this->teamsRepository->update($team, $updateData['team_data']);

            if (isset($updateData['user_ids'])) {
                $this->usersRepository->addToTeam($updatedTeam, $updateData['user_ids']);
            }

            DB::commit();

            return $this->sendResponse(new TeamsResource($updatedTeam));
        } catch (Exception $e) {
            DB::rollBack();
            throw new ApiResponseException($e->getMessage());
        }
    }
}
