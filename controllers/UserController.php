<?php
class UserController {
    private $db;

    public function __construct($database) {
        $this->db = $database->getDb();
    }

    public function login() {
        require 'views/login.php';
    }

    public function authenticate($username, $password) {
        $collection = $this->db->users;
        $user = $collection->findOne(['name' => $username]);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = (string)$user['_id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['color'] = $user['color'];
            return true;
        }
        return false;
    }

    public function logout() {
        session_destroy();
        header('Location: index.php');
        exit();
    }

    public function displayProfile() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $usersCollection = $this->db->users;
        $user = $usersCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])]);
        
        require 'views/profile.php';
    }

    public function updateProfile($data) {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        $usersCollection = $this->db->users;
        $updateData = [
            'color' => $data['color'] ?? $_SESSION['color']
        ];

        // Si un nouveau mot de passe est fourni
        if (!empty($data['new_password'])) {
            if (password_verify($data['current_password'], $user['password'])) {
                $updateData['password'] = password_hash($data['new_password'], PASSWORD_DEFAULT);
            } else {
                return false;
            }
        }

        $result = $usersCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
            ['$set' => $updateData]
        );

        if ($result->getModifiedCount() > 0) {
            $_SESSION['color'] = $updateData['color'];
            return true;
        }

        return false;
    }
}