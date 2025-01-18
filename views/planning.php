<!DOCTYPE html>
<html>
<head>
    <title>Planning Corvée Patates</title>
</head>
<body>
    <h1>Planning des corvées d'épluchage</h1>
    <?php if (!isset($_SESSION['user'])): ?>
        <a href="index.php?action=login">Se connecter</a>
    <?php else: ?>
        <a href="index.php?action=logout">Se déconnecter</a>
    <?php endif; ?>
    <p>Planning en construction...</p>
</body>
</html>