<?php

namespace App\Controllers;

use App\Models\Admin;
use App\Middleware\AuthMiddleware;
use Exception;

class SettingsController extends Controller {
    private $adminModel;

    public function __construct() {
        $this->adminModel = new Admin();
    }

    public function get() {
        try {
            $admin = $this->adminModel->getFirst();
            if (!$admin) {
                $this->response(["status" => "error", "message" => "Settings not found"], 404);
            }

            // Strictly remove sensitive and internal info for public consumption
            unset($admin['password']);
            unset($admin['username']);
            unset($admin['last_login']);
            unset($admin['last_login_ip']);
            unset($admin['created_at']);

            $this->response(["status" => "success", "data" => $admin]);
        } catch (Exception $e) {
            $this->response(["status" => "error", "message" => $e->getMessage()], 500);
        }
    }

    public function update() {
        try {
            $decodedToken = AuthMiddleware::authenticate();
            $admin_id = $decodedToken->admin_id;

            $data = json_decode(file_get_contents("php://input"), true);

            // Default values to preserve existing if not provided
            $admin = $this->adminModel->findById($admin_id);
            if (!$admin) {
                $this->response(["status" => "error", "message" => "Admin not found"], 404);
            }

            $settings = [
                'display_name' => $data['display_name'] ?? $admin['display_name'],
                'email' => $data['email'] ?? $admin['email'],
                'avatar' => $data['avatar'] ?? $admin['avatar'],
                'biography' => $data['biography'] ?? $admin['biography'],
                'brand_name' => $data['brand_name'] ?? $admin['brand_name'],
                'mission_statement' => $data['mission_statement'] ?? $admin['mission_statement'],
                'copyright_notice' => $data['copyright_notice'] ?? $admin['copyright_notice'],
                'twitter_url' => $data['twitter_url'] ?? $admin['twitter_url'],
                'github_url' => $data['github_url'] ?? $admin['github_url'],
                'linkedin_url' => $data['linkedin_url'] ?? $admin['linkedin_url']
            ];

            if ($this->adminModel->updateSettings($admin_id, $settings)) {
                $this->response(["status" => "success", "message" => "Settings updated successfully"]);
            } else {
                $this->response(["status" => "error", "message" => "Failed to update settings"], 500);
            }
        } catch (Exception $e) {
            $this->response(["status" => "error", "message" => $e->getMessage()], 500);
        }
    }
}
