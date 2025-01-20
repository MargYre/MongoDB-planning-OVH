<?php
session_start();

require_once 'config/database.php';
require_once 'controllers/PlanningController.php';
require_once 'controllers/UserController.php';
require_once 'controllers/AuthController.php';

$database = new Database();
$authController = new AuthController($database);
$planningController = new PlanningController($database);
$userController = new UserController($database);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$publicRoutes = [
    '/login' => true,
    '/auth/login' => true,
    '/register' => true,
    '/auth/register' => true
];

if (!isset($publicRoutes[$uri]) && !$authController->isAuthenticated()) {
    header('Location: /login');
    exit();
}

try {
    switch ($uri) {
        case '/login':
            if ($method === 'GET') {
                require 'views/login.php';
            }
            break;

        case '/auth/login':
            if ($method === 'POST') {
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';
                
                if ($authController->login($username, $password)) {
                    header('Location: /planning');
                    exit();
                } else {
                    $error = "Identifiants invalides";
                    require 'views/login.php';
                }
            }
            break;

        case '/logout':
            $authController->logout();
            header('Location: /login');
            exit();
            break;

        case '/planning':
            if ($method === 'GET') {
                $planningController->display();
            }
            break;

        case '/planning/create':
            if ($method === 'POST') {
                $planningController->create($_POST);
            }
            break;

        case '/planning/update':
            if ($method === 'POST') {
                $planningController->update($_POST);
            }
            break;

        case '/user/profile':
            if ($method === 'GET') {
                $userController->displayProfile();
            } elseif ($method === 'POST') {
                $userController->updateProfile($_POST);
            }
            break;

        default:
            if ($authController->isAuthenticated()) {
                header('Location: /planning');
            } else {
                header('Location: /login');
            }
            exit();
    }
} catch (Exception $e) {
    error_log("Erreur : " . $e->getMessage());
    
    $error = "Une erreur est survenue. Veuillez réessayer plus tard.";
    require 'views/error.php';
}