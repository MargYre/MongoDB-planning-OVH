<?php
class PlanningController {
    private $db;

    public function __construct($database) {
        $this->db = $database->getDb();
    }

    public function display() {
        // Récupérer l'année sélectionnée (par défaut année courante)
        $selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
        
        // Récupérer les années disponibles
        $years = $this->getAvailableYears();
        
        // Récupérer le planning de l'année
        $planning = $this->getYearlyPlanning($selectedYear);
        
        // Récupérer les statistiques
        $stats = $this->getStats($selectedYear);
        
        // Récupérer la liste des utilisateurs avec leurs couleurs
        $users = $this->getUsers();
        
        // Charger la vue
        require ROOT_PATH . '/views/planning.php';
    }

    private function getAvailableYears() {
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
    }

    private function getYearlyPlanning($year) {
        $cursor = $this->db->planning->find(
            ['year' => (int)$year],
            [
                'sort' => ['date' => 1],
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
                'username' => $document['username'],
                'user_id' => $document['user_id']
            ];
        }
        return $planning;
    }

    public function getStats($year) {
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
            $stats[] = [
                'username' => $document->_id,
                'count' => $document->count
            ];
        }
        return $stats;
    }

    private function getUsers() {
        $cursor = $this->db->users->find(
            [],
            ['projection' => ['username' => 1, 'color' => 1]]
        );
        
        $users = [];
        foreach ($cursor as $document) {
            $users[$document['username']] = [
                'id' => (string)$document['_id'],
                'color' => $document['color']
            ];
        }
        return $users;
    }

    public function update($postData) {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return ['error' => 'Non autorisé'];
        }

        try {
            $week = (int)$postData['week'];
            $year = (int)$postData['year'];
            $userId = new MongoDB\BSON\ObjectId($postData['user_id']);
            
            // Récupérer les informations de l'utilisateur
            $user = $this->db->users->findOne(['_id' => $userId]);
            if (!$user) {
                throw new Exception('Utilisateur non trouvé');
            }

            // Mettre à jour le planning
            $result = $this->db->planning->updateOne(
                [
                    'year' => $year,
                    'week' => $week
                ],
                [
                    '$set' => [
                        'user_id' => $userId,
                        'username' => $user['username']
                    ]
                ]
            );

            if ($result->getModifiedCount() > 0) {
                return ['success' => true];
            } else {
                throw new Exception('Aucune modification effectuée');
            }

        } catch (Exception $e) {
            error_log("Erreur mise à jour planning : " . $e->getMessage());
            http_response_code(500);
            return ['error' => 'Erreur lors de la mise à jour'];
        }
    }

    public function generateYearlyPlanning($year) {
        try {
            // Récupérer tous les utilisateurs
            $users = iterator_to_array($this->db->users->find());
            if (empty($users)) {
                throw new Exception('Aucun utilisateur trouvé');
            }

            $userCount = count($users);
            $userIndex = 0;
            $planning = [];

            // Générer le planning pour chaque semaine
            $date = new DateTime($year . '-01-01');
            $date->modify('next sunday'); // Commencer au premier dimanche

            for ($week = 1; $week <= 52; $week++) {
                $user = $users[$userIndex];
                $planning[] = [
                    'year' => (int)$year,
                    'week' => $week,
                    'date' => new MongoDB\BSON\UTCDateTime($date->getTimestamp() * 1000),
                    'user_id' => $user['_id'],
                    'username' => $user['username']
                ];

                $date->modify('+1 week');
                $userIndex = ($userIndex + 1) % $userCount;
            }

            // Supprimer l'ancien planning de l'année
            $this->db->planning->deleteMany(['year' => (int)$year]);

            // Insérer le nouveau planning
            $this->db->planning->insertMany($planning);

            return ['success' => true, 'message' => 'Planning généré avec succès'];

        } catch (Exception $e) {
            error_log("Erreur génération planning : " . $e->getMessage());
            return ['error' => 'Erreur lors de la génération du planning'];
        }
    }
}