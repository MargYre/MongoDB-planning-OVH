<?php
class Database {
    private $db;

    public function __construct() {
        try {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
            $dotenv->load();
            
            if (!isset($_ENV['MONGODB_URI'])) {
                throw new Exception('MONGODB_URI non définie dans .env');
            }
            
            $options = [
                'connectTimeoutMS' => 2000,
                'retryWrites' => true
            ];
            
            $client = new MongoDB\Client($_ENV['MONGODB_URI'], $options);
            
            // Test de connexion
            $client->listDatabases();
            
            $this->db = $client->planning;
            
        } catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            die('Erreur de connexion MongoDB : délai dépassé');
        } catch (MongoDB\Driver\Exception\AuthenticationException $e) {
            die('Erreur d\'authentification MongoDB : vérifiez vos identifiants');
        } catch (Exception $e) {
            die('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }

    // Ajout de la méthode getDb() manquante
    public function getDb() {
        if (!$this->db) {
            throw new Exception('La connexion à la base de données n\'a pas été établie');
        }
        return $this->db;
    }

    // Méthode utilitaire pour vérifier la connexion
    public function isConnected() {
        return $this->db !== null;
    }
}