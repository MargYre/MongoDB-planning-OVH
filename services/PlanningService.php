<?php
class PlanningService {
    private $db;
    
    public function __construct($database) {
        $this->db = $database->getDb();
    }

    public function updateWeekAssignment($week, $year, $userId) {
        error_log("Début updateWeekAssignment - Données reçues: " . json_encode([
            'week' => $week,
            'year' => $year,
            'userId' => $userId
        ]));
        try {
            $this->validateInputs($week, $year, $userId);
            $user = $this->getAndValidateUser($userId);
            $this->validateWeekExists($week, $year);
            return $this->performUpdate($week, $year, $user);
        } catch (Exception $e) {
            error_log("Erreur dans updateWeekAssignment: " . $e->getMessage());
            throw $e;
        }
    }

    private function validateInputs($week, $year, $userId) {
        // Conversion explicite en entiers pour week et year
        $week = (int)$week;
        $year = (int)$year;
    
        if (!is_numeric($week) || !is_numeric($year) || 
            $week < 1 || $week > 53 || 
            $year < 2000 || $year > 2100) {
            throw new InvalidArgumentException(sprintf(
                'Valeurs invalides : semaine=%d, année=%d', 
                $week, 
                $year
            ));
        }
    
        // Validation plus précise de l'ObjectId MongoDB
        if (!preg_match('/^[0-9a-f]{24}$/i', $userId)) {
            throw new InvalidArgumentException(
                'Format d\'ID utilisateur invalide : doit être un ObjectId MongoDB valide'
            );
        }
    }
    private function validateWeekExists($week, $year) {
        $existingWeek = $this->db->planning->findOne([
            'year' => (int)$year,
            'week' => (int)$week
        ]);

        if (!$existingWeek) {
            throw new RuntimeException('Semaine non trouvée dans le planning');
        }
    }

    private function performUpdate($week, $year, $user) {
        try {
            $result = $this->db->planning->updateOne(
                [
                    'year' => (int)$year,
                    'week' => (int)$week
                ],
                [
                    '$set' => [
                        'user_id' => $user['_id'],
                        'username' => $user['username']
                    ]
                ]
            );

            return [
                'success' => true,
                'message' => $result->getModifiedCount() > 0 
                    ? 'Planning mis à jour avec succès'
                    : 'Aucune modification nécessaire'
            ];
        } catch (Exception $e) {
            error_log("Erreur dans performUpdate: " . $e->getMessage());
            throw new RuntimeException('Erreur lors de la mise à jour du planning');
        }
    }
    private function getAndValidateUser($userId) {
        try {
            $objectId = new MongoDB\BSON\ObjectId($userId);
            $user = $this->db->users->findOne(['_id' => $objectId]);
            
            if (!$user) {
                throw new RuntimeException('Utilisateur non trouvé');
            }
            
            return $user;
        } catch (MongoDB\Driver\Exception\InvalidArgumentException $e) {
            error_log("Erreur lors de la validation de l'utilisateur: " . $e->getMessage());
            throw new InvalidArgumentException('ID utilisateur invalide');
        }
    }
}