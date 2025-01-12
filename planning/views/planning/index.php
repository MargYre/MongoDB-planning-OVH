<!DOCTYPE html>
<html>
<head>
    <title>Planning Corvée Patates</title>
</head>
<body>
    <h1>Planning des corvées d'épluchage</h1>
    <?php
    try {
        // Test de connexion MongoDB
        $collections = $this->db->listCollections();
        echo "<p>Connexion à MongoDB réussie !</p>";
    } catch(Exception $e) {
        echo "<p>Erreur de connexion MongoDB : " . $e->getMessage() . "</p>";
    }
    ?>
</body>
</html>