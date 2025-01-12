<?php
class Database {
    private $host = "localhost";
    private $database = "planning_patates";
    private $username = ""; 
    private $password = ""; 
    public $conn;
    public $db;

    public function getConnection() {
        try {
            $this->conn = new MongoDB\Client("mongodb://localhost:27017");
            $this->db = $this->conn->planning_patates;
            return $this->db;
        } catch(Exception $e) {
            echo "Erreur de connexion : " . $e->getMessage();
            return null;
        }
    }
}