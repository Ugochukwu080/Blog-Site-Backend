<?php

namespace App\Models;

use PDO;

class Book extends Model {
    protected $table = 'books';

    public function findAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findBySlug($slug) {
        $query = "SELECT * FROM " . $this->table . " WHERE slug = :slug LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table . " (title, slug, description, price, stock, cover_image, category, format) 
                  VALUES (:title, :slug, :description, :price, :stock, :cover_image, :category, :format)";
        
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':slug', $data['slug']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':stock', $data['stock']);
        $stmt->bindParam(':cover_image', $data['cover_image']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':format', $data['format']);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET title = :title, description = :description, price = :price, stock = :stock, cover_image = :cover_image, category = :category, format = :format 
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':stock', $data['stock']);
        $stmt->bindParam(':cover_image', $data['cover_image']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':format', $data['format']);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
