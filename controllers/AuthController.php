<?php
class AuthController {
    private $db;

    public function __construct($database) {
        $this->db = $database->getDb();
    }

    public function authenticate($username, $password) {
        $collection = $this->db->users;
        $user = $collection->findOne(['name' => $username]);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = (string)$user['_id'];
            $_SESSION['username'] = $user['name'];
            return true;
        }
        return false;
    }

    public function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }

    public function logout() {
        session_destroy();
        header('Location: index.php');
        exit();
    }
}