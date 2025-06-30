<?php

namespace App\Services;

use App\DTOs\AuthTelegramDTO;
use App\Exceptions\Api\Auth\InvalidRefreshTokenException;
use App\Exceptions\API\Auth\UnauthorizedException;
use App\Repositories\UserRepository;
use Azate\LaravelTelegramLoginAuth\TelegramLoginAuth;
use Exception;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TelegramLoginAuth $telegramLoginAuth
    ) {}

    public function checkTelegramHash($initData)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');

        // Разобрать строку
        parse_str($initData, $data);

        // Проверка наличия hash
        if (!isset($data['hash'])) {
            return response()->json(['error' => 'Hash not found'], 401);
        }

        // Проверка подписи
        $hash = $data['hash'];
        unset($data['hash']);

        ksort($data);
        $checkString = '';
        foreach ($data as $k => $v) {
            $checkString .= "$k=$v\n";
        }
        $checkString = rtrim($checkString, "\n");

        $secretKey = hash('sha256', $botToken, true);
        $calculatedHash = hash_hmac('sha256', $checkString, $secretKey);

        if (!hash_equals($calculatedHash, $hash)) {
            return response()->json(['error' => 'Invalid Telegram hash'], 401);
        }

        // Проверяем наличие данных пользователя
        if (!isset($data['user'])) {
            return response()->json(['error' => 'User data not found'], 401);
        }

        return json_decode($data['user'], true);
    }

    public function login($requestData)
    {
        $userData = $this->checkTelegramHash($requestData['initData']);

        $user = $this->userRepository->findUserByTelegramId($userData["id"]);

        if(!$user) throw new UnauthorizedException();

        if (is_null($user['refresh_token_expires_at']) || $user['refresh_token_expires_at'] <= now())
        {
            $refreshToken = hash('sha256', Str::random(60));

            $user = $this->userRepository->update(
                user: $user,
                data: [
                    "refresh_token" => $refreshToken,
                    "refresh_token_expires_at" => now()->addDays(7)
                ]);
        }

        $user["access_token"] = JWTAuth::fromUser($user);

        return $user;
    }

    public function register($requestData)
    {
        $userData = $this->checkTelegramHash($requestData['initData']);

        $user = $this->userRepository->create([
            "telegram_id" => $userData["id"],
            "name" => $requestData->name,
            'username' => $userData["username"],
            "avatar" => $userData["avatar"],
            "auth_date" => $userData["auth_date"],
            "refresh_token" => hash('sha256', Str::random(60)),
            "refresh_token_expires_at" => now()->addDays(7)
        ]);

        $user["access_token"] = JWTAuth::fromUser($user);

        return $user;
    }

    public function refresh($refresh_token): string
    {
        if (!$refresh_token) throw new InvalidRefreshTokenException();

        $user = $this->userRepository->findByRefreshToken($refresh_token);

        if (!$user) throw new InvalidRefreshTokenException();

        if($user['refresh_token_expires_at'] <= now()) throw new InvalidRefreshTokenException();

        $access_token = JWTAuth::fromUser($user);

        return $access_token;
    }
}
