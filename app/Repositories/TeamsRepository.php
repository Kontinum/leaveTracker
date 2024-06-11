<?php

namespace App\Repositories;

use App\Models\Team;

class TeamsRepository
{
    /**
     * @return Team
     */
    private function getModel(): Team
    {
        return new Team();
    }

    /**
     * @param array $createData
     * @return Team
     */
    public function create(array $createData): Team
    {
        $team = $this->getModel()->fill($createData);
        $team->save();

        return $team;
    }

    /**
     * @param Team $team
     * @param array $updateData
     * @return Team
     */
    public function update(Team $team, array $updateData): Team
    {
        $team->update($updateData);

        return $team;
    }

}
