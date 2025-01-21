<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning des corvées d'épluchage</title>
    <style>
        /* ... Styles existants ... */
        .auth-buttons {
            margin-left: auto;
            padding: 8px 15px;
        }
        .btn-login {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 3px;
        }
        .btn-logout {
            padding: 8px 15px;
            background-color: #ff4444;
            color: white;
            text-decoration: none;
            border-radius: 3px;
        }
        .user-info {
            margin-right: 15px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Planning des corvées d'épluchage</h1>
            <div class="year-selector">
                <span>Année : </span>
                <select onchange="window.location.href='index.php?year=' + this.value">
                    <?php foreach ($years as $year): ?>
                        <option value="<?= $year ?>" <?= $selectedYear == $year ? 'selected' : '' ?>>
                            <?= $year ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="auth-buttons">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="user-info">Connecté en tant que <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="index.php?action=logout" class="btn-logout">Déconnexion</a>
                <?php else: ?>
                    <a href="index.php?action=login" class="btn-login">Se connecter</a>
                <?php endif; ?>
            </div>
        </div>

        <table class="planning-table">
            <thead>
                <tr>
                    <th>Semaine</th>
                    <th>Date</th>
                    <th>Éplucheur</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($planning as $week): ?>
                    <tr>
                        <td><?= $week['week'] ?></td>
                        <td><?= $week['date']->format('d/m/Y') ?></td>
                        <td>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <select class="user-dropdown" 
                                        onchange="updatePlanning(<?= $week['week'] ?>, <?= $selectedYear ?>, this.value)"
                                        style="background-color: <?= $users[$week['username']]['color'] ?? '#ffffff' ?>">
                                    <?php foreach ($users as $username => $user): ?>
                                        <option value="<?= $user['id'] ?>" 
                                                <?= $week['username'] === $username ? 'selected' : '' ?>
                                                style="background-color: <?= $user['color'] ?>">
                                            <?= htmlspecialchars($username) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <span style="color: <?= $users[$week['username']]['color'] ?? 'black' ?>">
                                    <?= htmlspecialchars($week['username']) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="stats">
            <h2>Statistiques par ordre croissant</h2>
            <ol>
                <?php foreach ($stats as $stat): ?>
                    <li>
                        <span style="color: <?= $users[$stat['username']]['color'] ?? 'black' ?>">
                            <?= htmlspecialchars($stat['username']) ?> : <?= $stat['count'] ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>

    <?php if (isset($_SESSION['user_id'])): ?>
    <script>
    function updatePlanning(week, year, userId) {
        fetch('index.php?action=update_planning', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `week=${week}&year=${year}&user_id=${userId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Erreur: ' + data.error);
            } else {
                // Recharger la page pour voir les changements
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue');
        });
    }
    </script>
    <?php endif; ?>
</body>
</html>