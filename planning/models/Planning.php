<?php
class Planning {
    private $db;
    private $collection;

    public function __construct($db) {
        $this->db = $db;
        $this->collection = $this->db->plannings;
    }

    public function getWeeklyAssignments() {
        try {
            return $this->collection->find([], ['sort' => ['weekNumber' => 1]]);
        } catch(Exception $e) {
            echo "Erreur : " . $e->getMessage();
            return [];
        }
    }

    public function updateAssignment($weekNumber, $person) {
        try {
            return $this->collection->updateOne(
                ['weekNumber' => $weekNumber],
                ['$set' => ['assignedPerson' => $person]],
                ['upsert' => true]
            );
        } catch(Exception $e) {
            echo "Erreur de mise à jour : " . $e->getMessage();
            return false;
        }
    }

    public function getStats() {
        try {
            return $this->collection->aggregate([
                ['$group' => [
                    '_id' => '$assignedPerson',
                    'count' => ['$sum' => 1]
                ]],
                ['$sort' => ['count' => 1]]
            ])->toArray();
        } catch(Exception $e) {
            echo "Erreur statistiques : " . $e->getMessage();
            return [];
        }
    }
}