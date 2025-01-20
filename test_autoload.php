<?php
require_once __DIR__ . '/vendor/autoload.php';

try {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    echo "Connexion réussie à MongoDB !\n";
    $databases = $client->listDatabases();
    foreach ($databases as $database) {
        echo "Base de données : " . $database['name'] . "\n";
    }
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
?>
