<?php
require_once __DIR__ . '/vendor/autoload.php';  // Ajouter cette ligne
session_start();
require_once 'config/database.php';
require_once 'controllers/PlanningController.php';
require_once 'controllers/UserController.php';

$database = new Database();
$planningController = new PlanningController($database);
$userController = new UserController($database);

// Utiliser $_GET['action'] au lieu de $uri pour l'instant
$action = isset($_GET['action']) ? $_GET['action'] : 'display';

switch($action) {
    case 'login':
        $userController->login();
        break;
    case 'auth/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            if ($userController->authenticate($username, $password)) {
                header('Location: index.php');
                exit;
            }
        }
        $userController->login();
        break;
    case 'logout':
        $userController->logout();
        break;
    default:
        $planningController->display();
        break;
}