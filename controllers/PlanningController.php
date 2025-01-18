<?php
class PlanningController {
    private $db;

    public function __construct($database) {
        $this->db = $database->getDb();
    }

    public function display() {
        require 'views/planning.php';
    }
}