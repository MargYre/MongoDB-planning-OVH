<?php
class Database {
    private $db;

    public function __construct() {
        try {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
            $dotenv->load();
            
            $client = new MongoDB\Client($_ENV['MONGODB_URI']);
            $this->db = $client->planning;
        } catch (Exception $e) {
            die('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }

    public function getDb() {
        return $this->db;
    }
}