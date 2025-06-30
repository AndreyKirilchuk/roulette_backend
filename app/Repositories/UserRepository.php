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
        return $user->update($data)->refresh();
    }

    public function findByRefreshToken($refresh_token)
    {
        return User::query()->where('refresh_token', $refresh_token)->first();
    }
}
