<?php

namespace App\Helper;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTToken
{
    public static function CreateToken($userEmail)
    {
        $key = env('JWT_SECRET');
        $payload = [
            'iss' => 'laravel_token',
            'iat' => time(),
            'exp' => time() + 60 * 60,
            'userEmail' => $userEmail
        ];

        return JWT::encode($payload, $key, 'HS256');
    }

    public static function CreateTokenForResetPass($userEmail)
    {
        $key = env('JWT_SECRET');
        $payload = [
            'iss' => 'laravel_token',
            'iat' => time(),
            'exp' => time() + 60 * 5,
            'userEmail' => $userEmail
        ];

        return JWT::encode($payload, $key, 'HS256');
    }

    public static function VerifyToken($token)
    {
        try {
            $key = env('JWT_SECRET');
            $decode = JWT::decode($token, new Key($key, 'HS256'));

            return $decode->userEmail;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}