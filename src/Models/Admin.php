<?php

namespace App\Models;

use PDO;

class Admin extends Model {
    protected $table = 'admin';

    public function findById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getFirst() {
        $query = "SELECT * FROM " . $this->table . " LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByUsername($username) {
        $query = "SELECT * FROM " . $this->table . " WHERE username = :username LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateLastLogin($id, $ip_address) {
        $query = "UPDATE " . $this->table . " SET last_login = NOW(), last_login_ip = :ip WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':ip', $ip_address);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updatePassword($id, $hashed_password) {
        $query = "UPDATE " . $this->table . " SET password = :password WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updateSettings($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET display_name = :display_name, 
                      email = :email, 
                      avatar = :avatar, 
                      biography = :biography, 
                      brand_name = :brand_name, 
                      mission_statement = :mission_statement, 
                      copyright_notice = :copyright_notice, 
                      twitter_url = :twitter_url, 
                      github_url = :github_url, 
                      linkedin_url = :linkedin_url 
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':display_name', $data['display_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':avatar', $data['avatar']);
        $stmt->bindParam(':biography', $data['biography']);
        $stmt->bindParam(':brand_name', $data['brand_name']);
        $stmt->bindParam(':mission_statement', $data['mission_statement']);
        $stmt->bindParam(':copyright_notice', $data['copyright_notice']);
        $stmt->bindParam(':twitter_url', $data['twitter_url']);
        $stmt->bindParam(':github_url', $data['github_url']);
        $stmt->bindParam(':linkedin_url', $data['linkedin_url']);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
}
