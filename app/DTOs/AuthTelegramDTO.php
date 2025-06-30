<?php

namespace App\DTOs;

class AuthTelegramDTO
{
    public function __construct(
        public readonly array $user,
        public readonly int $auth_date,
        public readonly string $hash,
    ) {}

    public static function fromRequest(array $data)
    {
        return new self(
            user: $data['user'],
            auth_date: $data['auth_date'],
            hash: $data['hash']
        );
    }
}
