<?php

namespace App\Repositories;

use App\Models\Meme;
use App\Models\User;

class MemeRepository
{
    public function get()
    {
        return Meme::all();
    }

    public function whereGet($column, $value)
    {
        return Meme::query()->where($column, $value)->get();
    }

    public function find($id)
    {
       return Meme::query()->find($id);
    }
}
