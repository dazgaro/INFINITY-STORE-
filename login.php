<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// === Paramètres de connexion à la base de données ===
$host = "localhost";
$dbname = "site_inscription";
$username_db = "root";
$password_db = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// === Traitement du formulaire ===
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupère les données du formulaire
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Recherche l'utilisateur dans la base
    $sql = "SELECT * FROM utilisateurs WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        // Mot de passe correct → on démarre la session
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];

        // Redirection vers le tableau de bord
        if($user["role"] === "admin"){
         header("Location: admin.php");   
        }else{
            header("location: index.php");
        }
        
        exit();
    } else {
        // Identifiants incorrects
        echo "<p style='color:red;'>❌ Email ou mot de passe incorrect.</p>";
        echo "<p><a href='../login/login.html'>← Revenir à la page de connexion</a></p>";
    }
} else {
    echo "Méthode non autorisée.";
}
?>