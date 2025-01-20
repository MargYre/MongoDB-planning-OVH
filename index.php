<?php
require_once __DIR__ . '/vendor/autoload.php';
session_start();
require_once 'config/database.php';
require_once 'controllers/PlanningController.php';
require_once 'controllers/UserController.php';

$database = new Database();
$planningController = new PlanningController($database);
$userController = new UserController($database);

// Routing principal
$action = isset($_GET['action']) ? $_GET['action'] : 'display';

switch($action) {
    // Routes d'authentification
    case 'login':
        $userController->login();
        break;
    case 'auth/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            if ($userController->authenticate($username, $password)) {
                header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'index.php');
                exit;
            }
        }
        $userController->login();
        break;
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userData = [
                'username' => $_POST['username'] ?? '',
                'password' => $_POST['password'] ?? '',
                'color' => $_POST['color'] ?? '#000000'
            ];
            if ($userController->register($userData)) {
                header('Location: index.php?action=login&registered=1');
                exit;
            }
        }
        $userController->showRegisterForm();
        break;
    case 'logout':
        $userController->logout();
        break;
        
    // Routes du planning
    case 'update_planning':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login&error=auth_required');
            exit;
        }
        $planningController->update($_POST);
        break;
    default:
        $planningController->display();
        break;
}