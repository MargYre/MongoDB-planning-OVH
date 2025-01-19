<?php
session_start();
require_once 'config/database.php';
require_once 'controllers/PlanningController.php';
require_once 'controllers/UserController.php';

$database = new Database();
$planningController = new PlanningController($database);

$action = isset($_GET['action']) ? $_GET['action'] : 'display';

switch($action) {
    case 'login':
        $userController = new UserController($database);
        $userController->login();
        break;
    case 'logout':
        $userController = new UserController($database);
        $userController->logout();
        break;
    default:
        $planningController->display();
        break;
}