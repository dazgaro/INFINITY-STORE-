<?php
// admin_prices.php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'iphone_store');

/*******************************************************************
* FONCTIONS DE BASE DE DONNÉES
*******************************************************************/
function getDbConnection() {
    try {
        $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8';
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        die("Erreur de connexion : ".$e->getMessage());
    }
}


session_start();
// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}

function getPhonePrices() {
    $db = getDbConnection();
    $stmt = $db->query("SELECT * FROM phone_prices ORDER BY type, price DESC");
    return $stmt->fetchAll();
}

function getTradeInValues() {
    $db = getDbConnection();
    $stmt = $db->query("SELECT * FROM trade_in_values ORDER BY model");
    return $stmt->fetchAll();
}

$phonePrices = getPhonePrices();
$tradeInValues = getTradeInValues();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Prix - INFINITY STORE</title>
    <style>
        /* Styles similaires à admin.php */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        .admin-header {
            background: linear-gradient(135deg, #255867 0%, #4a7c87 100%);
            color: white;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo { width: 150px; }
        .admin-nav {
            display: flex;
            gap: 1rem;
        }
        .admin-nav a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background 0.3s;
        }
        .admin-nav a:hover {
            background: rgba(255,255,255,0.2);
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .price-section {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .price-section h2 {
            color: #255867;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }
        th, td {
            padding: 0.8rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #555;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #255867;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #1a4653;
        }
        .btn-edit {
            background: #ffc107;
            color: #212529;
        }
        .btn-edit:hover {
            background: #e0a800;
        }
        .btn-delete {
            background: #dc3545;
        }
        .btn-delete:hover {
            background: #c82333;
        }
        .btn-add {
            margin-bottom: 1rem;
            background: #28a745;
        }
        .btn-add:hover {
            background: #218838;
        }
        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .logout-btn:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <img src="logo.png" alt="INFINITY STORE Logo" class="logo">
        <nav class="admin-nav">
            <a href="admin.php">Tableau de bord</a>
            <a href="admin_prices.php">Gestion des prix</a>
             <a href="../login/logout.php" class="nav-link logout-link">
                <span class="emoji">🚪</span>
                Déconnexion
            </a>
        </nav>
    </header>

    <div class="container">
        <div class="price-section">
            <h2>Prix des téléphones</h2>
            <a href="admin_edit_price.php?type=phone" class="btn btn-add">+ Ajouter un prix</a>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Modèle</th>
                        <th>Stockage</th>
                        <th>Prix ( FCFA)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($phonePrices as $price): ?>
                    <tr>
                        <td><?= $price['id'] ?></td>
                        <td><?= ucfirst($price['type']) ?></td>
                        <td><?= htmlspecialchars($price['model']) ?></td>
                        <td><?= htmlspecialchars($price['storage']) ?></td>
                        <td><?= number_format($price['price']) ?></td>
                        <td>
                            <a href="admin_edit_price.php?id=<?= $price['id'] ?>&type=phone" class="btn btn-edit">Modifier</a>
                            <a href="admin_delete.php?id=<?= $price['id'] ?>&table=phone_prices" class="btn btn-delete" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="price-section">
            <h2>Valeurs de reprise</h2>
            <a href="admin_edit_price.php?type=trade_in" class="btn btn-add">+ Ajouter une valeur</a>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Modèle</th>
                        <th>Valeur base</th>
                        <th>Valeur sup.</th>
                        <th>Déductions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tradeInValues as $value): ?>
                    <tr>
                        <td><?= $value['id'] ?></td>
                        <td><?= htmlspecialchars($value['model']) ?></td>
                        <td><?= number_format($value['base_value']) ?></td>
                        <td><?= number_format($value['superior_value']) ?></td>
                        <td>
                            Sans box: -<?= $value['deduction_no_box'] ?><br>
                            Écran: -<?= $value['deduction_screen_issue'] ?><br>
                            Batterie: -<?= $value['deduction_battery_issue'] ?><br>
                            Sans ID: -<?= $value['deduction_no_id'] ?><br>
                            Coque arrière: -<?= $value['deduction_rear_issue'] ?>
                        </td>
                        <td>
                            <a href="admin_edit_price.php?id=<?= $value['id'] ?>&type=trade_in" class="btn btn-edit">Modifier</a>
                            <a href="admin_delete.php?id=<?= $value['id'] ?>&table=trade_in_values" class="btn btn-delete" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>