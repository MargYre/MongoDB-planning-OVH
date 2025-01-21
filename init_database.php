<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';

$database = new Database();
$db = $database->getDb();

// Création des utilisateurs
$users = [
    ['username' => 'vincent', 'password' => password_hash('vincent123', PASSWORD_DEFAULT), 'color' => '#FF0000'],
    ['username' => 'david', 'password' => password_hash('david123', PASSWORD_DEFAULT), 'color' => '#00FF00'],
    ['username' => 'thomas', 'password' => password_hash('thomas123', PASSWORD_DEFAULT), 'color' => '#0000FF'],
    ['username' => 'christophe', 'password' => password_hash('christophe123', PASSWORD_DEFAULT), 'color' => '#FFA500']
];

try {
    // Supprimer la collection si elle existe déjà
    $db->users->drop();
    
    // Insérer les utilisateurs
    $result = $db->users->insertMany($users);
    echo "Utilisateurs créés avec succès : " . $result->getInsertedCount() . " utilisateurs insérés\n";

    // Générer le planning pour 2021-2025
    $planning = [];
    $date = new DateTime('2021-01-03'); // Premier dimanche de 2021
    $userCount = count($users);
    $userIndex = 0;

    // Boucle sur les années de 2021 à 2025
    for ($year = 2021; $year <= 2025; $year++) {
        // Trouver le premier dimanche de l'année
        if ($year > 2021) {
            $date = new DateTime($year . '-01-01');
            while ($date->format('w') != 0) { // 0 = dimanche
                $date->modify('+1 day');
            }
        }

        // Générer les semaines pour l'année courante
        for ($week = 1; $week <= 52; $week++) {
            $user = $users[$userIndex];
            $planning[] = [
                'year' => $year,
                'week' => $week,
                'date' => new MongoDB\BSON\UTCDateTime($date->getTimestamp() * 1000),
                'user_id' => $result->getInsertedIds()[$userIndex],
                'username' => $user['username']
            ];
            
            $date->modify('+1 week');
            $userIndex = ($userIndex + 1) % $userCount;
        }
    }

    // Supprimer l'ancien planning s'il existe
    $db->planning->drop();
    
    // Insérer le nouveau planning
    $result = $db->planning->insertMany($planning);
    echo "Planning créé avec succès : " . $result->getInsertedCount() . " semaines insérées\n";

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}