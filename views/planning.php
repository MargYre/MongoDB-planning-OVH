<!DOCTYPE html>
<html>
<head>
    <title>Planning Corvée Patates</title>
    <style>
        .vincent { background-color: #FF9900; }
        .david { background-color: #00CCFF; }
        .thomas { background-color: #00FF33; }
        .christophe { background-color: #FFFF00; }
        .personne { background-color: #FF0000; }
        
        .container {
            width: 950px;
            margin: 0 auto;
        }
        .annee {
            text-align: center;
        }
        table {
            border-collapse: collapse;
            margin: 20px 0;
        }
        td {
            padding: 10px;
            border: 1px solid black;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Planning des corvées d'épluchage</h1>

        <?php if (!isset($_SESSION['user'])): ?>
            <a href="index.php?action=login">Se connecter</a>
        <?php else: ?>
            <a href="index.php?action=logout">Se déconnecter</a>
        <?php endif; ?>

        <p class="annee">
            <label>Année : 
                <select name="annee">
                    <option value="2024">2024</option>
                    <option value="2025" selected>2025</option>
                    <option value="2026">2026</option>
                </select>
            </label>
        </p>

        <form method="post">
            <table align="center">
                <?php
                // Date de début de l'année
                $date = new DateTime('2025-01-01');
                
                // Pour chaque semaine de l'année
                for ($week = 1; $week <= 52; $week += 4) {
                    echo "<tr>";
                    
                    // Afficher 4 semaines par ligne
                    for ($i = 0; $i < 4 && ($week + $i) <= 52; $i++) {
                        $currentWeek = $week + $i;
                        echo "<td>";
                        echo $date->format('d/m/Y');
                        
                        // Si connecté, afficher select modifiable, sinon juste le texte
                        if (isset($_SESSION['user'])) {
                            echo "<select name='week_$currentWeek' class='personne'>";
                            echo "<option value='personne'>personne</option>";
                            echo "<option value='vincent'>vincent</option>";
                            echo "<option value='david'>david</option>";
                            echo "<option value='thomas'>thomas</option>";
                            echo "<option value='christophe'>christophe</option>";
                            echo "</select>";
                        } else {
                            echo "<br>Non assigné";
                        }
                        
                        echo "</td>";
                        
                        // Avancer d'une semaine
                        $date->modify('+1 week');
                    }
                    
                    echo "</tr>";
                }
                ?>
            </table>

            <?php if (isset($_SESSION['user'])): ?>
                <div align="center">
                    <input type="submit" value="Valider le planning">
                </div>
            <?php endif; ?>
        </form>

        <h2>Statistiques</h2>
        <ol>
            <li>Vincent : 0 semaines</li>
            <li>David : 0 semaines</li>
            <li>Thomas : 0 semaines</li>
            <li>Christophe : 0 semaines</li>
        </ol>
    </div>
</body>
</html>