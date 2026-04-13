<?php

namespace App\Controllers;

use App\Models\Admin;
use App\Middleware\AuthMiddleware;
use Firebase\JWT\JWT;
use Exception;

class AuthController extends Controller {
    private $adminModel;

    public function __construct() {
        $this->adminModel = new Admin();
    }

    public function login() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (empty($data['username']) || empty($data['password'])) {
                $this->response(["status" => "error", "message" => "Username and password are required"], 400);
            }

            $admin = $this->adminModel->findByUsername($data['username']);

            // password_verify auto-detects the algorithm (BCrypt) from the stored hash
            if (!$admin || !password_verify($data['password'], $admin['password'])) {
                $this->response(["status" => "error", "message" => "Invalid credentials"], 401);
            }

            // Update last login info
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            $this->adminModel->updateLastLogin($admin['id'], $ip);

            // Generate JWT
            $payload = [
                'iat' => time(),
                'exp' => time() + (2 * 60 * 60), // 2 hours expiration
                'admin_id' => $admin['id'],
                'username' => $admin['username']
            ];

            $token = JWT::encode($payload, JWT_SECRET, 'HS256');

            $this->response([
                "status" => "success",
                "message" => "Login successful",
                "token" => $token
            ]);

        } catch (Exception $e) {
            $this->response(["status" => "error", "message" => "Internal error: " . $e->getMessage()], 500);
        }
    }

    public function changePassword() {
        try {
            // Security First: Authenticate and extract admin_id
            $decodedToken = AuthMiddleware::authenticate();
            $admin_id = $decodedToken->admin_id;

            $data = json_decode(file_get_contents("php://input"), true);

            if (empty($data['old_password']) || empty($data['new_password'])) {
                $this->response(["status" => "error", "message" => "Old and new passwords are required"], 400);
            }

            // Fetch admin from DB to verify old password
            $admin = $this->adminModel->findById($admin_id); // Assuming Model::findById works for Admin too or adding it

            // password_verify auto-detects the algorithm (BCrypt) from the stored hash
            if (!$admin || !password_verify($data['old_password'], $admin['password'])) {
                $this->response(["status" => "error", "message" => "Invalid old password"], 401);
            }

            // Hash new password using BCrypt with an explicit cost of 12 (default is 10)
            // Higher cost = more secure, but slower. 12 is a good balance for production.
            $hashed_password = password_hash($data['new_password'], PASSWORD_BCRYPT, ['cost' => 12]);

            // Update password
            if ($this->adminModel->updatePassword($admin_id, $hashed_password)) {
                $this->response(["status" => "success", "message" => "Password updated successfully"]);
            } else {
                $this->response(["status" => "error", "message" => "Failed to update password"], 500);
            }

        } catch (Exception $e) {
            $this->response(["status" => "error", "message" => "Internal error: " . $e->getMessage()], 500);
        }
    }
}
