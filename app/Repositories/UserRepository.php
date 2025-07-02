<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function findUserByTelegramId($telegramId)
    {
        return User::query()->where('telegram_id', $telegramId)->first();
    }

    public function create($data)
    {
        $user = User::query()->create($data);
        return $user->refresh();
    }

    public function update($user, $data)
    {
        return $user->update($data);
    }

    public function findByRefreshToken($refresh_token)
    {
        return User::query()->where('refresh_token', $refresh_token)->first();
    }

    public function getFilteredUsers($request)
    {
        $users =  User::query()
        ->where('name', 'like', '%' . $request->name . '%');

        if($request->sortBy === 'count_spins')
        {
            $users->orderBy('count_spins', 'asc');
        }
        if($request->sortBy === 'count_memes')
        {
            $users = $users->when('user_memes', fn($q) => $q->orderBy('count', 'asc'));
        }

        return $users->get();
    }
}
