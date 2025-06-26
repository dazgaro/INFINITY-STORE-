<?php
session_start();

/*******************************************************************
* CONFIGURATION DE LA BASE DE DONNÉES
*******************************************************************/
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

function initDatabase() {
    $db = getDbConnection();
    
    try {
        // Table des utilisateurs
        $db->exec("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nom_complet VARCHAR(100) NOT NULL,
    whatsapp VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
        
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
            user_id INT NOT NULL,
            form_type ENUM('achat', 'troc', 'vente') NOT NULL,
            nom VARCHAR(100) NOT NULL,
            whatsapp VARCHAR(20) NOT NULL,
            email VARCHAR(100),
            data JSON NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )");
        
        // Table des frais de livraison
        $db->exec("CREATE TABLE IF NOT EXISTS delivery_fees (
            id INT AUTO_INCREMENT PRIMARY KEY,
            zone VARCHAR(50) NOT NULL,
            fee DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Table de suivi des commandes
        $db->exec("CREATE TABLE IF NOT EXISTS order_tracking (
            id INT AUTO_INCREMENT PRIMARY KEY,
            submission_id INT NOT NULL,
            whatsapp VARCHAR(20) NOT NULL,
            status ENUM('Reçu', 'En traitement', 'Expédié', 'Livré', 'Annulé') DEFAULT 'Reçu',
            status_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            notes TEXT,
            FOREIGN KEY (submission_id) REFERENCES submissions(id),
            INDEX idx_whatsapp (whatsapp)
        )");
        
        // Insertion des données initiales si tables vides
        if ($db->query("SELECT COUNT(*) FROM phone_prices")->fetchColumn() == 0) {
            $phonePrices = [
                ['neuf', 'iPhone 16 Pro Max', '256g', 785000],
                // ... (tous vos autres modèles)
            ];

            $tradeInValues = [
                ['iPhone 7', 20000, 25000, 5000, 5000, 5000, 0, 5000],
                // ... (tous vos autres modèles de reprise)
            ];

            $deliveryFees = [
                ['Abidjan (Plateau)', 5000],
                // ... (toutes vos autres zones)
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
            
            $stmtDelivery = $db->prepare("INSERT INTO delivery_fees (zone, fee) VALUES (?, ?)");
            foreach ($deliveryFees as $data) {
                $stmtDelivery->execute($data);
            }
            
            $db->commit();
        }
    } catch (PDOException $e) {
        die("Erreur d'initialisation : ".$e->getMessage());
    }
}

/*******************************************************************
* FONCTIONS D'AUTHENTIFICATION
*******************************************************************/
function registerUser($username, $password, $nom_complet, $whatsapp, $email) {
    $db = getDbConnection();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        $stmt = $db->prepare("INSERT INTO users (username, password, nom_complet, whatsapp, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $hashedPassword, $nom_complet, $whatsapp, $email]);
        return $db->lastInsertId();
    } catch (PDOException $e) {
        error_log("Erreur d'inscription: ".$e->getMessage());
        return false;
    }
}

function loginUser($username, $password) {
    $db = getDbConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nom_complet'] = $user['nom_complet'];
        return true;
    }
    return false;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getUserInfo($userId) {
    $db = getDbConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch();
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

function getDeliveryFees() {
    $db = getDbConnection();
    $stmt = $db->query("SELECT zone, fee FROM delivery_fees ORDER BY fee");
    return $stmt->fetchAll();
}

function getOrderTracking($userId) {
    $db = getDbConnection();
    
    // Si on reçoit un objet PDO par erreur, on récupère l'ID de session
    if ($userId instanceof PDO) {
        $userId = getCurrentUserId();
    }
    
    // Validation de l'ID utilisateur
    if (!is_numeric($userId)) {
        throw new InvalidArgumentException("L'ID utilisateur doit être numérique. Reçu: " . gettype($userId));
    }

    $stmt = $db->prepare("
        SELECT t.status, t.status_date, t.notes, s.form_type, s.created_at 
        FROM order_tracking t
        JOIN submissions s ON t.submission_id = s.id
        WHERE s.user_id = ?
        ORDER BY t.status_date DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}
// Après la fonction getOrderTracking()
function getOrderTrackingForSubmission($db, $submissionId) {
    if (!is_numeric($submissionId)) {
        throw new InvalidArgumentException("L'ID de soumission doit être numérique");
    }

    $stmt = $db->prepare("
        SELECT status, status_date, notes 
        FROM order_tracking 
        WHERE submission_id = ?
        ORDER BY status_date DESC
    ");
    $stmt->execute([$submissionId]);
    return $stmt->fetchAll();
}

function saveSubmission($formData) {
    if (!isLoggedIn()) return false;
    
    $db = getDbConnection();
    $db->beginTransaction();
    
    try {
        $stmt = $db->prepare("INSERT INTO submissions (user_id, form_type, nom, whatsapp, email, data) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            getCurrentUserId(),
            $formData['form_type'],
            $formData['nom'],
            $formData['whatsapp'],
            $formData['email'] ?? null,
            json_encode($formData['details'], JSON_UNESCAPED_UNICODE)
        ]);
        
        $submissionId = $db->lastInsertId();
        
        $stmtTrack = $db->prepare("INSERT INTO order_tracking (submission_id, whatsapp, status) VALUES (?, ?, 'reçu')");
        $stmtTrack->execute([$submissionId, $formData['whatsapp']]);
        
        $db->commit();
        return true;
    } catch (PDOException $e) {
        $db->rollBack();
        error_log("Erreur de sauvegarde: ".$e->getMessage());
        return false;
    }
}