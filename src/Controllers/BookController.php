<?php

namespace App\Controllers;

use App\Models\Book;
use App\Middleware\AuthMiddleware;
use Exception;

class BookController extends Controller {
    private $bookModel;
    private $uploadDir = 'uploads/books/';

    public function __construct() {
        $this->bookModel = new Book();
    }

    public function getAll() {
        try {
            $books = $this->bookModel->findAll();
            $this->response(["status" => "success", "data" => $books]);
        } catch (Exception $e) {
            $this->response(["status" => "error", "message" => $e->getMessage()], 500);
        }
    }

    public function getById($id) {
        try {
            $book = $this->bookModel->findById($id);
            if ($book) {
                $this->response(["status" => "success", "data" => $book]);
            } else {
                $this->response(["status" => "error", "message" => "Book not found"], 404);
            }
        } catch (Exception $e) {
            $this->response(["status" => "error", "message" => $e->getMessage()], 500);
        }
    }

    public function getBySlug($slug) {
        try {
            $book = $this->bookModel->findBySlug($slug);
            if ($book) {
                $this->response(["status" => "success", "data" => $book]);
            } else {
                $this->response(["status" => "error", "message" => "Book not found"], 404);
            }
        } catch (Exception $e) {
            $this->response(["status" => "error", "message" => $e->getMessage()], 500);
        }
    }

    public function create() {
        AuthMiddleware::authenticate();

        try {
            $data = $_POST;

            if (empty($data['title']) || empty($data['price']) || empty($data['description']) || empty($data['category']) || empty($data['format'])) {
                $this->response(["status" => "error", "message" => "All fields (title, price, description, category, format) are required"], 400);
            }

            // Handle file upload
            $cover_image = "";
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $cover_image = $this->handleUpload($_FILES['cover_image']);
            }
            

            // Sanitize and prepare data
            $data['price'] = (float)$data['price'];
            $data['stock'] = (int)($data['stock'] ?? 0);
            $data['slug'] = $this->createSlug($data['title']);
            $data['cover_image'] = $this->getFullUrl($cover_image);

            $id = $this->bookModel->create($data);
            if ($id) {
                $this->response(["status" => "success", "message" => "Book created", "id" => $id], 201);
            } else {
                $this->response(["status" => "error", "message" => "Failed to create book"], 500);
            }
        } catch (Exception $e) {
            $this->response(["status" => "error", "message" => $e->getMessage()], 400);
        }
    }

    public function update($id) {
        AuthMiddleware::authenticate();

        try {
            $book = $this->bookModel->findById($id);
            if (!$book) {
                $this->response(["status" => "error", "message" => "Book not found"], 404);
            }

            $data = $_POST;
            
            // Keep existing fields if not provided
            $data['title'] = $data['title'] ?? $book['title'];
            $data['description'] = $data['description'] ?? $book['description'];
            $data['category'] = $data['category'] ?? $book['category'];
            $data['format'] = $data['format'] ?? $book['format'];
            $data['price'] = (float)($data['price'] ?? $book['price']);
            $data['stock'] = (int)($data['stock'] ?? $book['stock']);
            $data['cover_image'] = $book['cover_image'];

            // Handle file upload if new one provided
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                // Remove old image
                $this->deleteFile($book['cover_image']);
                
                // Upload new one
                $data['cover_image'] = $this->getFullUrl($this->handleUpload($_FILES['cover_image']));
            }

            if ($this->bookModel->update($id, $data)) {
                $this->response(["status" => "success", "message" => "Book updated"]);
            } else {
                $this->response(["status" => "error", "message" => "Failed to update book"], 500);
            }
        } catch (Exception $e) {
            $this->response(["status" => "error", "message" => $e->getMessage()], 400);
        }
    }

    public function delete($id) {
        AuthMiddleware::authenticate();

        try {
            $book = $this->bookModel->findById($id);
            if (!$book) {
                $this->response(["status" => "error", "message" => "Book not found"], 404);
            }

            // Delete associated image
            $this->deleteFile($book['cover_image']);

            if ($this->bookModel->delete($id)) {
                $this->response(["status" => "success", "message" => "Book deleted successfully"]);
            } else {
                $this->response(["status" => "error", "message" => "Failed to delete book. Entry not found or already removed."], 404);
            }
        } catch (Exception $e) {
            $this->response(["status" => "error", "message" => $e->getMessage()], 500);
        }
    }

    private function handleUpload($file) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowed)) {
            throw new Exception("Invalid file type. Only JPEG, PNG, and WEBP are allowed.");
        }

        if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
            throw new Exception("File too large. Max size is 5MB.");
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $target = 'public/' . $this->uploadDir . $filename;

        // Ensure directory exists
        if (!is_dir('public/' . $this->uploadDir)) {
            mkdir('public/' . $this->uploadDir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $target)) {
            return '/' . $this->uploadDir . $filename;
        }

        throw new Exception("Failed to move uploaded file.");
    }

    private function deleteFile($path) {
        if (!empty($path)) {
            $fullPath = 'public' . $path;
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }

    private function createSlug($title) {
        $slug = preg_replace('~[^\pL\d]+~u', '-', $title);
        $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
        $slug = preg_replace('~[^-\w]+~', '', $slug);
        $slug = trim($slug, '-');
        $slug = preg_replace('~-+~', '-', $slug);
        $slug = strtolower($slug);
        return empty($slug) ? 'n-a' : $slug;
    }
}
