<?php

class Database {
    private $host = "localhost";
    private $port = 27017;
    private $database = "planning_patates";
    private $username = "";
    private $password = "";
    private $client;
    private $db;

    public function __construct() {
        if (!extension_loaded('mongodb')) {
            throw new Exception("L'extension MongoDB n'est pas installée");
        }

        try {
            // Construction de l'URI
            $uri = "mongodb://";
            if ($this->username && $this->password) {
                $uri .= urlencode($this->username) . ":" . urlencode($this->password) . "@";
            }
            $uri .= $this->host . ":" . $this->port;

            // Options de connexion
            $options = [
                'connectTimeoutMS' => 2000,
                'retryWrites' => true
            ];

            // Création du client
            $this->client = new MongoDB\Client($uri, $options);
            
            // Sélection de la base de données
            $this->db = $this->client->selectDatabase($this->database);
            
            // Test de la connexion
            $this->db->command(['ping' => 1]);
            
        } catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            error_log("Timeout de connexion MongoDB: " . $e->getMessage());
            throw new Exception("Impossible de se connecter à MongoDB (timeout)");
            
        } catch (MongoDB\Driver\Exception\AuthenticationException $e) {
            error_log("Erreur d'authentification MongoDB: " . $e->getMessage());
            throw new Exception("Erreur d'authentification MongoDB");
            
        } catch (Exception $e) {
            error_log("Erreur MongoDB: " . $e->getMessage());
            throw new Exception("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->db;
    }

    public function getClient() {
        return $this->client;
    }

    public function getCollection($name) {
        return $this->db->selectCollection($name);
    }

    public function testConnection() {
        try {
            $collections = $this->db->listCollections();
            return iterator_to_array($collections);
        } catch (Exception $e) {
            throw new Exception("Erreur lors du test de connexion: " . $e->getMessage());
        }
    }
}