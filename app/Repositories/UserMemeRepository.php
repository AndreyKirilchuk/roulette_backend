<?php

namespace App\Repositories;

use App\Models\Meme;
use App\Models\User;
use App\Models\UserMemes;
use Illuminate\Support\Facades\DB;

class UserMemeRepository
{
    public function incrementOrCreate($userId, $memeId)
    {
        return UserMemes::query()->updateOrCreate(
            ['user_id' => $userId, 'meme_id' => $memeId],
            ['count' => DB::raw('COALESCE(count, 0) + 1')]
        );
    }

    public function firstWhere($userId, $memeId)
    {
        return UserMemes::query()->where(['user_id' => $userId, 'meme_id' => $memeId])->first();
    }
}
