<?php
require 'config.php';
initDatabase();

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}
// Empêcher les admins d'accéder à la page utilisateur normale

$userInfo = getUserInfo(getCurrentUserId());

// Traitement des requêtes API
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'get_trade_in':
            if (isset($_GET['model'])) {
                header('Content-Type: application/json');
                $data = getTradeInValue($_GET['model']);
                echo $data ? json_encode($data) : json_encode(['error' => 'Modèle non trouvé']);
                exit;
            }
            break;
            
        case 'get_delivery_fees':
            header('Content-Type: application/json');
            echo json_encode(getDeliveryFees());
            exit;
            
        case 'get_tracking':
            header('Content-Type: application/json');
            $data = getOrderTracking(getCurrentUserId());
            echo $data ? json_encode($data) : json_encode(['error' => 'Aucune commande trouvée']);
            exit;
    }
}

// Traitement des formulaires
$success_message = $error_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $form_type = $_POST['form_type'] ?? '';
    $nom = htmlspecialchars($userInfo['nom_complet']);
    $whatsapp = htmlspecialchars($userInfo['whatsapp']);
    $email = htmlspecialchars($userInfo['email'] ?? '');
    
    $details = [];
    switch ($form_type) {
        case 'achat':
            $details = [
                'type' => $_POST['achatType'] ?? '',
                'modele' => $_POST['modele'] ?? '',
                'livraison' => [
                    'methode' => $_POST['livraison'] ?? 'retrait',
                    'adresse' => $_POST['adresse_livraison'] ?? '',
                    'ville' => $_POST['ville_livraison'] ?? '',
                    'quartier' => $_POST['quartier_livraison'] ?? '',
                    'zone' => $_POST['zone_livraison'] ?? '',
                    'infos' => $_POST['infos_livraison'] ?? ''
                ]
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
                'appareil' => [
                    'model' => $_POST['model'] ?? '',
                    'storage' => $_POST['storage'] ?? '',
                    'conditions' => [
                        'no_box' => isset($_POST['hasBox']) && $_POST['hasBox'] === 'no',
                        'screen_issue' => isset($_POST['screen_condition']) && $_POST['screen_condition'] === 'issue',
                        'battery_issue' => isset($_POST['battery_condition']) && $_POST['battery_condition'] === 'issue',
                        'no_id' => isset($_POST['no_id']) && $_POST['no_id'] === 'yes',
                        'rear_issue' => isset($_POST['body_condition']) && $_POST['body_condition'] === 'issue'
                    ],
                    'estimated_value' => $_POST['estimated_value'] ?? 0
                ]
            ];
            break;
    }
    
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
$phonePricesNeuf = json_encode(getPhonePrices('neuf'), JSON_UNESCAPED_UNICODE);
$phonePricesQuasi = json_encode(getPhonePrices('quasi'), JSON_UNESCAPED_UNICODE);
$tradeInModels = json_encode(getTradeInModels(), JSON_UNESCAPED_UNICODE);
$deliveryFees = json_encode(getDeliveryFees(), JSON_UNESCAPED_UNICODE);
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
      
        .main-container {
            width: 95%; max-width: 1200px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            padding: 2rem;
            box-shadow: 0 25px 50px rgba(37, 88, 103, 0.3);
        }
        
        .logo {
            width: 120px;
            height: 60px;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo img {
            width: 100%;
            height: auto;
            max-height: 120px;
            object-fit: contain;
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
        .btn-secondary {
            background: linear-gradient(135deg, #4a7c87, #255867);
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
        #livraisonDetails {
            background: #f5f5f5;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            border-left: 3px solid #C79626;
        }
        #livraisonDetails .form-group {
            margin-bottom: 1rem;
        }
        .timeline {
            position: relative;
            padding-left: 50px;
            margin-top: 2rem;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 2rem;
            border-left: 2px solid #C79626;
        }
        .timeline-item:last-child {
            border-left: 2px solid transparent;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -10px;
            top: 0;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #C79626;
        }
        .timeline-content {
            background: #f5f5f5;
            padding: 1rem;
            border-radius: 8px;
            margin-left: 1rem;
        }
        .timeline-date {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }
        .timeline-status {
            font-weight: bold;
            color: #255867;
            margin-bottom: 0.5rem;
            text-transform: capitalize;
        }
        .status-received { color: #255867; }
        .status-processing { color: #4a7c87; }
        .status-shipped { color: #C79626; }
        .status-delivered { color: #28a745; }
        .status-cancelled { color: #dc3545; }
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
        small {
            display: block;
            font-size: 0.8rem;
            color: #666;
            margin-top: 0.3rem;
        }
        .section-title {
            margin: 1.5rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
            color: #255867;
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
        
        <div class="logo">
            <img src="logo.png" alt="INFINITY STORE Logo">
        </div>

        <h1>✨INFINITY STORE ✨</h1>
        
        <div class="tab-nav">
            <button class="btn-tab active" data-tab="achat">📱 Achat Direct</button>
            <button class="btn-tab" data-tab="troc">🔁 Troc d'iPhone</button>
            <button class="btn-tab" data-tab="vente">💰 Vente d'iPhone</button>
            <button class="btn-tab" data-tab="suivi">📦 Suivi de commande</button>
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
                    <label class="form-label">Méthode de livraison</label><br>
                    <div class="form-check">
                        <input type="radio" name="livraison" id="retrait" value="retrait" checked>
                        <label for="retrait">Retrait en magasin</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="livraison" id="livraison_domicile" value="livraison_domicile">
                        <label for="livraison_domicile">Livraison à domicile</label>
                    </div>
                </div>

                <div id="livraisonDetails" style="display: none;">
                    <div class="form-group">
                        <label class="form-label">Adresse de livraison</label>
                        <input type="text" class="form-control" name="adresse_livraison">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ville</label>
                        <input type="text" class="form-control" name="ville_livraison">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Quartier</label>
                        <input type="text" class="form-control" name="quartier_livraison">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Zone</label>
                        <select class="form-select" name="zone_livraison" id="zoneLivraison">
                            <option value="" disabled selected>Sélectionnez votre zone</option>
                            <?php foreach (getDeliveryFees() as $zone): ?>
                                <option value="<?= htmlspecialchars($zone['zone']) ?>"><?= htmlspecialchars($zone['zone']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Informations complémentaires</label>
                        <textarea class="form-control" name="infos_livraison" rows="2"></textarea>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Nom complet</label>
                    <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($userInfo['nom_complet']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Numéro WhatsApp</label>
                    <input type="tel" class="form-control" name="whatsapp" value="<?= htmlspecialchars($userInfo['whatsapp']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($userInfo['email'] ?? '') ?>">
                </div>
                
                <button type="submit" class="btn">Soumettre l'achat</button>
            </form>
        </div>

        <!-- Section Troc -->
        <div class="form-section" id="troc-section">
            <form method="post">
                <input type="hidden" name="form_type" value="troc">
                <h3 class="section-title">Nouvel appareil souhaité</h3>
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

                <h3 class="section-title">Votre appareil actuel</h3>
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
                        <option value="64 gb">64 GB</option>
                        <option value="128 gb">128 GB</option>
                        <option value="256 gb">256 GB</option>
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
                        <label for="screenIssue">Écran endommagé</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="batteryIssue" name="oldPhoneCondition[]" value="battery_issue">
                        <label for="batteryIssue">Batterie à changer</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="noID" name="oldPhoneCondition[]" value="no_id">
                        <label for="noID">Compte iCloud bloquant</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="rearIssue" name="oldPhoneCondition[]" value="rear_issue">
                        <label for="rearIssue">Coque arrière endommagée</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="button" id="estimateTradeBtn" class="btn btn-secondary">Estimer la valeur</button>
                </div>
                <div class="total-price" id="tradeInEstimationResult" style="display: none;">
                    Valeur estimée: <span id="estimatedTradeInValue">0</span> FCFA
                </div>
                <div class="total-price" id="tradeDifferenceResult" style="display: none;">
                    Montant à payer: <span id="tradeDifferenceValue">0</span> FCFA
                </div>

                <div class="form-group">
                    <label class="form-label">Nom complet</label>
                    <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($userInfo['nom_complet']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Numéro WhatsApp</label>
                    <input type="tel" class="form-control" name="whatsapp" value="<?= htmlspecialchars($userInfo['whatsapp']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($userInfo['email'] ?? '') ?>">
                </div>

                <button type="submit" class="btn">Soumettre la Demande</button>
            </form>
        </div>

        <!-- Section Vente -->
        <div class="form-section" id="vente-section">
            <form method="post">
                <input type="hidden" name="form_type" value="vente">
                <input type="hidden" name="estimated_value" id="estimatedValueField" value="0">
                
                <div class="form-group">
                    <label class="form-label">Modèle de l'iPhone</label>
                    <select class="form-select" id="venteModel" name="model" required>
                        <option value="" disabled selected>Choisir votre modèle</option>
                        <?php foreach (getTradeInModels() as $model): ?>
                            <option value="<?= htmlspecialchars($model) ?>"><?= htmlspecialchars($model) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Capacité</label>
                    <select class="form-select" id="venteStorage" name="storage" required>
                        <option value="64g">64 GB</option>
                        <option value="128g">128 GB</option>
                        <option value="256g">256 GB</option>
                        <option value="512g">512 GB</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">État de l'appareil</label>
                    <div class="form-check">
                        <input type="checkbox" id="venteNoBox" name="hasBox" value="no">
                        <label for="venteNoBox">Sans carton</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="venteScreenIssue" name="screen_condition" value="issue">
                        <label for="venteScreenIssue">Écran endommagé</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="venteBatteryIssue" name="battery_condition" value="issue">
                        <label for="venteBatteryIssue">Batterie à changer</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="venteNoID" name="no_id" value="yes">
                        <label for="venteNoID">Compte iCloud bloquant</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="venteRearIssue" name="body_condition" value="issue">
                        <label for="venteRearIssue">Coque arrière endommagée</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="button" id="estimateSellBtn" class="btn btn-secondary">Estimer la valeur</button>
                </div>
                <div class="total-price" id="sellEstimationResult" style="display: none;">
                    Valeur estimée: <span id="estimatedSellValue">0</span> FCFA
                    <small>(estimation indicative, valeur finale après expertise)</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Nom complet</label>
                    <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($userInfo['nom_complet']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Numéro WhatsApp</label>
                    <input type="tel" class="form-control" name="whatsapp" value="<?= htmlspecialchars($userInfo['whatsapp']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($userInfo['email'] ?? '') ?>">
                </div>

                <button type="submit" class="btn">Demander une estimation</button>
            </form>
        </div>

        <!-- Section Suivi de commande -->
        <div class="form-section" id="suivi-section">
            <h2>Suivi de votre commande</h2>
            <p>Vos commandes récentes apparaîtront ci-dessous</p>
            
            <button type="button" id="checkStatusBtn" class="btn">Actualiser les commandes</button>
            
            <div id="trackingResults" style="margin-top: 2rem;">
                <h3>Historique de vos commandes</h3>
                <div id="trackingTimeline" class="timeline"></div>
            </div>
        </div>
    </div>
    
    <div class="navigation">
        <a href="../acceuil/acceuil.html" class="nav-link home-link">
            <span class="emoji">🏠</span>
            Accueil
        </a>
        
        <span style="margin: 0 1rem; font-weight: bold; color: #255867;">
            Connecté en tant que <?= htmlspecialchars($userInfo['nom_complet']) ?>
        </span>
        
        <a href="logout.php" class="nav-link logout-link">
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
        const deliveryFees = <?= $deliveryFees ?>;

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
                    priceDisplayElement.textContent = `${selectedPhone.model} ${selectedPhone.storage} - ${selectedPhone.price} FCFA`;
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
            
            if (!model) {
                alert("Veuillez sélectionner un modèle");
                return Promise.reject("Modèle non sélectionné");
            }

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
                document.getElementById('tradeInEstimationResult').style.display = 'block';
                
                // Calcul de la différence avec le nouveau téléphone
                calculateTradeDifference(finalValue);
                
                return finalValue;
            } catch (error) {
                console.error('Erreur:', error);
                alert("Erreur de calcul. Veuillez vérifier les informations et réessayer.");
                throw error;
            }
        }

        // Calcul de la différence pour le troc
        function calculateTradeDifference(tradeInValue) {
            const newPhoneSelection = document.getElementById('trocModeleSelect').value;
            if (!newPhoneSelection) {
                document.getElementById('tradeDifferenceResult').style.display = 'none';
                return;
            }
            
            const [model, storage] = newPhoneSelection.split('|');
            const type = document.querySelector('input[name="newAchatTypeTroc"]:checked').value;
            
            const selectedPhone = phonePrices[type].find(item => 
                item.model === model && item.storage === storage
            );
            
            if (selectedPhone) {
                const difference = selectedPhone.price - tradeInValue;
                document.getElementById('tradeDifferenceValue').textContent = difference;
                document.getElementById('tradeDifferenceResult').style.display = 'block';
            }
        }

        // Calcul de la valeur estimée pour la vente
        async function calculateSellValue() {
            const model = document.getElementById('venteModel').value;
            const storage = document.getElementById('venteStorage').value;
            
            if (!model) {
                alert("Veuillez sélectionner un modèle");
                return;
            }

            try {
                const response = await fetch(`?action=get_trade_in&model=${encodeURIComponent(model)}`);
                const data = await response.json();
                
                if (data.error) throw new Error(data.error);
                
                // Valeur de base
                let baseValue = data.base_value;
                if (storage === '128g' || storage === '256g' || storage === '512g') {
                    baseValue = data.superior_value;
                }
                
                // Calcul des déductions
                let totalDeduction = 0;
                if (document.getElementById('venteNoBox').checked) totalDeduction += parseFloat(data.deduction_no_box || 0);
                if (document.getElementById('venteScreenIssue').checked) totalDeduction += parseFloat(data.deduction_screen_issue || 0);
                if (document.getElementById('venteBatteryIssue').checked) totalDeduction += parseFloat(data.deduction_battery_issue || 0);
                if (document.getElementById('venteNoID').checked) totalDeduction += parseFloat(data.deduction_no_id || 0);
                if (document.getElementById('venteRearIssue').checked) totalDeduction += parseFloat(data.deduction_rear_issue || 0);
                
                // Valeur finale
                const finalValue = Math.max(0, baseValue - totalDeduction);
                document.getElementById('estimatedSellValue').textContent = finalValue;
                document.getElementById('estimatedValueField').value = finalValue;
                document.getElementById('sellEstimationResult').style.display = 'block';
            } catch (error) {
                console.error('Erreur:', error);
                alert("Erreur de calcul. Veuillez vérifier les informations et réessayer.");
            }
        }

        // Affichage des résultats du suivi
        async function loadTrackingResults() {
            try {
                const response = await fetch(`?action=get_tracking`);
                const data = await response.json();
                
                const timeline = document.getElementById('trackingTimeline');
                timeline.innerHTML = '';
                
                if (data.error) {
                    timeline.innerHTML = `<p>${data.error}</p>`;
                    return;
                }
                
                if (data.length === 0) {
                    timeline.innerHTML = '<p>Aucune commande trouvée.</p>';
                    return;
                }
                
                data.forEach(order => {
                    const item = document.createElement('div');
                    item.className = 'timeline-item';
                    
                    const statusClass = `status-${order.status.replace(' ', '_')}`;
                    
                    item.innerHTML = `
                        <div class="timeline-content">
                            <div class="timeline-date">${new Date(order.status_date).toLocaleString()}</div>
                            <div class="timeline-status ${statusClass}">${order.status.replace(/_/g, ' ')}</div>
                            ${order.notes ? `<p>${order.notes}</p>` : ''}
                            <div class="timeline-order-info">
                                <small>Type: ${order.form_type}</small><br>
                                <small>Date de commande: ${new Date(order.created_at).toLocaleDateString()}</small>
                            </div>
                        </div>
                    `;
                    
                    timeline.appendChild(item);
                });
                
                document.getElementById('trackingResults').style.display = 'block';
            } catch (error) {
                console.error('Erreur:', error);
                document.getElementById('trackingTimeline').innerHTML = '<p>Erreur lors du chargement des commandes</p>';
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
                    
                    // Charger automatiquement le suivi quand on clique sur l'onglet
                    if (this.dataset.tab === 'suivi') {
                        loadTrackingResults();
                    }
                });
            });

            // Initialisation des sélecteurs
            updateModelSelect('neuf', document.getElementById('modeleSelect'));
            updateModelSelect('neuf', document.getElementById('trocModeleSelect'));

            // Gestion de l'affichage des détails de livraison
            document.querySelectorAll('input[name="livraison"]').forEach(input => {
                input.addEventListener('change', function() {
                    const livraisonDetails = document.getElementById('livraisonDetails');
                    livraisonDetails.style.display = this.value === 'livraison_domicile' ? 'block' : 'none';
                    updatePriceDisplay(document.getElementById('modeleSelect'), document.getElementById('achatPrix'));
                });
            });

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
                
                // Recalculer la différence si une estimation existe déjà
                if (document.getElementById('tradeInEstimationResult').style.display !== 'none') {
                    const currentEstimate = parseFloat(document.getElementById('estimatedTradeInValue').textContent);
                    calculateTradeDifference(currentEstimate);
                }
            });

            // Boutons d'estimation
            document.getElementById('estimateTradeBtn').addEventListener('click', calculateTradeInValue);
            document.getElementById('estimateSellBtn').addEventListener('click', calculateSellValue);

            // Bouton de vérification du statut
            document.getElementById('checkStatusBtn').addEventListener('click', loadTrackingResults);

            // Calculateur de troc
            document.getElementById('trocOldModel').addEventListener('change', function() {
                document.getElementById('tradeInEstimationResult').style.display = 'none';
                document.getElementById('tradeDifferenceResult').style.display = 'none';
            });
            document.getElementById('trocOldStorage').addEventListener('change', function() {
                document.getElementById('tradeInEstimationResult').style.display = 'none';
                document.getElementById('tradeDifferenceResult').style.display = 'none';
            });
            document.querySelectorAll('input[name="oldPhoneCondition[]"]').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    document.getElementById('tradeInEstimationResult').style.display = 'none';
                    document.getElementById('tradeDifferenceResult').style.display = 'none';
                });
            });

            // Calculateur de vente
            document.getElementById('venteModel').addEventListener('change', function() {
                document.getElementById('sellEstimationResult').style.display = 'none';
            });
            document.getElementById('venteStorage').addEventListener('change', function() {
                document.getElementById('sellEstimationResult').style.display = 'none';
            });
            document.querySelectorAll('#vente-section input[type="checkbox"]').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    document.getElementById('sellEstimationResult').style.display = 'none';
                });
            });

            // Charger les commandes au démarrage si on est sur l'onglet suivi
            if (document.querySelector('.btn-tab.active').dataset.tab === 'suivi') {
                loadTrackingResults();
            }
        });
    </script>
</body>
</html>