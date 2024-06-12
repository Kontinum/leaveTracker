<?php

namespace App\Repositories;

use App\Enums\UserTypes;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UsersRepository
{
    private function getModel(): User
    {
        return new User();
    }

    public function create(array $createData): User
    {
        $user = $this->getModel()->fill($createData);
        $user->save();

        return $user;
    }

    public function update(User $user, array $updateData): User
    {
        $user->update($updateData);

        return $user;
    }

    public function get(array $attributes, bool $first = true)
    {
        $userBuilder = $this->getModel()->where($attributes);

        return $first ? $userBuilder->first() : $userBuilder->get();
    }

    /**
     * @param Team $team
     * @param array $userIds
     * @return int
     */
    public function addToTeam(Team $team, array $userIds): int
    {
        return $this->getModel()
            ->whereIn('id', $userIds)
            ->whereNull('team_id')
            ->update([
               'team_id' => $team->id
            ]);
    }

    /**
     * In provided $userIds there must be at least one user with type manager without team_id
     *
     * @param array $userIds
     * @return mixed
     */
    public function checkManagers(array $userIds = []): Collection
    {
        return $this->getModel()
            ->when(count($userIds) > 0, function (Builder $query) use ($userIds) {
                $query->whereIn('id', $userIds);
            })
            ->where('user_type_id', UserTypes::MANAGER->value)
            ->whereNull('team_id')
            ->get();

    }

    /**
     * For a given userId retrieve all users within the same team including leaves data
     * @param int $userId
     * @param array $leaveStatuses
     * @return Collection
     */
    public function getUserTeamLeavesData(int $userId, array $leaveStatuses): Collection
    {
        $rawSqlString = 'select team_id from users where id = ?';

        return $this->getModel()::with(['leaves' => function ($query) use ($leaveStatuses) {
            $query->whereIn('leave_status_id', $leaveStatuses);
        }])
            ->whereHas('leaves', function (Builder $query) use ($leaveStatuses) {
                $query->whereIn('leave_status_id', $leaveStatuses);
            })
            ->where('team_id', '=', DB::select($rawSqlString,  [$userId])[0]->team_id)
            ->get();
    }
}
