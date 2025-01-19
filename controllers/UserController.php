<?php
class UserController {
    private $db;

    public function __construct($database) {
        $this->db = $database->getDb();
    }

    public function login() {
        require 'views/login.php';
    }

    public function logout() {
        session_destroy();
        header('Location: index.php');
    }
}