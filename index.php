<?php
// Définir la gestion des erreurs pour les requêtes AJAX
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        http_response_code(500);
        echo json_encode([
            'error' => 'Erreur serveur: ' . $errstr
        ]);
        exit;
    }
    return false;
});

define('ROOT_PATH', __DIR__);
require_once ROOT_PATH . '/vendor/autoload.php';
session_start();

// Charger les dépendances
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/services/PlanningService.php';  // Ajout du service
require_once ROOT_PATH . '/controllers/PlanningController.php';
require_once ROOT_PATH . '/controllers/UserController.php';

try {
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
                    $redirect = isset($_SESSION['redirect_after_login']) 
                        ? $_SESSION['redirect_after_login'] 
                        : 'index.php';
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
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                    if (!headers_sent()) {
                        header('Content-Type: application/json');
                    }
                    http_response_code(401);
                    echo json_encode(['error' => 'auth_required']);
                    exit;
                }
                $_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'] ?? 'index.php';
                header('Location: index.php?action=login&error=auth_required');
                exit;
            }
            if (!headers_sent()) {
                header('Content-Type: application/json');
            }
            echo json_encode($planningController->update($_POST));
            exit;
        default:
            $planningController->display();
            break;
    }
} catch (Exception $e) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
    throw $e;
}