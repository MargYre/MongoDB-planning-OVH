<?php
class Database {
    private $client;
    private $db;

    public function __construct() {
        try {
            $this->client = new MongoDB\Client("mongodb://localhost:27017");
            $this->db = $this->client->planning_patates;
        } catch(Exception $e) {
            die("Erreur de connexion MongoDB : " . $e->getMessage());
        }
    }

    public function getDb() {
        return $this->db;
    }
}