<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function users($request)
    {
        return $this->userRepository->getFilteredUsers($request);
    }
}
