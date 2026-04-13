<?php

namespace App\Controllers;

use App\Models\Blog;
use Exception;

class BlogController extends Controller {
    private $blogModel;

    public function __construct() {
        $this->blogModel = new Blog();
    }

    public function getAllPublished() {
        try {
            $blogs = $this->blogModel->getPublished();
            $this->response(["status" => "success", "data" => $blogs]);
        } catch (Exception $e) {
            $this->response(["status" => "error", "message" => $e->getMessage()], 500);
        }
    }

    public function getById($id) {
        try {
            $blog = $this->blogModel->findById($id);
            if ($blog) {
                $this->response(["status" => "success", "data" => $blog]);
            } else {
                $this->response(["status" => "error", "message" => "Blog post not found"], 404);
            }
        } catch (Exception $e) {
            $this->response(["status" => "error", "message" => $e->getMessage()], 500);
        }
    }

    public function getBySlug($slug) {
        try {
            $blog = $this->blogModel->findBySlug($slug);
            if ($blog) {
                // Increment views for public access by slug
                $this->blogModel->incrementView($blog['id']);
                $this->response(["status" => "success", "data" => $blog]);
            } else {
                $this->response(["status" => "error", "message" => "Blog post not found"], 404);
            }
        } catch (Exception $e) {
            $this->response(["status" => "error", "message" => $e->getMessage()], 500);
        }
    }

    public function incrementView($id) {
        try {
            if ($this->blogModel->incrementView($id)) {
                $this->response(["status" => "success", "message" => "View incremented"]);
            } else {
                $this->response(["status" => "error", "message" => "Failed to increment view"], 400);
            }
        } catch (Exception $e) {
            $this->response(["status" => "error", "message" => $e->getMessage()], 500);
        }
    }

    public function getAllDrafts() {
        try {
            $drafts = $this->blogModel->getDrafts();
            $this->response(["status" => "success", "data" => $drafts]);
        } catch (Exception $e) {
            $this->response(["status" => "error", "message" => $e->getMessage()], 500);
        }
    }

    public function create() {
        try {
            $rawInput = file_get_contents("php://input");

            // Guard: reject payloads larger than 64MB (matches max_allowed_packet in my.ini)
            $maxBytes = 64 * 1024 * 1024; // 64MB
            if (strlen($rawInput) > $maxBytes) {
                $this->response(["status" => "error", "message" => "Request payload too large. Maximum allowed size is 64MB."], 413);
            }

            $data = json_decode($rawInput, true);

            if (empty($data['title']) || empty($data['content'])) {
                $this->response(["status" => "error", "message" => "Title and content are required"], 400);
            }

            // Generate slug
            $data['slug'] = $this->createSlug($data['title']);

            $id = $this->blogModel->create($data);
            if ($id) {
                $this->response(["status" => "success", "message" => "Blog created", "id" => $id], 201);
            } else {
                $this->response(["status" => "error", "message" => "Failed to create blog"], 500);
            }
        } catch (Exception $e) {
            $this->response(["status" => "error", "message" => $e->getMessage()], 500);
        }
    }

    public function update($id) {
        try {
            $rawInput = file_get_contents("php://input");

            // Guard: reject payloads larger than 64MB (matches max_allowed_packet in my.ini)
            $maxBytes = 64 * 1024 * 1024; // 64MB
            if (strlen($rawInput) > $maxBytes) {
                $this->response(["status" => "error", "message" => "Request payload too large. Maximum allowed size is 64MB."], 413);
            }

            $data = json_decode($rawInput, true);

            if (empty($data['title']) || empty($data['content'])) {
                $this->response(["status" => "error", "message" => "Title and content are required"], 400);
            }

            if ($this->blogModel->update($id, $data)) {
                $this->response(["status" => "success", "message" => "Blog updated"]);
            } else {
                $this->response(["status" => "error", "message" => "Failed to update blog"], 400);
            }
        } catch (Exception $e) {
            $this->response(["status" => "error", "message" => $e->getMessage()], 500);
        }
    }

    public function publish($id) {
        try {
            if ($this->blogModel->publish($id)) {
                $this->response(["status" => "success", "message" => "Blog published"]);
            } else {
                $this->response(["status" => "error", "message" => "Failed to publish blog"], 400);
            }
        } catch (Exception $e) {
            $this->response(["status" => "error", "message" => $e->getMessage()], 500);
        }
    }

    public function delete($id) {
        try {
            if ($this->blogModel->delete($id)) {
                $this->response(["status" => "success", "message" => "Blog deleted successfully"]);
            } else {
                $this->response(["status" => "error", "message" => "Failed to delete blog. Entry not found or already removed."], 404);
            }
        } catch (Exception $e) {
            $this->response(["status" => "error", "message" => $e->getMessage()], 500);
        }
    }

    private function createSlug($title) {
        $slug = preg_replace('~[^\pL\d]+~u', '-', $title);
        $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
        $slug = preg_replace('~[^-\w]+~', '', $slug);
        $slug = trim($slug, '-');
        $slug = preg_replace('~-+~', '-', $slug);
        $slug = strtolower($slug);

        if (empty($slug)) {
            return 'n-a';
        }

        return $slug;
    }
}
