<?php

namespace App\Models;

use PDO;

class Blog extends Model {
    protected $table = 'blogs';

    public function getPublished() {
        $query = "SELECT * FROM " . $this->table . " WHERE status = 'published' ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDrafts() {
        $query = "SELECT * FROM " . $this->table . " WHERE status = 'draft' ORDER BY created_at DESC";
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
        $query = "SELECT * FROM " . $this->table . " WHERE slug = :slug AND status = 'published' LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table . " (title, slug, tagline, content, cover_image, status) 
                  VALUES (:title, :slug, :tagline, :content, :cover_image, :status)";
        
        $stmt = $this->db->prepare($query);

        $status = $data['status'] ?? 'draft';

        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':slug', $data['slug']);
        $stmt->bindParam(':tagline', $data['tagline']);
        $stmt->bindParam(':content', $data['content']);
        $stmt->bindParam(':cover_image', $data['cover_image']);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET title = :title, tagline = :tagline, content = :content, cover_image = :cover_image 
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':tagline', $data['tagline']);
        $stmt->bindParam(':content', $data['content']);
        $stmt->bindParam(':cover_image', $data['cover_image']);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function publish($id) {
        $query = "UPDATE " . $this->table . " SET status = 'published' WHERE id = :id";
        $stmt = $this->db->prepare($query);
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

    public function incrementView($id) {
        $query = "UPDATE " . $this->table . " SET views = views + 1 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
