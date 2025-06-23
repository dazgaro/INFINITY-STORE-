<?php
// admin_delete.php

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

// Vérifie que les paramètres nécessaires sont présents
if (!isset($_GET['id']) || !isset($_GET['table'])) {
    $_SESSION['error_message'] = "Paramètres manquants pour la suppression";
    header("Location: admin_prices.php");
    exit();
}

$id = $_GET['id'];
$table = $_GET['table'];

// Liste des tables autorisées pour la suppression
$allowedTables = ['phone_prices', 'trade_in_values'];

// Vérifie que la table est autorisée
if (!in_array($table, $allowedTables)) {
    $_SESSION['error_message'] = "Table non autorisée";
    header("Location: admin_prices.php");
    exit();
}

try {
    $db = getDbConnection();
    
    // Prépare et exécute la requête de suppression
    $stmt = $db->prepare("DELETE FROM $table WHERE id = ?");
    $stmt->execute([$id]);
    
    // Vérifie si une ligne a été supprimée
    if ($stmt->rowCount() > 0) {
        $_SESSION['success_message'] = "Suppression effectuée avec succès";
    } else {
        $_SESSION['error_message'] = "Aucun enregistrement trouvé avec cet ID";
    }
    
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Erreur lors de la suppression : " . $e->getMessage();
}

// Redirection vers la page des prix
header("Location: admin_prices.php");
exit();
?>