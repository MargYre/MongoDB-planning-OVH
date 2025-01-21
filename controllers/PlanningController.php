<?php

class PlanningController {
    private $db;
    private $database;

    public function __construct($database) {
        $this->database = $database;
        $this->db = $database->getDb();
    }

    public function display() {
        try {
            $selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
            $years = $this->getAvailableYears();
            $planning = $this->getYearlyPlanning($selectedYear);
            $stats = $this->getStats($selectedYear);
            $users = $this->getUsers();
            
            if (empty($years)) {
                throw new RuntimeException('Aucune année disponible dans le planning');
            }
            
            require ROOT_PATH . '/views/planning.php';
        } catch (Exception $e) {
            error_log("Erreur dans display(): " . $e->getMessage());
            echo "Une erreur est survenue lors du chargement du planning.";
        }
    }

    private function getAvailableYears() {
        try {
            $pipeline = [
                ['$group' => ['_id' => '$year']],
                ['$sort' => ['_id' => 1]]
            ];
            
            $years = [];
            $cursor = $this->db->planning->aggregate($pipeline);
            foreach ($cursor as $document) {
                $years[] = $document->_id;
            }
            return $years;
        } catch (MongoDB\Driver\Exception $e) {
            error_log("Erreur MongoDB dans getAvailableYears(): " . $e->getMessage());
            throw new RuntimeException('Erreur lors de la récupération des années');
        }
    }

    private function getYearlyPlanning($year) {
        try {
            $cursor = $this->db->planning->find(
                ['year' => (int)$year],
                [
                    'sort' => ['week' => 1],
                    'projection' => [
                        'date' => 1,
                        'week' => 1,
                        'username' => 1,
                        'user_id' => 1
                    ]
                ]
            );
            
            $planning = [];
            foreach ($cursor as $document) {
                $planning[] = [
                    'date' => $document['date']->toDateTime(),
                    'week' => $document['week'],
                    'username' => $document['username'] ?? 'Non assigné',
                    'user_id' => isset($document['user_id']) ? (string)$document['user_id'] : null
                ];
            }
            return $planning;
        } catch (MongoDB\Driver\Exception $e) {
            error_log("Erreur MongoDB dans getYearlyPlanning(): " . $e->getMessage());
            throw new RuntimeException('Erreur lors de la récupération du planning');
        }
    }

    private function getUsers() {
        try {
            $cursor = $this->db->users->find(
                [],
                ['projection' => ['username' => 1, 'color' => 1]]
            );
            
            $users = [];
            foreach ($cursor as $document) {
                $users[$document['username']] = [
                    'id' => (string)$document['_id'],
                    'color' => $document['color'] ?? '#000000'
                ];
            }
            return $users;
        } catch (MongoDB\Driver\Exception $e) {
            error_log("Erreur MongoDB dans getUsers(): " . $e->getMessage());
            throw new RuntimeException('Erreur lors de la récupération des utilisateurs');
        }
    }

    public function getStats($year) {
        try {
            $pipeline = [
                ['$match' => ['year' => (int)$year]],
                ['$group' => [
                    '_id' => '$username',
                    'count' => ['$sum' => 1]
                ]],
                ['$sort' => ['count' => 1]]
            ];
            
            $stats = [];
            $cursor = $this->db->planning->aggregate($pipeline);
            foreach ($cursor as $document) {
                if ($document->_id !== null) {
                    $stats[] = [
                        'username' => $document->_id,
                        'count' => $document->count
                    ];
                }
            }
            return $stats;
        } catch (MongoDB\Driver\Exception $e) {
            error_log("Erreur MongoDB dans getStats(): " . $e->getMessage());
            throw new RuntimeException('Erreur lors de la récupération des statistiques');
        }
    }

    public function update($postData) {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return ['error' => 'Utilisateur non authentifié'];
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return ['error' => 'Méthode non autorisée'];
        }

        try {
            // Validation des données reçues
            if (!isset($postData['week']) || !isset($postData['year']) || !isset($postData['user_id'])) {
                throw new InvalidArgumentException('Données manquantes');
            }

            // Nettoyage et validation des données
            $week = filter_var($postData['week'], FILTER_VALIDATE_INT);
            $year = filter_var($postData['year'], FILTER_VALIDATE_INT);
            $userId = trim(htmlspecialchars($postData['user_id']));

            if ($week === false || $year === false || empty($userId)) {
                throw new InvalidArgumentException('Données invalides');
            }

            $planningService = new PlanningService($this->database);
            $result = $planningService->updateWeekAssignment($week, $year, $userId);
            
            if (!isset($result['success'])) {
                throw new RuntimeException('Réponse invalide du service');
            }

            return $result;

        } catch (InvalidArgumentException $e) {
            error_log("Erreur de validation: " . $e->getMessage());
            http_response_code(400);
            return ['error' => $e->getMessage()];
        } catch (RuntimeException $e) {
            error_log("Erreur d'exécution: " . $e->getMessage());
            http_response_code(500);
            return ['error' => $e->getMessage()];
        } catch (Exception $e) {
            error_log("Erreur inattendue: " . $e->getMessage());
            http_response_code(500);
            return ['error' => 'Une erreur inattendue est survenue'];
        }
    }
}