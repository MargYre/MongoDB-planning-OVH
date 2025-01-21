<?php
// index.php
define('ROOT_PATH', __DIR__);
require_once ROOT_PATH . '/vendor/autoload.php';
session_start();

require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/controllers/PlanningController.php';
require_once ROOT_PATH . '/controllers/UserController.php';

$database = new Database();
$planningController = new PlanningController($database);
$userController = new UserController($database);

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
                // Redirection vers la page précédente si elle existe
                $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'index.php';
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
                exit;
            }
        }
        $userController->login();
        break;
    case 'logout':
        $userController->logout();
        break;
    case 'update_planning':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'] ?? 'index.php';
            header('Location: index.php?action=login&error=auth_required');
            exit;
        }
        $planningController->update($_POST);
        break;
    default:
        $planningController->display();
        break;
}