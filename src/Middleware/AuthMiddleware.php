<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthMiddleware {
    /**
     * Authenticates the request using JWT and returns the decoded payload.
     */
    public static function authenticate() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Authorization token missing or malformed"]);
            exit;
        }

        $token = $matches[1];

        try {
            // JWT_SECRET is defined in config/app.php
            $decoded = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
            return $decoded;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Unauthorized access: " . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Backward compatibility with existing routing which calls handle()
     */
    public static function handle() {
        return self::authenticate();
    }
}
