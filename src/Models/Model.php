<?php

namespace App\Models;

use App\Config\Database;
use PDO;

abstract class Model {
    protected $db;
    protected $table;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function all() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
