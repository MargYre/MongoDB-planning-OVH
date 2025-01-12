<?php
require_once __DIR__ . '/../vendor/autoload.php';  // Pour charger les dépendances
require 'config/database.php';
require 'controllers/PlanningController.php';

$database = new Database();
$db = $database->getConnection();
$controller = new PlanningController($db);
$controller->index();