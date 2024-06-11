<?php

namespace App\Repositories;

use App\Models\User;

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
}
