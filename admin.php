<?php
require 'config.php';

// Vérification du rôle admin


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fonction pour vérifier si l'utilisateur est connecté

// Fonction pour obtenir la connexion à la base de données


// Fonction pour obtenir le suivi de commande


// Fonctions spécifiques à l'admin
function getAllOrders($db) {
    $stmt = $db->query("
        SELECT s.*, u.nom_complet, u.whatsapp, u.email 
        FROM submissions s
        JOIN users u ON s.user_id = u.id
        ORDER BY s.created_at DESC
    ");
    return $stmt->fetchAll();
}

function getFilteredOrders($db, $filter) {
    $query = "
        SELECT s.*, u.nom_complet, u.whatsapp, u.email 
        FROM submissions s
        JOIN users u ON s.user_id = u.id
    ";
    
    if ($filter !== 'all') {
        $query .= " WHERE s.form_type = :filter";
    }
    
    $query .= " ORDER BY s.created_at DESC";
    
    $stmt = $db->prepare($query);
    
    if ($filter !== 'all') {
        $stmt->bindParam(':filter', $filter);
    }
    
    $stmt->execute();
    return $stmt->fetchAll();
}

function updateOrderStatus($db, $submissionId, $status, $notes = '') {
    // Vérifier d'abord si un enregistrement existe déjà
    $checkStmt = $db->prepare("SELECT * FROM order_tracking WHERE submission_id = ?");
    $checkStmt->execute([$submissionId]);
    
    if ($checkStmt->rowCount() > 0) {
        // Mise à jour si l'enregistrement existe
        $stmt = $db->prepare("
            UPDATE order_tracking 
            SET status = ?, notes = ?, status_date = CURRENT_TIMESTAMP 
            WHERE submission_id = ?
        ");
        return $stmt->execute([$status, $notes, $submissionId]);
    } else {
        // Insertion si l'enregistrement n'existe pas
        $stmt = $db->prepare("
            INSERT INTO order_tracking (submission_id, status, notes, status_date)
            VALUES (?, ?, ?, CURRENT_TIMESTAMP)
        ");
        return $stmt->execute([$submissionId, $status, $notes]);
    }
}

// Fonction pour générer le message WhatsApp
function generateWhatsAppMessage($order, $data) {
    $message = "Nouvelle commande INFINITY STORE\n\n";
    $message .= "Type: " . strtoupper($order['form_type']) . "\n";
    $message .= "Client: " . $order['nom_complet'] . "\n";
    $message .= "WhatsApp: " . $order['whatsapp'] . "\n";
    
    if (!empty($order['email'])) {
        $message .= "Email: " . $order['email'] . "\n";
    }
    
    if ($order['form_type'] === 'achat') {
        $message .= "\nDétails achat:\n";
        $message .= "Type: " . ($data['type'] ?? 'N/A') . "\n";
        $message .= "Modèle: " . ($data['modele'] ?? 'N/A') . "\n";
        
        // Ajout des détails de livraison
        if (isset($data['livraison'])) {
            $message .= "\nLivraison:\n";
            $message .= "Méthode: " . ($data['livraison']['methode'] === 'livraison_domicile' ? 'Livraison à domicile' : 'Retrait en magasin') . "\n";
            
            if ($data['livraison']['methode'] === 'livraison_domicile') {
                $message .= "Adresse: " . ($data['livraison']['adresse'] ?? 'N/A') . "\n";
                $message .= "Ville: " . ($data['livraison']['ville'] ?? 'N/A') . "\n";
                $message .= "Quartier: " . ($data['livraison']['quartier'] ?? 'N/A') . "\n";
                $message .= "Zone: " . ($data['livraison']['zone'] ?? 'N/A') . "\n";
                if (!empty($data['livraison']['infos'])) {
                    $message .= "Infos: " . $data['livraison']['infos'] . "\n";
                }
            }
        }
    } elseif ($order['form_type'] === 'troc') {
        $message .= "\nNouvel appareil:\n";
        $message .= "Type: " . ($data['new_type'] ?? 'N/A') . "\n";
        $message .= "Modèle: " . ($data['new_modele'] ?? 'N/A') . "\n";
        
        $message .= "\nAncien appareil:\n";
        $message .= "Modèle: " . ($data['old_model'] ?? 'N/A') . "\n";
        $message .= "Stockage: " . ($data['old_storage'] ?? 'N/A') . "\n";
        if (!empty($data['conditions'])) {
            $conditionsMap = [
                'no_box' => 'Sans carton',
                'screen_issue' => 'Écran endommagé',
                'battery_issue' => 'Batterie à changer',
                'no_id' => 'Compte iCloud bloquant',
                'rear_issue' => 'Coque arrière endommagée'
            ];
            $conditions = array_map(function($cond) use ($conditionsMap) {
                return $conditionsMap[$cond] ?? $cond;
            }, $data['conditions']);
            $message .= "Conditions: " . implode(', ', $conditions) . "\n";
        }
    } elseif ($order['form_type'] === 'vente') {
        $message .= "\nDétails vente:\n";
        $message .= "Modèle: " . ($data['appareil']['model'] ?? 'N/A') . "\n";
        $message .= "Capacité: " . ($data['appareil']['storage'] ?? 'N/A') . "\n";
        $message .= "Valeur estimée: " . ($data['appareil']['estimated_value'] ?? '0') . " FCFA\n";
        
        $message .= "\nConditions:\n";
        $message .= "Sans carton: " . ($data['appareil']['conditions']['no_box'] ? 'Oui' : 'Non') . "\n";
        $message .= "Écran endommagé: " . ($data['appareil']['conditions']['screen_issue'] ? 'Oui' : 'Non') . "\n";
        $message .= "Batterie à changer: " . ($data['appareil']['conditions']['battery_issue'] ? 'Oui' : 'Non') . "\n";
        $message .= "Compte iCloud bloquant: " . ($data['appareil']['conditions']['no_id'] ? 'Oui' : 'Non') . "\n";
        $message .= "Coque arrière endommagée: " . ($data['appareil']['conditions']['rear_issue'] ? 'Oui' : 'Non') . "\n";
    }
    
    return rawurlencode($message);
}

// Traitement des filtres et actions admin
$filter = $_GET['filter'] ?? 'all';
$db = getDbConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_status'])) {
        updateOrderStatus(
            $db,
            $_POST['submission_id'],
            $_POST['new_status'],
            $_POST['notes'] ?? ''
        );
    }
}

$orders = $filter === 'all' ? getAllOrders($db) : getFilteredOrders($db, $filter);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - INFINITY STORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 1.5rem;
            background: linear-gradient(135deg, #255867 0%, #4a7c87 100%);
            color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .order-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        .order-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.8rem;
        }
        .status-received { background: #d4edda; color: #155724; }
        .status-processing { background: #fff3cd; color: #856404; }
        .status-shipped { background: #cce5ff; color: #004085; }
        .status-delivered { background: #d1ecf1; color: #0c5460; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .order-details {
            margin-top: 15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        select, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            padding: 8px 15px;
            background: #255867;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-whatsapp {
            background: #25D366;
        }
        .btn-whatsapp:hover {
            background: #128C7E;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .navigation {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            align-items: center;
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
        .delivery-section {
            background: #f0f7f9;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            border-left: 3px solid #C79626;
        }
        .delivery-section h4 {
            margin-bottom: 0.5rem;
            color: #255867;
        }
        .action-buttons {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
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
        .no-orders {
            text-align: center;
            padding: 2rem;
            color: #777;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        @media (max-width: 768px) {
            .detail-row {
                flex-direction: column;
            }
            .detail-label {
                margin-bottom: 0.3rem;
            }
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Tableau de bord Admin ✨INFINITY STORE✨</h1>
            <form action="logout.php" method="post">
                <button type="submit" class="btn btn-danger">Déconnexion</button>
            </form>
        </div>

        <div class="navigation">
            <div class="filter-nav">
                <a href="?filter=all" class="<?= $filter === 'all' ? 'active' : '' ?>">Toutes</a>
                <a href="?filter=achat" class="<?= $filter === 'achat' ? 'active' : '' ?>">Achats</a>
                <a href="?filter=troc" class="<?= $filter === 'troc' ? 'active' : '' ?>">Trocs</a>
                <a href="?filter=vente" class="<?= $filter === 'vente' ? 'active' : '' ?>">Ventes</a>
            </div>
            <span>Connecté en tant qu'admin: <?= htmlspecialchars($_SESSION['nom_complet']) ?></span>
        </div>

        <div class="navigation">
            
            <a href="admin_prices.php" class="btn">Gestion des prix</a>
        </div>
        
        <?php if (empty($orders)): ?>
            <div class="no-orders">
                <p>Aucune commande trouvée pour ce filtre.</p>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): 
                $data = json_decode($order['data'], true);
                $tracking = getOrderTrackingForSubmission($db, $order['id']);
                $whatsappMessage = generateWhatsAppMessage($order, $data);
                $whatsappUrl = "https://wa.me/?text=" . $whatsappMessage;
            ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <span class="submission-type type-<?= $order['form_type'] ?>">
                                <?= strtoupper($order['form_type']) ?>
                            </span>
                            <h3>Commande #<?= $order['id'] ?></h3>
                        </div>
                        <div>
                            <span class="order-status status-<?= $tracking[0]['status'] ?? 'received' ?>">
                                <?= ucfirst($tracking[0]['status'] ?? 'received') ?>
                            </span>
                            <div class="submission-date">
                                <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                    
                    <p><strong>Client:</strong> <?= htmlspecialchars($order['nom_complet']) ?></p>
                    <p><strong>WhatsApp:</strong> <?= htmlspecialchars($order['whatsapp']) ?></p>
                    <?php if (!empty($order['email'])): ?>
                    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                    <?php endif; ?>
                    
                    <div class="order-details">
                        <?php if ($order['form_type'] === 'achat'): ?>
                            <h4>Détails achat</h4>
                            <div class="detail-row">
                                <div class="detail-label">Type:</div>
                                <div class="detail-value"><?= htmlspecialchars($data['type'] ?? 'N/A') ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Modèle:</div>
                                <div class="detail-value"><?= htmlspecialchars($data['modele'] ?? 'N/A') ?></div>
                            </div>
                            
                            <?php if (isset($data['livraison'])): ?>
                                <div class="delivery-section">
                                    <h4>Livraison</h4>
                                    <div class="detail-row">
                                        <div class="detail-label">Méthode:</div>
                                        <div class="detail-value">
                                            <?= $data['livraison']['methode'] === 'livraison_domicile' ? 'Livraison à domicile' : 'Retrait en magasin' ?>
                                        </div>
                                    </div>
                                    
                                    <?php if ($data['livraison']['methode'] === 'livraison_domicile'): ?>
                                        <div class="detail-row">
                                            <div class="detail-label">Adresse:</div>
                                            <div class="detail-value"><?= htmlspecialchars($data['livraison']['adresse'] ?? 'N/A') ?></div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Ville:</div>
                                            <div class="detail-value"><?= htmlspecialchars($data['livraison']['ville'] ?? 'N/A') ?></div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Quartier:</div>
                                            <div class="detail-value"><?= htmlspecialchars($data['livraison']['quartier'] ?? 'N/A') ?></div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Zone:</div>
                                            <div class="detail-value"><?= htmlspecialchars($data['livraison']['zone'] ?? 'N/A') ?></div>
                                        </div>
                                        <?php if (!empty($data['livraison']['infos'])): ?>
                                        <div class="detail-row">
                                            <div class="detail-label">Infos compl.:</div>
                                            <div class="detail-value"><?= htmlspecialchars($data['livraison']['infos']) ?></div>
                                        </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                        <?php elseif ($order['form_type'] === 'troc'): ?>
                            <h4>Nouvel appareil</h4>
                            <div class="detail-row">
                                <div class="detail-label">Type:</div>
                                <div class="detail-value"><?= htmlspecialchars($data['new_type'] ?? 'N/A') ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Modèle:</div>
                                <div class="detail-value"><?= htmlspecialchars($data['new_modele'] ?? 'N/A') ?></div>
                            </div>
                            
                            <h4>Ancien appareil</h4>
                            <div class="detail-row">
                                <div class="detail-label">Modèle:</div>
                                <div class="detail-value"><?= htmlspecialchars($data['old_model'] ?? 'N/A') ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Stockage:</div>
                                <div class="detail-value">
                                    <?= htmlspecialchars($data['old_storage'] ?? 'N/A') ?>
                                </div>
                            </div>
                            <?php if (!empty($data['conditions'])): ?>
                            <div class="detail-row">
                                <div class="detail-label">Conditions:</div>
                                <div class="detail-value">
                                    <?= implode(', ', array_map(function($cond) {
                                        $conditionsMap = [
                                            'no_box' => 'Sans carton',
                                            'screen_issue' => 'Écran endommagé',
                                            'battery_issue' => 'Batterie à changer',
                                            'no_id' => 'Compte iCloud bloquant',
                                            'rear_issue' => 'Coque arrière endommagée'
                                        ];
                                        return $conditionsMap[$cond] ?? $cond;
                                    }, $data['conditions'])) ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                        <?php elseif ($order['form_type'] === 'vente'): ?>
                            <div class="detail-row">
                                <div class="detail-label">Modèle:</div>
                                <div class="detail-value"><?= htmlspecialchars($data['appareil']['model'] ?? 'N/A') ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Capacité:</div>
                                <div class="detail-value"><?= htmlspecialchars($data['appareil']['storage'] ?? 'N/A') ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Valeur estimée:</div>
                                <div class="detail-value"><?= htmlspecialchars($data['appareil']['estimated_value'] ?? '0') ?> FCFA</div>
                            </div>
                            
                            <h4>Conditions de l'appareil</h4>
                            <div class="detail-row">
                                <div class="detail-label">Sans carton:</div>
                                <div class="detail-value"><?= $data['appareil']['conditions']['no_box'] ? 'Oui' : 'Non' ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Écran endommagé:</div>
                                <div class="detail-value"><?= $data['appareil']['conditions']['screen_issue'] ? 'Oui' : 'Non' ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Batterie à changer:</div>
                                <div class="detail-value"><?= $data['appareil']['conditions']['battery_issue'] ? 'Oui' : 'Non' ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Compte iCloud bloquant:</div>
                                <div class="detail-value"><?= $data['appareil']['conditions']['no_id'] ? 'Oui' : 'Non' ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Coque arrière endommagée:</div>
                                <div class="detail-value"><?= $data['appareil']['conditions']['rear_issue'] ? 'Oui' : 'Non' ?></div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <form method="post" class="status-form">
                        <input type="hidden" name="submission_id" value="<?= $order['id'] ?>">
                        
                        <div class="form-group">
                            <label>Mettre à jour le statut:</label>
                            <select name="new_status" required>
                                <option value="Reçu" <?= ($tracking[0]['status'] ?? '') === 'Reçu' ? 'selected' : '' ?>>Reçu</option>
                                <option value="En traitement" <?= ($tracking[0]['status'] ?? '') === 'En traitement' ? 'selected' : '' ?>>En traitement</option>
                                <option value="Expédié" <?= ($tracking[0]['status'] ?? '') === 'Expédié' ? 'selected' : '' ?>>Expédié</option>
                                <option value="Livré" <?= ($tracking[0]['status'] ?? '') === 'Livré' ? 'selected' : '' ?>>Livré</option>
                                <option value="Annulé" <?= ($tracking[0]['status'] ?? '') === 'Annulé' ? 'selected' : '' ?>>Annulé</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Notes:</label>
                            <textarea name="notes" rows="2"><?= $tracking[0]['notes'] ?? '' ?></textarea>
                        </div>
                        
                        <div class="action-buttons">
                            <button type="submit" name="update_status" class="btn">Mettre à jour</button>
                            <a href="<?= $whatsappUrl ?>" class="btn btn-whatsapp" target="_blank">
                                <i class="fab fa-whatsapp"></i> Partager via WhatsApp
                            </a>
                        </div>
                    </form>
                    
                    <div class="order-history">
                        <h4>Historique:</h4>
                        <?php foreach ($tracking as $entry): ?>
                            <p>
                                <strong><?= date('d/m/Y H:i', strtotime($entry['status_date'])) ?>:</strong> 
                                <?= ucfirst($entry['status']) ?>
                                <?= $entry['notes'] ? " - " . $entry['notes'] : '' ?>
                            </p>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>