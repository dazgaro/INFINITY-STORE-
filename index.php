<?php
/*******************************************************************
* CONFIGURATION DE LA BASE DE DONNÉES
*******************************************************************/
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

function initDatabase() {
    $db = getDbConnection();
    
    try {
        // Table des prix des téléphones
        $db->exec("CREATE TABLE IF NOT EXISTS phone_prices (
            id INT AUTO_INCREMENT PRIMARY KEY,
            type ENUM('neuf', 'quasi') NOT NULL,
            model VARCHAR(50) NOT NULL,
            storage VARCHAR(20) NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            INDEX idx_type (type),
            INDEX idx_model (model)
        )");
        
        // Table des valeurs de reprise
        $db->exec("CREATE TABLE IF NOT EXISTS trade_in_values (
            id INT AUTO_INCREMENT PRIMARY KEY,
            model VARCHAR(50) NOT NULL,
            base_value DECIMAL(10,2) NOT NULL,
            superior_value DECIMAL(10,2) NOT NULL,
            deduction_no_box DECIMAL(10,2) DEFAULT 0,
            deduction_screen_issue DECIMAL(10,2) DEFAULT 0,
            deduction_battery_issue DECIMAL(10,2) DEFAULT 0,
            deduction_no_id DECIMAL(10,2) DEFAULT 0,
            deduction_rear_issue DECIMAL(10,2) DEFAULT 0,
            INDEX idx_model (model)
        )");
        
        // Table des soumissions de formulaire
        $db->exec("CREATE TABLE IF NOT EXISTS submissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            form_type ENUM('achat', 'troc', 'vente') NOT NULL,
            nom VARCHAR(100) NOT NULL,
            whatsapp VARCHAR(20) NOT NULL,
            email VARCHAR(100),
            data JSON NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Insertion des données initiales si tables vides
        if ($db->query("SELECT COUNT(*) FROM phone_prices")->fetchColumn() == 0) {
            $phonePrices = [
                // Modèles scellés (neuf)
                ['neuf', 'iPhone 16 Pro Max', '256g', 785],
                ['neuf', 'iPhone 16 Pro', '256g', 685],
                ['neuf', 'iPhone 16 Pro', '128g', 675],
                ['neuf', 'iPhone 16 Plus', '256g', 665],
                ['neuf', 'iPhone 15 Pro Max', '512g', 635],
                ['neuf', 'iPhone 15 Pro Max', '256g', 615],
                ['neuf', 'iPhone 16', '256g', 575],
                ['neuf', 'iPhone 15 Pro', '256g', 555],
                ['neuf', 'iPhone 16 Plus', '128g', 550],
                ['neuf', 'iPhone 15 Pro', '128g', 515],
                ['neuf', 'iPhone 16', '128g', 495],
                ['neuf', 'iPhone 14 Pro Max', '256g', 485],
                ['neuf', 'iPhone 14 Pro Max', '128g', 460],
                ['neuf', 'iPhone 15', '256g', 455],
                ['neuf', 'iPhone 14 Pro', '256g', 445],
                ['neuf', 'iPhone 15', '128g', 395],
                ['neuf', 'iPhone 14 Pro', '128g', 385],
                ['neuf', 'iPhone 13 Pro Max', '256g', 375],
                ['neuf', 'iPhone 13 Pro Max', '128g', 350],
                ['neuf', 'iPhone 14', '256g', 325],
                ['neuf', 'iPhone 14', '128g', 295],
                ['neuf', 'iPhone 13 Pro', '128g', 290],
                ['neuf', 'iPhone 12 Pro Max', '128g', 285],
                ['neuf', 'iPhone 12 Pro', '128g', 245],
                ['neuf', 'iPhone 13', '128g', 245],
                ['neuf', 'iPhone 12', '64g', 175],
                ['neuf', 'iPhone 11', '128g', 150],
                ['neuf', 'iPhone 11', '64g', 140],
                ['neuf', 'iPhone XR', '128g', 125],
                ['neuf', 'iPhone XR', '64g', 120],
                ['neuf', 'iPhone X', '64g', 100],
                
                // Modèles quasi-neufs (quasi)
                ['quasi', 'iPhone 15 Pro Max', '256g', 565],
                ['quasi', 'iPhone 14 Pro Max', '128g', 415],
                ['quasi', 'iPhone 14 Pro', '128g', 370],
                ['quasi', 'iPhone 13 Pro Max', '256g', 310],
                ['quasi', 'iPhone 13 Pro Max', '128g', 300],
                ['quasi', 'iPhone 13 Pro', '256g', 280],
                ['quasi', 'iPhone 13 Pro', '128g', 260],
                ['quasi', 'iPhone 12 Pro Max', '128g', 260],
                ['quasi', 'iPhone 13', '128g', 195],
                ['quasi', 'iPhone 12 Pro', '128g', 185],
                ['quasi', 'iPhone 13 Mini', '128g', 180],
                ['quasi', 'iPhone 11 Pro', '64g', 150],
                ['quasi', 'iPhone 12', '64g', 150],
                ['quasi', 'iPhone 11', '64g', 125],
                ['quasi', 'iPhone 12 Mini', '64g', 125],
                ['quasi', 'iPhone XR', '64g', 105],
                ['quasi', 'iPhone X', '64g', 90]
            ];
            
             $tradeInValues = [
                // Format: [model, base_value, superior_value, deduction_no_box, deduction_screen_issue, deduction_battery_issue, deduction_no_id, deduction_rear_issue]
                ['iPhone 7', 20, 25, 5, 5, 5, 0, 5],
                ['iPhone 7 Plus', 30, 35, 5, 10, 5, 0, 10],
                ['iPhone 8', 30, 40, 5, 5, 5, 0, 10],
                ['iPhone X', 45, 50, 5, 10, 5, 20, 10],
                ['iPhone XR', 60, 70, 5, 10, 10, 20, 10],
                ['iPhone XS', 50, 60, 5, 10, 5, 20, 10],
                ['iPhone XS Max', 65, 80, 5, 15, 10, 20, 10],
                ['iPhone 11', 85, 90, 5, 10, 10, 20, 10],
                ['iPhone 11 Pro', 95, 100, 5, 15, 10, 25, 10],
                ['iPhone 11 Pro Max', 100, 115, 5, 15, 10, 30, 10],
                ['iPhone 12 mini', 85, 105, 5, 15, 10, 30, 10],
                ['iPhone 12', 110, 115, 5, 15, 10, 30, 15],
                ['iPhone 12 Pro', 120, 150, 10, 20, 15, 40, 20],
                ['iPhone 12 Pro Max', 140, 185, 10, 25, 20, 50, 20],
                ['iPhone 13 Mini', 140, 145, 10, 25, 20, 40, 15],
                ['iPhone 13', 150, 175, 10, 30, 20, 50, 20],
                ['iPhone 13 Pro', 180, 220, 10, 55, 30, 70, 20],
                ['iPhone 13 Pro Max', 210, 240, 10, 70, 35, 80, 20],
                ['iPhone 14', 200, 230, 10, 30, 30, 60, 20],
                ['iPhone 14 Plus', 220, 340, 10, 0, 0, 0, 20], // XXX remplacé par 0
                ['iPhone 14 Pro', 280, 300, 10, 90, 40, 120, 20],
                ['iPhone 14 Pro Max', 300, 320, 10, 100, 50, 120, 20],
                ['iPhone 15', 280, 310, 0, 0, 0, 0, 0], // XXX remplacé par 0
                ['iPhone 15 Pro', 390, 400, 0, 0, 0, 0, 0], // XXX remplacé par 0
                ['iPhone 15 Pro Max', 430, 460, 0, 0, 0, 0, 0], // XXX remplacé par 0
                ['iPhone 16', 380, 470, 0, 0, 0, 0, 0], // Pas de déductions
                ['iPhone 16 Pro', 530, 570, 0, 0, 0, 0, 0], // Pas de déductions
                ['iPhone 16 Pro Max', 580, 650, 0, 0, 0, 0, 0] // Pas de déductions
            ];
            $db->beginTransaction();
            
            $stmtPhone = $db->prepare("INSERT INTO phone_prices (type, model, storage, price) VALUES (?, ?, ?, ?)");
            foreach ($phonePrices as $data) {
                $stmtPhone->execute($data);
            }
            
            $stmtTrade = $db->prepare("INSERT INTO trade_in_values 
                (model, base_value, superior_value, deduction_no_box, deduction_screen_issue, 
                 deduction_battery_issue, deduction_no_id, deduction_rear_issue) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($tradeInValues as $data) {
                $stmtTrade->execute($data);
            }
            
            $db->commit();
        }
    } catch (PDOException $e) {
        die("Erreur d'initialisation : ".$e->getMessage());
    }
}

/*******************************************************************
* FONCTIONS DE L'APPLICATION
*******************************************************************/
function getPhonePrices($type) {
    $db = getDbConnection();
    $stmt = $db->prepare("SELECT model, storage, price FROM phone_prices WHERE type = ? ORDER BY price DESC");
    $stmt->execute([$type]);
    return $stmt->fetchAll();
}

function getTradeInModels() {
    $db = getDbConnection();
    $stmt = $db->query("SELECT model FROM trade_in_values ORDER BY model");
    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

function getTradeInValue($model) {
    $db = getDbConnection();
    $stmt = $db->prepare("SELECT * FROM trade_in_values WHERE model = ?");
    $stmt->execute([$model]);
    return $stmt->fetch();
}

function saveSubmission($formData) {
    $db = getDbConnection();
    $stmt = $db->prepare("INSERT INTO submissions (form_type, nom, whatsapp, email, data) VALUES (?, ?, ?, ?, ?)");
    
    // Préparer les données à sauvegarder
    $data = [
        'details' => $formData['details'] ?? [],
        'metadata' => [
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT']
        ]
    ];
    
    return $stmt->execute([
        $formData['form_type'],
        $formData['nom'],
        $formData['whatsapp'],
        $formData['email'] ?? null,
        json_encode($data)
    ]);
}

/*******************************************************************
* TRAITEMENT DES REQUÊTES
*******************************************************************/
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialisation de la base de données
initDatabase();

// API pour les valeurs de reprise
if (isset($_GET['action']) && $_GET['action'] == 'get_trade_in' && isset($_GET['model'])) {
    header('Content-Type: application/json');
    $data = getTradeInValue($_GET['model']);
    echo $data ? json_encode($data) : json_encode(['error' => 'Modèle non trouvé']);
    exit;
}

// Traitement des formulaires
$success_message = $error_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $form_type = $_POST['form_type'] ?? '';
    $nom = htmlspecialchars($_POST['nom'] ?? '');
    $whatsapp = htmlspecialchars($_POST['whatsapp'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    
    // Préparer les données spécifiques au formulaire
    $details = [];
    switch ($form_type) {
        case 'achat':
            $details = [
                'type' => $_POST['achatType'] ?? '',
                'modele' => $_POST['modele'] ?? '',
                'commentaire' => $_POST['commentaire'] ?? ''
            ];
            break;
            
        case 'troc':
            $details = [
                'new_type' => $_POST['newAchatTypeTroc'] ?? '',
                'new_modele' => $_POST['new_modele'] ?? '',
                'old_model' => $_POST['old_model'] ?? '',
                'old_storage' => $_POST['old_storage'] ?? '',
                'conditions' => $_POST['oldPhoneCondition'] ?? []
            ];
            break;
            
        case 'vente':
            $details = [
                'model' => $_POST['model'] ?? '',
                'storage' => $_POST['storage'] ?? '',
                'screen_condition' => $_POST['screen_condition'] ?? '',
                'battery_condition' => $_POST['battery_condition'] ?? '',
                'functionality' => $_POST['functionality'] ?? '',
                'body_condition' => $_POST['body_condition'] ?? '',
                'has_box' => $_POST['hasBox'] ?? '',
                'has_accessories' => $_POST['hasAccessories'] ?? '',
                'accessories_details' => $_POST['accessories_details'] ?? '',
                'other_info' => $_POST['other_info'] ?? ''
            ];
            break;
    }
    
    // Sauvegarder en base de données
    if (saveSubmission([
        'form_type' => $form_type,
        'nom' => $nom,
        'whatsapp' => $whatsapp,
        'email' => $email,
        'details' => $details
    ])) {
        $success_message = "Votre demande a été enregistrée avec succès. Nous vous contacterons rapidement.";
    } else {
        $error_message = "Une erreur est survenue lors de l'enregistrement. Veuillez réessayer.";
    }
}

// Préparation des données pour le frontend
$phonePricesNeuf = json_encode(getPhonePrices('neuf'));
$phonePricesQuasi = json_encode(getPhonePrices('quasi'));
$tradeInModels = json_encode(getTradeInModels());
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service iPhone Pro - INFINITY STORE</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #255867 0%, #4a7c87 50%, #7ba8b8 100%);
            min-height: 100vh;
            padding: 2rem 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .logo { width: 60px; margin: 0 auto 1.5rem;position:center }
        .main-container {
            width: 95%; max-width: 1200px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            padding: 2rem;
            box-shadow: 0 25px 50px rgba(37, 88, 103, 0.3);
        }
        h1 { 
            font-size: 2.2rem; 
            color: #255867; 
            text-align: center; 
            margin-bottom: 1.5rem;
            position: relative;
        }
        h1::after {
            content: '';
            position: absolute;
            bottom: 0; left: 50%;
            transform: translateX(-50%);
            width: 100px; height: 3px;
            background: linear-gradient(90deg, #C79626, #957F67);
        }
        .tab-nav {
            display: flex;
            margin-bottom: 2rem;
            border-bottom: 2px solid #e0e0e0;
        }
        .btn-tab {
            flex: 1; padding: 1rem;
            border: none; background: none;
            cursor: pointer; font-weight: 600;
            color: #666; text-align: center;
        }
        .btn-tab.active {
            color: #255867;
            position: relative;
        }
        .btn-tab.active::after {
            content: '';
            position: absolute;
            bottom: -2px; left: 0;
            width: 100%; height: 3px;
            background: linear-gradient(90deg, #C79626, #957F67);
        }
        .form-section {
            display: none;
            padding: 1.5rem;
            background: white;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        .form-section.active { display: block; }
        .form-group { margin-bottom: 1.5rem; }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .form-control, .form-select {
            width: 100%; padding: 0.8rem 1rem;
            border: 1px solid #ddd; border-radius: 8px;
            font-size: 1rem; background-color: #f9f9f9;
        }
        .form-check {
            display: inline-flex;
            align-items: center;
            margin-right: 1.5rem;
            cursor: pointer;
        }
        .total-price {
            font-weight: 700; font-size: 1.2rem;
            color: #255867; padding: 1rem;
            background: #f0f7f9; border-radius: 8px;
            margin: 1.5rem 0; text-align: center;
            border-left: 4px solid #C79626;
        }
        .btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: linear-gradient(135deg, #C79626, #957F67);
            color: white; border: none; border-radius: 50px;
            font-weight: 600; cursor: pointer;
            text-align: center; width: 100%;
        }
        .alert {
            padding: 1rem; margin-bottom: 1.5rem;
            border-radius: 8px; font-weight: 500;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        @media (max-width: 768px) {
            .tab-nav { flex-direction: column; }
            h1 { font-size: 1.8rem; }
        }
         .navigation {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin: 2rem 0;
        }

        .nav-link {
            display: inline-block;
            padding: 1rem 2rem;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            min-width: 180px;
            border: 2px solid transparent;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .nav-link:hover::before {
            left: 100%;
        }

        .home-link {
            background: linear-gradient(135deg,  #C79626, #4a7c87);
            color: white;
            box-shadow: 0 8px 25px rgba(37, 88, 103, 0.4);
        }

        .logout-link {
            background: transparent;
            color: #255867;
            border: 2px solid #255867;
            box-shadow: 0 8px 25px rgba(37, 88, 103, 0.2);
        }

        .logout-link:hover {
            background: #255867;
            color: white;
        }
    </style>
</head>
<body>
   

    <div class="main-container">
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php elseif (!empty($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>

        <h1>Service iPhone Pro</h1>
        
        <div class="tab-nav">
            <button class="btn-tab active" data-tab="achat">📱 Achat Direct</button>
            <button class="btn-tab" data-tab="troc">🔁 Troc d'iPhone</button>
            <button class="btn-tab" data-tab="vente">💰 Vente d'iPhone</button>
        </div>

        <!-- Section Achat -->
        <div class="form-section active" id="achat-section">
            <form method="post">
                <input type="hidden" name="form_type" value="achat">
                <div class="form-group">
                    <label class="form-label">Type d'achat</label><br>
                    <div class="form-check">
                        <input type="radio" name="achatType" id="neuf" value="neuf" checked>
                        <label for="neuf">Neuf Scellé</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="achatType" id="quasi" value="quasi">
                        <label for="quasi">Quasi-Neuf</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Modèle souhaité</label>
                    <select class="form-select" id="modeleSelect" name="modele" required>
                        <option value="" disabled selected>Sélectionnez un modèle</option>
                    </select>
                </div>
                <div class="total-price" id="achatPrix">Sélectionnez un modèle pour voir le prix</div>
                
                <div class="form-group">
                    <label class="form-label">Nom complet</label>
                    <input type="text" class="form-control" name="nom" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Numéro WhatsApp</label>
                    <input type="tel" class="form-control" name="whatsapp" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email">
                </div>
                <div class="form-group">
                    <label class="form-label">Commentaire</label>
                    <textarea class="form-control" name="commentaire" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn">Soumettre l'achat</button>
            </form>
        </div>

        <!-- Section Troc -->
        <div class="form-section" id="troc-section">
            <form method="post">
                <input type="hidden" name="form_type" value="troc">
                <h3>Nouvel appareil souhaité</h3>
                <div class="form-group">
                    <label class="form-label">Type</label><br>
                    <div class="form-check">
                        <input type="radio" name="newAchatTypeTroc" id="neufTroc" value="neuf" checked>
                        <label for="neufTroc">Neuf Scellé</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="newAchatTypeTroc" id="quasiTroc" value="quasi">
                        <label for="quasiTroc">Quasi-Neuf</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Modèle</label>
                    <select class="form-select" id="trocModeleSelect" name="new_modele" required>
                        <option value="" disabled selected>Sélectionnez un modèle</option>
                    </select>
                </div>
                <div class="total-price" id="trocNewPhonePrice">Sélectionnez un modèle pour voir le prix</div>

                <h3>Votre appareil actuel</h3>
                <div class="form-group">
                    <label class="form-label">Modèle</label>
                    <select class="form-select" id="trocOldModel" name="old_model" required>
                        <option value="" disabled selected>Choisir votre modèle</option>
                        <?php foreach (getTradeInModels() as $model): ?>
                            <option value="<?= htmlspecialchars($model) ?>"><?= htmlspecialchars($model) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Capacité</label>
                    <select class="form-select" id="trocOldStorage" name="old_storage" required>
                        <option value="base">Stockage de base</option>
                        <option value="superieur">Stockage supérieur</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">État de l'appareil</label>
                    <div class="form-check">
                        <input type="checkbox" id="noBox" name="oldPhoneCondition[]" value="no_box">
                        <label for="noBox">Sans carton</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="screenIssue" name="oldPhoneCondition[]" value="screen_issue">
                        <label for="screenIssue">Panne d'écran</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="batteryIssue" name="oldPhoneCondition[]" value="battery_issue">
                        <label for="batteryIssue">Panne de batterie</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="noID" name="oldPhoneCondition[]" value="no_id">
                        <label for="noID">Sans ID</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="rearIssue" name="oldPhoneCondition[]" value="rear_issue">
                        <label for="rearIssue">Souci coque arrière</label>
                    </div>
                </div>
                
                <div class="total-price">Valeur estimée: <span id="estimatedTradeInValue">0</span>k FCFA</div>

                <div class="form-group">
                    <label class="form-label">Nom complet</label>
                    <input type="text" class="form-control" name="nom" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Numéro WhatsApp</label>
                    <input type="tel" class="form-control" name="whatsapp" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email">
                </div>

                <button type="submit" class="btn">Soumettre la Demande</button>
            </form>
        </div>

        <!-- Section Vente -->
        <div class="form-section" id="vente-section">
            <form method="post">
                <input type="hidden" name="form_type" value="vente">
                <div class="form-group">
                    <label class="form-label">Modèle exact</label>
                    <input type="text" class="form-control" name="model" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Capacité (Go)</label>
                    <input type="number" class="form-control" name="storage" required>
                </div>
                <div class="form-group">
                    <label class="form-label">État de l'écran</label>
                    <textarea class="form-control" name="screen_condition" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">État de la batterie</label>
                    <input type="text" class="form-control" name="battery_condition">
                </div>
                <div class="form-group">
                    <label class="form-label">Fonctionnalités</label>
                    <textarea class="form-control" name="functionality" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">État général</label>
                    <textarea class="form-control" name="body_condition" required></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Carton d'origine</label><br>
                    <div class="form-check">
                        <input type="radio" name="hasBox" id="sellHasBoxYes" value="oui" required>
                        <label for="sellHasBoxYes">Oui</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="hasBox" id="sellHasBoxNo" value="non">
                        <label for="sellHasBoxNo">Non</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Accessoires</label><br>
                    <div class="form-check">
                        <input type="radio" name="hasAccessories" id="sellHasAccessoriesYes" value="oui" required>
                        <label for="sellHasAccessoriesYes">Oui</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="hasAccessories" id="sellHasAccessoriesNo" value="non">
                        <label for="sellHasAccessoriesNo">Non</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Détails accessoires</label>
                    <textarea class="form-control" name="accessories_details"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Nom complet</label>
                    <input type="text" class="form-control" name="nom" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Numéro WhatsApp</label>
                    <input type="tel" class="form-control" name="whatsapp" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email">
                </div>

                <button type="submit" class="btn">Soumettre la demande</button>
            </form>
        </div>
    </div>
     <div class="navigation">
            <a href="../acceuil/acceuil.html" class="nav-link home-link">
                <span class="emoji">🏠</span>
                Accueil
            </a>
            
            <a href="../login/logout.php" class="nav-link logout-link">
                <span class="emoji">🚪</span>
                Déconnexion
            </a>
        </div>

    <script>
        // Données initiales
        const phonePrices = {
            neuf: <?= $phonePricesNeuf ?>,
            quasi: <?= $phonePricesQuasi ?>
        };

        // Mise à jour des sélecteurs de modèle
        function updateModelSelect(type, selectElement) {
            selectElement.innerHTML = '<option value="" disabled selected>Sélectionnez un modèle</option>';
            phonePrices[type].forEach(item => {
                const option = document.createElement('option');
                option.value = `${item.model}|${item.storage}`;
                option.textContent = `${item.model} ${item.storage}`;
                selectElement.appendChild(option);
            });
        }

        // Affichage du prix
        function updatePriceDisplay(selectElement, priceDisplayElement) {
            if (selectElement.value) {
                const [model, storage] = selectElement.value.split('|');
                const type = document.querySelector('input[name="achatType"]:checked')?.value || 
                            document.querySelector('input[name="newAchatTypeTroc"]:checked')?.value;
                
                const selectedPhone = phonePrices[type].find(item => 
                    item.model === model && item.storage === storage
                );
                
                if (selectedPhone) {
                    priceDisplayElement.textContent = `${selectedPhone.model} ${selectedPhone.storage} - ${selectedPhone.price}k FCFA`;
                }
            } else {
                priceDisplayElement.textContent = "Sélectionnez un modèle pour voir le prix";
            }
        }

        // Calcul des déductions pour le troc
        async function calculateTradeInValue() {
            const model = document.getElementById('trocOldModel').value;
            const storage = document.getElementById('trocOldStorage').value;
            const conditions = Array.from(document.querySelectorAll('input[name="oldPhoneCondition[]"]:checked')).map(cb => cb.value);
            
            if (!model) return;

            try {
                const response = await fetch(`?action=get_trade_in&model=${encodeURIComponent(model)}`);
                const data = await response.json();
                
                if (data.error) throw new Error(data.error);
                
                // Valeur de base
                const baseValue = storage === 'superieur' ? data.superior_value : data.base_value;
                
                // Calcul des déductions
                let totalDeduction = 0;
                conditions.forEach(condition => {
                    const deductionField = 'deduction_' + condition;
                    if (data[deductionField] !== undefined) {
                        totalDeduction += parseFloat(data[deductionField]);
                    }
                });
                
                // Valeur finale
                const finalValue = Math.max(0, baseValue - totalDeduction);
                document.getElementById('estimatedTradeInValue').textContent = finalValue;
            } catch (error) {
                console.error('Erreur:', error);
                alert("Erreur de calcul. Veuillez réessayer.");
            }
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion des onglets
            document.querySelectorAll('.btn-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    document.querySelectorAll('.btn-tab').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.form-section').forEach(s => s.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById(`${this.dataset.tab}-section`).classList.add('active');
                });
            });

            // Initialisation des sélecteurs
            updateModelSelect('neuf', document.getElementById('modeleSelect'));
            updateModelSelect('neuf', document.getElementById('trocModeleSelect'));

            // Écouteurs d'événements
            document.querySelectorAll('input[name="achatType"]').forEach(input => {
                input.addEventListener('change', function() {
                    updateModelSelect(this.value, document.getElementById('modeleSelect'));
                });
            });

            document.querySelectorAll('input[name="newAchatTypeTroc"]').forEach(input => {
                input.addEventListener('change', function() {
                    updateModelSelect(this.value, document.getElementById('trocModeleSelect'));
                });
            });

            document.getElementById('modeleSelect').addEventListener('change', function() {
                updatePriceDisplay(this, document.getElementById('achatPrix'));
            });

            document.getElementById('trocModeleSelect').addEventListener('change', function() {
                updatePriceDisplay(this, document.getElementById('trocNewPhonePrice'));
            });

            // Calculateur de troc
            document.getElementById('trocOldModel').addEventListener('change', calculateTradeInValue);
            document.getElementById('trocOldStorage').addEventListener('change', calculateTradeInValue);
            document.querySelectorAll('input[name="oldPhoneCondition[]"]').forEach(checkbox => {
                checkbox.addEventListener('change', calculateTradeInValue);
            });
        });
    </script>
</body>
</html>