<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning des corvées d'épluchage</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .year-selector {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .year-selector select {
            padding: 5px;
            font-size: 16px;
        }

        .planning-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .planning-table th,
        .planning-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .planning-table th {
            background-color: #f4f4f4;
        }

        .user-cell {
            cursor: pointer;
            padding: 5px;
            border-radius: 3px;
        }

        .stats {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 5px;
        }

        .stats h2 {
            margin-top: 0;
        }

        .logout {
            padding: 8px 15px;
            background-color: #ff4444;
            color: white;
            text-decoration: none;
            border-radius: 3px;
        }

        .user-dropdown {
            padding: 5px;
            margin: 5px;
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
            <a href="index.php?action=logout" class="logout">Déconnexion</a>
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
</body>
</html>