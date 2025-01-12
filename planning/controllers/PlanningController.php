<?php
class PlanningController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }

    public function index() {
        // Test de la connexion
        try {
            $collections = $this->db->listCollections();
            include 'views/planning/index.php';
        } catch(Exception $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }
}