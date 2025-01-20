<?php
class PlanningController {
    private $db;

    public function __construct($database) {
        $this->db = $database->getDb();
    }

    public function display() {
        // Récupérer les données du planning depuis MongoDB
        $weeksCollection = $this->db->weeks;
        $currentYear = isset($_GET['year']) ? intval($_GET['year']) : 2025;
        
        // Récupérer toutes les semaines pour l'année en cours
        $weeks = $weeksCollection->find(['year' => $currentYear]);
        $weekAssignments = [];
        
        // Organiser les données par numéro de semaine
        foreach ($weeks as $week) {
            $weekAssignments[$week->num_week] = [
                'user_id' => $week->user_id,
                'start_date' => $week->start_date
            ];
        }
        
        // Calculer les statistiques
        $stats = $this->calculateStats($currentYear);
        
        // Passer les données à la vue
        $viewData = [
            'weekAssignments' => $weekAssignments,
            'currentYear' => $currentYear,
            'stats' => $stats
        ];
        
        require 'views/planning.php';
    }

    public function update() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        $weeksCollection = $this->db->weeks;
        $year = intval($_POST['year']);
        $updates = [];

        foreach ($_POST as $key => $value) {
            if (strpos($key, 'week_') === 0) {
                $weekNum = intval(substr($key, 5));
                
                // Mettre à jour ou créer l'assignation
                $weeksCollection->updateOne(
                    [
                        'year' => $year,
                        'num_week' => $weekNum
                    ],
                    [
                        '$set' => [
                            'user_id' => $value,
                            'last_updated' => new MongoDB\BSON\UTCDateTime(),
                            'updated_by' => $_SESSION['user_id']
                        ]
                    ],
                    ['upsert' => true]
                );
            }
        }

        return true;
    }

    private function calculateStats($year) {
        $weeksCollection = $this->db->weeks;
        $pipeline = [
            ['$match' => ['year' => $year]],
            ['$group' => [
                '_id' => '$user_id',
                'count' => ['$sum' => 1]
            ]]
        ];

        $stats = [];
        $results = $weeksCollection->aggregate($pipeline);

        foreach ($results as $result) {
            $stats[$result->_id] = $result->count;
        }

        return $stats;
    }
}