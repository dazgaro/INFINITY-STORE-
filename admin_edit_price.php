<?php
// admin_edit_price.php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'base');

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

$db = getDbConnection();
$message = '';
$price = ['id' => '', 'type' => '', 'model' => '', 'storage' => '', 'price' => ''];
$tradeIn = [
    'id' => '', 
    'model' => '', 
    'base_value' => '', 
    'superior_value' => '',
    'deduction_no_box' => '',
    'deduction_screen_issue' => '',
    'deduction_battery_issue' => ''
];

// Détermine si on édite un prix existant ou on en crée un nouveau
$editMode = false;
$priceType = isset($_GET['type']) ? $_GET['type'] : '';

// Si un ID est fourni, on charge les données existantes
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $editMode = true;
    $id = $_GET['id'];
    
    if ($priceType === 'phone') {
        $stmt = $db->prepare("SELECT * FROM phone_prices WHERE id = ?");
        $stmt->execute([$id]);
        $price = $stmt->fetch();
    } elseif ($priceType === 'trade_in') {
        $stmt = $db->prepare("SELECT * FROM trade_in_values WHERE id = ?");
        $stmt->execute([$id]);
        $tradeIn = $stmt->fetch();
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($priceType === 'phone') {
        $type = $_POST['type'];
        $model = $_POST['model'];
        $storage = $_POST['storage'];
        $priceValue = $_POST['price'];
        
        if ($editMode) {
            $stmt = $db->prepare("UPDATE phone_prices SET type = ?, model = ?, storage = ?, price = ? WHERE id = ?");
            $stmt->execute([$type, $model, $storage, $priceValue, $_POST['id']]);
            $message = "Prix mis à jour avec succès!";
        } else {
            $stmt = $db->prepare("INSERT INTO phone_prices (type, model, storage, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$type, $model, $storage, $priceValue]);
            $message = "Nouveau prix ajouté avec succès!";
        }
    } elseif ($priceType === 'trade_in') {
        $model = $_POST['model'];
        $baseValue = $_POST['base_value'];
        $superiorValue = $_POST['superior_value'];
        $deductionNoBox = $_POST['deduction_no_box'];
        $deductionScreen = $_POST['deduction_screen_issue'];
        $deductionBattery = $_POST['deduction_battery_issue'];
        
        if ($editMode) {
            $stmt = $db->prepare("UPDATE trade_in_values SET model = ?, base_value = ?, superior_value = ?, deduction_no_box = ?, deduction_screen_issue = ?, deduction_battery_issue = ? WHERE id = ?");
            $stmt->execute([$model, $baseValue, $superiorValue, $deductionNoBox, $deductionScreen, $deductionBattery, $_POST['id']]);
            $message = "Valeur de reprise mise à jour avec succès!";
        } else {
            $stmt = $db->prepare("INSERT INTO trade_in_values (model, base_value, superior_value, deduction_no_box, deduction_screen_issue, deduction_battery_issue) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$model, $baseValue, $superiorValue, $deductionNoBox, $deductionScreen, $deductionBattery]);
            $message = "Nouvelle valeur de reprise ajoutée avec succès!";
        }
    }
    
    // Redirection après traitement
    header("Location: admin_prices.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editMode ? 'Modifier' : 'Ajouter' ?> un prix - INFINITY STORE</title>
    <style>
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
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .form-section {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .form-section h2 {
            color: #255867;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #555;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: #255867;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #1a4653;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            background: #d4edda;
            color: #155724;
        }
        .deductions-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 1rem;
        }
        .deduction-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <img src="logo.png" alt="INFINITY STORE Logo" class="logo">
        <nav class="admin-nav">
            <a href="admin.php">Tableau de bord</a>
            <a href="admin_prices.php">Gestion des prix</a>
            <form action="admin_logout.php" method="post" style="display: inline;">
                <button type="submit" class="btn btn-secondary">Déconnexion</button>
            </form>
        </nav>
    </header>

    <div class="container">
        <div class="form-section">
            <h2><?= $editMode ? 'Modifier' : 'Ajouter' ?> <?= $priceType === 'phone' ? 'un prix téléphone' : 'une valeur de reprise' ?></h2>
            
            <?php if (!empty($message)): ?>
                <div class="message"><?= $message ?></div>
            <?php endif; ?>
            
            <?php if ($priceType === 'phone'): ?>
            <form method="POST">
                <input type="hidden" name="id" value="<?= $price['id'] ?>">
                
                <div class="form-group">
                    <label for="type">Type</label>
                    <select id="type" name="type" required>
                        <option value="neuf" <?= $price['type'] === 'neuf' ? 'selected' : '' ?>>Neuf</option>
                        <option value="reconditionne" <?= $price['type'] === 'reconditionne' ? 'selected' : '' ?>>Reconditionné</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="model">Modèle</label>
                    <input type="text" id="model" name="model" value="<?= htmlspecialchars($price['model']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="storage">Stockage (Go)</label>
                    <input type="text" id="storage" name="storage" value="<?= htmlspecialchars($price['storage']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="price">Prix (k FCFA)</label>
                    <input type="number" step="0.01" id="price" name="price" value="<?= $price['price'] ?>" required>
                </div>
                
                <button type="submit" class="btn"><?= $editMode ? 'Mettre à jour' : 'Ajouter' ?></button>
                <a href="admin_prices.php" class="btn btn-secondary">Annuler</a>
            </form>
            
            <?php elseif ($priceType === 'trade_in'): ?>
            <form method="POST">
                <input type="hidden" name="id" value="<?= $tradeIn['id'] ?>">
                
                <div class="form-group">
                    <label for="model">Modèle</label>
                    <input type="text" id="model" name="model" value="<?= htmlspecialchars($tradeIn['model']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="base_value">Valeur de base (k FCFA)</label>
                    <input type="number" step="0.01" id="base_value" name="base_value" value="<?= $tradeIn['base_value'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="superior_value">Valeur supérieure (k FCFA)</label>
                    <input type="number" step="0.01" id="superior_value" name="superior_value" value="<?= $tradeIn['superior_value'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Déductions (k FCFA)</label>
                    <div class="deductions-container">
                        <div class="deduction-item">
                            <label for="deduction_no_box">Sans boîte</label>
                            <input type="number" step="0.01" id="deduction_no_box" name="deduction_no_box" value="<?= $tradeIn['deduction_no_box'] ?>" required>
                        </div>
                        <div class="deduction-item">
                            <label for="deduction_screen_issue">Problème écran</label>
                            <input type="number" step="0.01" id="deduction_screen_issue" name="deduction_screen_issue" value="<?= $tradeIn['deduction_screen_issue'] ?>" required>
                        </div>
                        <div class="deduction-item">
                            <label for="deduction_battery_issue">Problème batterie</label>
                            <input type="number" step="0.01" id="deduction_battery_issue" name="deduction_battery_issue" value="<?= $tradeIn['deduction_battery_issue'] ?>" required>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn"><?= $editMode ? 'Mettre à jour' : 'Ajouter' ?></button>
                <a href="admin_prices.php" class="btn btn-secondary">Annuler</a>
            </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>