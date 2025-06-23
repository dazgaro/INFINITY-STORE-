<?php
// admin.php

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

// Vérification de l'authentification
session_start();
// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}

// Récupération des soumissions
function getSubmissions($type = null) {
    $db = getDbConnection();
    
    if ($type) {
        $stmt = $db->prepare("SELECT * FROM submissions WHERE form_type = ? ORDER BY created_at DESC");
        $stmt->execute([$type]);
    } else {
        $stmt = $db->query("SELECT * FROM submissions ORDER BY created_at DESC");
    }
    
    return $stmt->fetchAll();
}

// Traitement des filtres
$filter = $_GET['filter'] ?? 'all';
$submissions = getSubmissions($filter === 'all' ? null : $filter);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - INFINITY STORE</title>
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
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .filter-nav {
            display: flex;
            margin-bottom: 1.5rem;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .filter-nav a {
            padding: 0.8rem 1.5rem;
            text-decoration: none;
            color: #555;
            font-weight: 500;
            transition: all 0.3s;
        }
        .filter-nav a:hover, .filter-nav a.active {
            background: #255867;
            color: white;
        }
        .filter-nav a.active {
            font-weight: 600;
        }
        .submission-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: relative;
        }
        .submission-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        .submission-type {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .type-achat { background: #d4edda; color: #155724; }
        .type-troc { background: #cce5ff; color: #004085; }
        .type-vente { background: #fff3cd; color: #856404; }
        .submission-date {
            color: #777;
            font-size: 0.9rem;
        }
        .submission-details {
            margin-top: 1rem;
        }
        .detail-row {
            display: flex;
            margin-bottom: 0.5rem;
        }
        .detail-label {
            font-weight: 600;
            min-width: 150px;
            color: #555;
        }
        .detail-value {
            flex: 1;
        }
        .json-data {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            margin-top: 1rem;
            font-family: monospace;
            white-space: pre-wrap;
            font-size: 0.9rem;
            max-height: 200px;
            overflow-y: auto;
        }
        .no-submissions {
            text-align: center;
            padding: 2rem;
            color: #777;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
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
        @media (max-width: 768px) {
            .admin-header {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }
            .detail-row {
                flex-direction: column;
            }
            .detail-label {
                margin-bottom: 0.3rem;
            }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <img src="logo.png" alt="INFINITY STORE Logo" class="logo">
        <nav class="admin-nav">
            <a href="admin.php">Tableau de bord</a>
            <a href="../login/admin_prices.php">Gestion des prix</a>
            <form action="../login/logout.php" method="post" style="display: inline;">
                <button type="submit" class="logout-btn">Déconnexion</button>
            </form>
        </nav>
    </header>

    <div class="container">
        <h1>Gestion des soumissions</h1>
        
        <div class="filter-nav">
            <a href="?filter=all" class="<?= $filter === 'all' ? 'active' : '' ?>">Toutes</a>
            <a href="?filter=achat" class="<?= $filter === 'achat' ? 'active' : '' ?>">Achats</a>
            <a href="?filter=troc" class="<?= $filter === 'troc' ? 'active' : '' ?>">Trocs</a>
            <a href="?filter=vente" class="<?= $filter === 'vente' ? 'active' : '' ?>">Ventes</a>
        </div>

        <?php if (empty($submissions)): ?>
            <div class="no-submissions">
                <p>Aucune soumission trouvée pour ce filtre.</p>
            </div>
        <?php else: ?>
            <?php foreach ($submissions as $submission): ?>
                <div class="submission-card">
                    <div class="submission-header">
                        <div>
                            <span class="submission-type type-<?= $submission['form_type'] ?>">
                                <?= strtoupper($submission['form_type']) ?>
                            </span>
                            <h3><?= htmlspecialchars($submission['nom']) ?></h3>
                        </div>
                        <div class="submission-date">
                            <?= date('d/m/Y H:i', strtotime($submission['created_at'])) ?>
                        </div>
                    </div>
                    
                    <div class="submission-details">
                        <div class="detail-row">
                            <div class="detail-label">WhatsApp:</div>
                            <div class="detail-value"><?= htmlspecialchars($submission['whatsapp']) ?></div>
                        </div>
                        <?php if (!empty($submission['email'])): ?>
                        <div class="detail-row">
                            <div class="detail-label">Email:</div>
                            <div class="detail-value"><?= htmlspecialchars($submission['email']) ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php 
                        $data = json_decode($submission['data'], true);
                        $details = $data['details'] ?? [];
                        ?>
                        
                        <?php if ($submission['form_type'] === 'achat'): ?>
                            <div class="detail-row">
                                <div class="detail-label">Type:</div>
                                <div class="detail-value"><?= htmlspecialchars($details['type'] ?? 'N/A') ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Modèle:</div>
                                <div class="detail-value"><?= htmlspecialchars($details['modele'] ?? 'N/A') ?></div>
                            </div>
                            <?php if (!empty($details['commentaire'])): ?>
                            <div class="detail-row">
                                <div class="detail-label">Commentaire:</div>
                                <div class="detail-value"><?= htmlspecialchars($details['commentaire']) ?></div>
                            </div>
                            <?php endif; ?>
                            
                        <?php elseif ($submission['form_type'] === 'troc'): ?>
                            <h4>Nouvel appareil</h4>
                            <div class="detail-row">
                                <div class="detail-label">Type:</div>
                                <div class="detail-value"><?= htmlspecialchars($details['new_type'] ?? 'N/A') ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Modèle:</div>
                                <div class="detail-value"><?= htmlspecialchars($details['new_modele'] ?? 'N/A') ?></div>
                            </div>
                            
                            <h4>Ancien appareil</h4>
                            <div class="detail-row">
                                <div class="detail-label">Modèle:</div>
                                <div class="detail-value"><?= htmlspecialchars($details['old_model'] ?? 'N/A') ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Stockage:</div>
                                <div class="detail-value">
                                    <?= $details['old_storage'] === 'superieur' ? 'Supérieur' : 'De base' ?>
                                </div>
                            </div>
                            <?php if (!empty($details['conditions'])): ?>
                            <div class="detail-row">
                                <div class="detail-label">Conditions:</div>
                                <div class="detail-value">
                                    <?= implode(', ', array_map(function($cond) {
                                        $conditionsMap = [
                                            'no_box' => 'Sans carton',
                                            'screen_issue' => 'Panne écran',
                                            'battery_issue' => 'Panne batterie',
                                            'no_id' => 'Sans ID',
                                            'rear_issue' => 'Souci coque'
                                        ];
                                        return $conditionsMap[$cond] ?? $cond;
                                    }, $details['conditions'])) ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                        <?php elseif ($submission['form_type'] === 'vente'): ?>
                            <div class="detail-row">
                                <div class="detail-label">Modèle:</div>
                                <div class="detail-value"><?= htmlspecialchars($details['model'] ?? 'N/A') ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Capacité:</div>
                                <div class="detail-value"><?= htmlspecialchars($details['storage'] ?? 'N/A') ?> Go</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">État écran:</div>
                                <div class="detail-value"><?= htmlspecialchars($details['screen_condition'] ?? 'N/A') ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">État batterie:</div>
                                <div class="detail-value"><?= htmlspecialchars($details['battery_condition'] ?? 'N/A') ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Fonctionnalités:</div>
                                <div class="detail-value"><?= htmlspecialchars($details['functionality'] ?? 'N/A') ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">État général:</div>
                                <div class="detail-value"><?= htmlspecialchars($details['body_condition'] ?? 'N/A') ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Carton:</div>
                                <div class="detail-value"><?= $details['has_box'] === 'oui' ? 'Oui' : 'Non' ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Accessoires:</div>
                                <div class="detail-value"><?= $details['has_accessories'] === 'oui' ? 'Oui' : 'Non' ?></div>
                            </div>
                            <?php if (!empty($details['accessories_details'])): ?>
                            <div class="detail-row">
                                <div class="detail-label">Détails access.:</div>
                                <div class="detail-value"><?= htmlspecialchars($details['accessories_details']) ?></div>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                       
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>