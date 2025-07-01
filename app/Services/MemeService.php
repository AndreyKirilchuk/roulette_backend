<?php

namespace App\Services;

use App\Repositories\MemeRepository;

class MemeService
{
    public function __construct(
        private readonly MemeRepository $memeRepository
    ) {}

    public function rotation()
    {

    }
}
