<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';  // Utilisation de dirname() pour un chemin plus fiable

try {
    // Configuration
    $host = "localhost";
    $port = 27017;
    $database = "planning_patates";
    
    // Connexion
    $client = new MongoDB\Client("mongodb://{$host}:{$port}");
    $db = $client->selectDatabase($database);
    
    // Création de la collection users
    if (!in_array('users', iterator_to_array($db->listCollections()))) {
        $db->createCollection('users');
        echo "Collection 'users' créée avec succès!\n";
        
        // Création d'un utilisateur test
        $users = $db->users;
        $result = $users->insertOne([
            'username' => 'admin',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ]);
        echo "Utilisateur test créé avec l'ID : " . $result->getInsertedId() . "\n";
    }
    
    // Création de la collection planning
    if (!in_array('planning', iterator_to_array($db->listCollections()))) {
        $db->createCollection('planning');
        echo "Collection 'planning' créée avec succès!\n";
        
        // Création d'une entrée test dans le planning
        $planning = $db->planning;
        $result = $planning->insertOne([
            'user_id' => 'admin',
            'week_number' => 1,
            'year' => 2025,
            'status' => 'assigned',
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ]);
        echo "Entrée de planning test créée avec l'ID : " . $result->getInsertedId() . "\n";
    }
    
    // Vérification des collections
    echo "\nCollections disponibles maintenant :\n";
    $collections = $db->listCollections();
    foreach ($collections as $collection) {
        echo "- " . $collection->getName() . "\n";
    }

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    exit(1);
}