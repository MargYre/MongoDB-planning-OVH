<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Planning Corvées</title>
    <link rel="stylesheet" href="../public/css/styles.css">
</head>
<body>
    <div class="login-container">
        <h1>Connexion au Planning</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="index.php?action=auth/login">
            <div class="form-group">
                <label for="username">Nom d'utilisateur:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-primary">Se connecter</button>
        </form>
    </div>
</body>
</html>