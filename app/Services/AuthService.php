<?php

namespace App\Services;

use App\DTOs\AuthTelegramDTO;
use App\Exceptions\Api\Auth\InvalidRefreshTokenException;
use App\Exceptions\API\Auth\UnauthorizedException;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function checkTelegramHash($initData)
    {
        // 1. Разбираем строку на параметры
        parse_str($initData, $receivedData);

        // 2. Проверяем наличие обязательных полей
        if (!isset($receivedData['hash'], $receivedData['auth_date'], $receivedData['user'])) {
            throw new Exception('Invalid initData: missing required fields');
        }

        // 3. Извлекаем hash для дальнейшей проверки
        $receivedHash = $receivedData['hash'];
        unset($receivedData['hash']); // Убираем hash из данных для проверки

        // 4. Сортируем поля по алфавиту и формируем data_check_string
        ksort($receivedData);
        $dataCheckString = [];
        foreach ($receivedData as $key => $value) {
            $dataCheckString[] = "$key=$value";
        }
        $dataCheckString = implode("\n", $dataCheckString);

        // 5. Генерируем секретный ключ (HMAC-SHA-256 от токена бота + "WebAppData")
        $secretKey = hash_hmac(
            'sha256',
            getenv('TELEGRAM_BOT_TOKEN'), // Токен бота из переменных окружения
            'WebAppData',
            true
        );

        // 6. Считаем HMAC-SHA-256 от data_check_string
        $calculatedHash = bin2hex(
            hash_hmac('sha256', $dataCheckString, $secretKey, true)
        );

        // 7. Сравниваем полученный hash и вычисленный
        if (!hash_equals($calculatedHash, $receivedHash)) {
            throw new Exception('Invalid initData: hash mismatch');
        }

        // 8. Проверяем, что данные не устарели (например, не старше 1 дня)
        $authDate = (int)$receivedData['auth_date'];
        if (time() - $authDate > 86400) {
            throw new Exception('initData is too old');
        }

        // 9. Декодируем user (если нужно)
        $receivedData['user'] = json_decode($receivedData['user'], true);

        return $receivedData;
    }

    public function login($requestData)
    {
        $initData = $this->checkTelegramHash($requestData['initData']);

        $userData = $initData['user'];

        $user = $this->userRepository->findUserByTelegramId($userData["id"]);

        if(!$user) throw new UnauthorizedException();

        if (is_null($user['refresh_token_expires_at']) || $user['refresh_token_expires_at'] <= now())
        {
            $refreshToken = hash('sha256', Str::random(60));

            $user = $this->userRepository->update(
                user: $user,
                data: [
                    "refresh_token" => $refreshToken,
                    "refresh_token_expires_at" => now()->addDays(30)
                ]);
        }

        $user["access_token"] = JWTAuth::fromUser($user);

        return $user;
    }

    public function register($requestData)
    {
        $initData = $this->checkTelegramHash($requestData['initData']);

        $userData = $initData['user'];

        $user = $this->userRepository->create([
            "telegram_id" => $userData["id"],
            "name" => $requestData['name'],
            'username' => $userData["username"],
            "avatar" => $userData["photo_url"],
            "auth_date" => $initData["auth_date"],
            "refresh_token" => hash('sha256', Str::random(60)),
            "refresh_token_expires_at" => now()->addDays(30)
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
