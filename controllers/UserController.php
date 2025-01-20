<?php
class UserController {
    private $db;
    private $error;

    public function __construct($database) {
        $this->db = $database->getDb();
        $this->error = null;
    }

    // Ajout de la méthode login manquante
    public function login() {
        $error = null;
        if (isset($_GET['error']) && $_GET['error'] === 'auth_required') {
            $error = "Vous devez être connecté pour accéder au planning";
        }
        // Afficher le formulaire de connexion
        require ROOT_PATH . '/views/login.php';
    }

    // Méthode pour authentifier l'utilisateur
    public function authenticate($username, $password) {
        $collection = $this->db->users;
        $user = $collection->findOne(['username' => $username]);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = (string)$user['_id'];
            $_SESSION['username'] = $user['username'];
            return true;
        }
        
        $this->error = "Identifiants incorrects";
        return false;
    }

    public function logout() {
        session_destroy();
        header('Location: index.php');
        exit();
    }
}