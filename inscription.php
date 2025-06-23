<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - INFINITY STORE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #255867 0%, #4a7c87 50%, #7ba8b8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            padding: 2rem 0;
        }

        /* Animations de fond */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .result-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 3rem 2.5rem;
            text-align: center;
            max-width: 500px;
            width: 90%;
            box-shadow: 
                0 25px 50px rgba(37, 88, 103, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.1);
            position: relative;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success {
            color: #27AE60;
        }

        .error {
            color: #E74C3C;
        }

        .icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            display: block;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-15px); }
            60% { transform: translateY(-8px); }
        }

        h2 {
            color: #255867;
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            letter-spacing: -0.5px;
            position: relative;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #C79626, #957F67);
            border-radius: 2px;
        }

        .message {
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .success-message {
            color: #27AE60;
            background: rgba(39, 174, 96, 0.1);
            padding: 1.5rem;
            border-radius: 15px;
            border: 2px solid rgba(39, 174, 96, 0.2);
            margin-bottom: 2rem;
        }

        .error-message {
            color: #E74C3C;
            background: rgba(231, 76, 60, 0.1);
            padding: 1.5rem;
            border-radius: 15px;
            border: 2px solid rgba(231, 76, 60, 0.2);
            margin-bottom: 2rem;
        }

        .button {
            display: inline-block;
            padding: 1.2rem 2.5rem;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            min-width: 160px;
            border: none;
            cursor: pointer;
            margin: 0.5rem;
        }

        .button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .button:hover::before {
            left: 100%;
        }

        .primary-button {
            background: linear-gradient(135deg, #C79626, #957F67);
            color: white;
            box-shadow: 0 8px 25px rgba(199, 150, 38, 0.4);
        }

        .primary-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(199, 150, 38, 0.6);
            background: linear-gradient(135deg, #d4a332, #a08973);
        }

        .secondary-button {
            background: transparent;
            color: #255867;
            border: 2px solid #255867;
            box-shadow: 0 8px 25px rgba(37, 88, 103, 0.2);
        }

        .secondary-button:hover {
            background: #255867;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(37, 88, 103, 0.4);
        }

        .buttons-container {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }

        /* Éléments décoratifs flottants */
        .floating-element {
            position: absolute;
            opacity: 0.08;
            pointer-events: none;
            animation: floatRandom 15s ease-in-out infinite;
            font-size: 2rem;
        }

        .floating-element:nth-child(1) {
            top: 15%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-element:nth-child(2) {
            top: 25%;
            right: 15%;
            animation-delay: 3s;
        }

        .floating-element:nth-child(3) {
            bottom: 20%;
            left: 15%;
            animation-delay: 6s;
        }

        @keyframes floatRandom {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(20px, -20px) rotate(90deg); }
            50% { transform: translate(-15px, -30px) rotate(180deg); }
            75% { transform: translate(-25px, 10px) rotate(270deg); }
        }

        /* Loading animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #C79626;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .result-container {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }

            h2 {
                font-size: 1.8rem;
            }

            .buttons-container {
                flex-direction: column;
                align-items: center;
            }

            .button {
                width: 100%;
                max-width: 280px;
            }
        }

        @media (max-width: 480px) {
            .result-container {
                padding: 1.5rem 1rem;
            }

            h2 {
                font-size: 1.6rem;
            }

            .icon {
                font-size: 3rem;
            }
        }
    </style>
</head>
<body>
    <!-- Éléments décoratifs -->
    <div class="floating-element">📱</div>
    <div class="floating-element">✨</div>
    <div class="floating-element">🔄</div>

    <div class="result-container">
        <?php
        // Affiche les erreurs PHP pour le débogage
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // Paramètres de connexion MySQL (WampServer)
        $host = "localhost";
        $dbname = "site_inscription"; // ← ICI on met bien le bon nom
        $username_db = "root";
        $password_db = ""; // Mot de passe vide sur WAMP par défaut

        // Connexion à la base de données avec PDO
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username_db, $password_db);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo '<span class="icon error">❌</span>';
            echo '<h2>Erreur de connexion</h2>';
            echo '<div class="error-message">Impossible de se connecter à la base de données : ' . htmlspecialchars($e->getMessage()) . '</div>';
            echo '<div class="buttons-container">';
            echo '<a href="../inscription/inscription.html" class="button secondary-button">← Retour à l\'inscription</a>';
            echo '</div>';
            exit;
        }

        // Si formulaire soumis
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Nettoyage des données
            $username = htmlspecialchars(trim($_POST["username"]));
            $email = htmlspecialchars(trim($_POST["email"]));
            $number = htmlspecialchars(trim($_POST["number"]));
            $password = trim($_POST["password"]);

            // Vérification email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo '<span class="icon error">❌</span>';
                echo '<h2>Email invalide</h2>';
                echo '<div class="error-message">L\'adresse e-mail saisie n\'est pas valide.</div>';
                echo '<div class="buttons-container">';
                echo '<a href="../inscription/inscription.html" class="button primary-button">← Reprendre l\'inscription</a>';
                echo '</div>';
                exit;
            }

            // Hachage du mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Requête SQL préparée
            $sql = "INSERT INTO utilisateurs (username, email, phone, password)
                    VALUES (:username, :email, :phone, :password)";
                
            $stmt = $pdo->prepare($sql);

            try {
                $stmt->execute([
                    ':username' => $username,
                    ':email' => $email,
                    ':phone' => $number,
                    ':password' => $hashedPassword
                ]);
                
                echo '<span class="icon success">✅</span>';
                echo '<h2>Inscription réussie !</h2>';
                echo '<div class="success-message">';
                echo '<strong>Félicitations ' . htmlspecialchars($username) . ' !</strong><br>';
                echo 'Votre compte a été créé avec succès.<br>';
                echo 'Vous pouvez maintenant vous connecter avec vos identifiants.';
                echo '</div>';
                echo '<div class="buttons-container">';
                echo '<a href="../login/login.html" class="button primary-button">Se connecter</a>';
                echo '<a href="../acceuil/acceuil.html" class="button secondary-button">← Retour à l\'accueil</a>';
                echo '</div>';
                
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    echo '<span class="icon error">❌</span>';
                    echo '<h2>Email déjà utilisé</h2>';
                    echo '<div class="error-message">';
                    echo 'Cette adresse e-mail est déjà associée à un compte.<br>';
                    echo 'Essayez avec une autre adresse ou connectez-vous.';
                    echo '</div>';
                    echo '<div class="buttons-container">';
                    echo '<a href="../inscription/inscription.html" class="button primary-button">← Reprendre l\'inscription</a>';
                    echo '<a href="../login/login.html" class="button secondary-button">Se connecter</a>';
                    echo '</div>';
                } else {
                    echo '<span class="icon error">❌</span>';
                    echo '<h2>Erreur d\'inscription</h2>';
                    echo '<div class="error-message">Une erreur technique s\'est produite : ' . htmlspecialchars($e->getMessage()) . '</div>';
                    echo '<div class="buttons-container">';
                    echo '<a href="../inscription/inscription.html" class="button primary-button">← Reprendre l\'inscription</a>';
                    echo '</div>';
                }
            }
        } else {
            echo '<span class="icon error">⚠️</span>';
            echo '<h2>Accès non autorisé</h2>';
            echo '<div class="error-message">Cette page ne peut être accédée que par le formulaire d\'inscription.</div>';
            echo '<div class="buttons-container">';
            echo '<a href="../inscription/inscription.html" class="button primary-button">Aller à l\'inscription</a>';
            echo '<a href="../acceuil/acceuil.html" class="button secondary-button">← Retour à l\'accueil</a>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>