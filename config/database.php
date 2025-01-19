<?php
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

try {
    $client = new MongoDB\Client($uri);
    $db = $client->planning; // nom de votre base
} catch(Exception $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>