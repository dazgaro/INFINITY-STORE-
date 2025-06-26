-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 26 juin 2025 à 00:19
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `iphone_store`
--

-- --------------------------------------------------------

--
-- Structure de la table `delivery_fees`
--

DROP TABLE IF EXISTS `delivery_fees`;
CREATE TABLE IF NOT EXISTS `delivery_fees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `zone` varchar(50) NOT NULL,
  `fee` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `delivery_fees`
--

INSERT INTO `delivery_fees` (`id`, `zone`, `fee`, `created_at`, `updated_at`) VALUES
(1, 'Abidjan (Plateau)', 5000, '2025-06-25 10:14:07', '2025-06-25 10:14:07'),
(2, 'Abidjan (Autres communes)', 5000, '2025-06-25 10:14:07', '2025-06-25 10:14:07'),
(3, 'Grand-Bassam', 7000, '2025-06-25 10:14:07', '2025-06-25 10:14:07'),
(4, 'Grands ponts (Bingerville, Anyama, etc.)', 8000, '2025-06-25 10:14:07', '2025-06-25 10:14:07'),
(5, 'Autres régions', 10000, '2025-06-25 10:14:07', '2025-06-25 10:14:07');

-- --------------------------------------------------------

--
-- Structure de la table `order_tracking`
--

DROP TABLE IF EXISTS `order_tracking`;
CREATE TABLE IF NOT EXISTS `order_tracking` (
  `id` int NOT NULL AUTO_INCREMENT,
  `submission_id` int NOT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `status` enum('Reçu','En traitement','Expédié','Livré','Annulé') DEFAULT 'Reçu',
  `status_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `submission_id` (`submission_id`),
  KEY `idx_whatsapp` (`whatsapp`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `order_tracking`
--

INSERT INTO `order_tracking` (`id`, `submission_id`, `whatsapp`, `status`, `status_date`, `notes`) VALUES
(1, 8, '1361637', 'Annulé', '2025-06-26 00:05:01', ''),
(2, 9, '1361637', 'Livré', '2025-06-26 00:04:55', ''),
(3, 10, '1361637', 'En traitement', '2025-06-26 00:04:49', '');

-- --------------------------------------------------------

--
-- Structure de la table `phone_prices`
--

DROP TABLE IF EXISTS `phone_prices`;
CREATE TABLE IF NOT EXISTS `phone_prices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` enum('neuf','quasi') NOT NULL,
  `model` varchar(50) NOT NULL,
  `storage` varchar(20) NOT NULL,
  `price` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`),
  KEY `idx_model` (`model`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `phone_prices`
--

INSERT INTO `phone_prices` (`id`, `type`, `model`, `storage`, `price`) VALUES
(1, 'neuf', 'iPhone 16 Pro Max', '256g', 785000),
(2, 'neuf', 'iPhone 16 Pro', '256g', 685000),
(3, 'neuf', 'iPhone 16 Pro', '128g', 675000),
(4, 'neuf', 'iPhone 16 Plus', '256g', 665000),
(5, 'neuf', 'iPhone 15 Pro Max', '512g', 635000),
(6, 'neuf', 'iPhone 15 Pro Max', '256g', 615000),
(7, 'neuf', 'iPhone 16', '256g', 575000),
(8, 'neuf', 'iPhone 15 Pro', '256g', 555000),
(9, 'neuf', 'iPhone 16 Plus', '128g', 550000),
(10, 'neuf', 'iPhone 15 Pro', '128g', 515000),
(11, 'neuf', 'iPhone 16', '128g', 495000),
(12, 'neuf', 'iPhone 14 Pro Max', '256g', 485000),
(13, 'neuf', 'iPhone 14 Pro Max', '128g', 460000),
(14, 'neuf', 'iPhone 15', '256g', 455000),
(15, 'neuf', 'iPhone 14 Pro', '256g', 445000),
(16, 'neuf', 'iPhone 15', '128g', 395000),
(17, 'neuf', 'iPhone 14 Pro', '128g', 385000),
(18, 'neuf', 'iPhone 13 Pro Max', '256g', 375000),
(19, 'neuf', 'iPhone 13 Pro Max', '128g', 350000),
(20, 'neuf', 'iPhone 14', '256g', 325000),
(21, 'neuf', 'iPhone 14', '128g', 295000),
(22, 'neuf', 'iPhone 13 Pro', '128g', 290000),
(23, 'neuf', 'iPhone 12 Pro Max', '128g', 285000),
(24, 'neuf', 'iPhone 12 Pro', '128g', 245000),
(25, 'neuf', 'iPhone 13', '128g', 245000),
(26, 'neuf', 'iPhone 12', '64g', 175000),
(27, 'neuf', 'iPhone 11', '128g', 150000),
(28, 'neuf', 'iPhone 11', '64g', 140000),
(29, 'neuf', 'iPhone XR', '128g', 125000),
(30, 'neuf', 'iPhone XR', '64g', 120000),
(31, 'neuf', 'iPhone X', '64g', 100000),
(32, 'quasi', 'iPhone 15 Pro Max', '256g', 565000),
(33, 'quasi', 'iPhone 14 Pro Max', '128g', 415000),
(34, 'quasi', 'iPhone 14 Pro', '128g', 370000),
(35, 'quasi', 'iPhone 13 Pro Max', '256g', 310000),
(36, 'quasi', 'iPhone 13 Pro Max', '128g', 300000),
(37, 'quasi', 'iPhone 13 Pro', '256g', 280000),
(38, 'quasi', 'iPhone 13 Pro', '128g', 260000),
(39, 'quasi', 'iPhone 12 Pro Max', '128g', 260000),
(40, 'quasi', 'iPhone 13', '128g', 195000),
(41, 'quasi', 'iPhone 12 Pro', '128g', 185000),
(42, 'quasi', 'iPhone 13 Mini', '128g', 180000),
(43, 'quasi', 'iPhone 11 Pro', '64g', 150000),
(44, 'quasi', 'iPhone 12', '64g', 150000),
(45, 'quasi', 'iPhone 11', '64g', 125000),
(46, 'quasi', 'iPhone 12 Mini', '64g', 125000),
(47, 'quasi', 'iPhone XR', '64g', 105000),
(48, 'quasi', 'iPhone X', '64g', 90000);

-- --------------------------------------------------------

--
-- Structure de la table `submissions`
--

DROP TABLE IF EXISTS `submissions`;
CREATE TABLE IF NOT EXISTS `submissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `form_type` enum('achat','troc','vente') NOT NULL,
  `nom` varchar(100) NOT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `data` json NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `submissions`
--

INSERT INTO `submissions` (`id`, `user_id`, `form_type`, `nom`, `whatsapp`, `email`, `data`, `created_at`) VALUES
(1, 2, 'vente', 'DAKOURI', '12322234', '', '{\"appareil\": {\"model\": \"iPhone 14 Pro Max\", \"storage\": \"64g\", \"conditions\": {\"no_id\": false, \"no_box\": false, \"rear_issue\": false, \"screen_issue\": true, \"battery_issue\": false}, \"estimated_value\": \"200000\"}}', '2025-06-25 22:43:34'),
(2, 2, 'achat', 'DAKOURI', '12322234', '', '{\"type\": \"neuf\", \"modele\": \"iPhone 16 Pro|128g\", \"livraison\": {\"zone\": \"Abidjan (Autres communes)\", \"infos\": \"\", \"ville\": \"\", \"adresse\": \"abidjan\", \"methode\": \"livraison_domicile\", \"quartier\": \"\"}}', '2025-06-25 23:06:41'),
(3, 3, 'achat', 'ABDOUL MAL', '0102940143', '', '{\"type\": \"neuf\", \"modele\": \"iPhone 15 Pro Max|256g\", \"livraison\": {\"zone\": \"Abidjan (Autres communes)\", \"infos\": \"\", \"ville\": \"\", \"adresse\": \"abidjan\", \"methode\": \"livraison_domicile\", \"quartier\": \"\"}}', '2025-06-25 23:20:48'),
(4, 2, 'troc', 'DAKOURI', '12322234', '', '{\"new_type\": \"neuf\", \"old_model\": \"iPhone 11\", \"conditions\": [\"screen_issue\", \"battery_issue\"], \"new_modele\": \"iPhone 16 Pro Max|256g\", \"old_storage\": \"superieur\"}', '2025-06-25 23:25:38'),
(5, 3, 'vente', 'ABDOUL MAL', '0102940143', '', '{\"appareil\": {\"model\": \"iPhone 11 Pro Max\", \"storage\": \"512g\", \"conditions\": {\"no_id\": false, \"no_box\": false, \"rear_issue\": false, \"screen_issue\": false, \"battery_issue\": false}, \"estimated_value\": \"0\"}}', '2025-06-25 23:36:17'),
(6, 2, 'troc', 'DAKOURI', '12322234', '', '{\"new_type\": \"neuf\", \"old_model\": \"iPhone 11 Pro Max\", \"conditions\": [], \"new_modele\": \"iPhone 16 Pro Max|256g\", \"old_storage\": \"128r\"}', '2025-06-25 23:41:46'),
(7, 2, 'troc', 'DAKOURI', '12322234', '', '{\"new_type\": \"neuf\", \"old_model\": \"iPhone 15\", \"conditions\": [], \"new_modele\": \"iPhone 16 Pro|128g\", \"old_storage\": \"128 gb\"}', '2025-06-25 23:57:43'),
(8, 4, 'achat', 'TAY Z', '1361637', '', '{\"type\": \"neuf\", \"modele\": \"iPhone 14 Pro Max|256g\", \"livraison\": {\"zone\": \"\", \"infos\": \"\", \"ville\": \"\", \"adresse\": \"\", \"methode\": \"retrait\", \"quartier\": \"\"}}', '2025-06-26 00:04:07'),
(9, 4, 'troc', 'TAY Z', '1361637', '', '{\"new_type\": \"neuf\", \"old_model\": \"iPhone 14\", \"conditions\": [], \"new_modele\": \"iPhone 15 Pro Max|512g\", \"old_storage\": \"64 gb\"}', '2025-06-26 00:04:19'),
(10, 4, 'vente', 'TAY Z', '1361637', '', '{\"appareil\": {\"model\": \"iPhone 14 Pro\", \"storage\": \"64g\", \"conditions\": {\"no_id\": false, \"no_box\": false, \"rear_issue\": false, \"screen_issue\": false, \"battery_issue\": false}, \"estimated_value\": \"0\"}}', '2025-06-26 00:04:32');

-- --------------------------------------------------------

--
-- Structure de la table `trade_in_values`
--

DROP TABLE IF EXISTS `trade_in_values`;
CREATE TABLE IF NOT EXISTS `trade_in_values` (
  `id` int NOT NULL AUTO_INCREMENT,
  `model` varchar(50) NOT NULL,
  `base_value` int NOT NULL,
  `superior_value` int NOT NULL,
  `deduction_no_box` int DEFAULT '0',
  `deduction_screen_issue` int DEFAULT '0',
  `deduction_battery_issue` int DEFAULT '0',
  `deduction_no_id` int DEFAULT '0',
  `deduction_rear_issue` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_model` (`model`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `trade_in_values`
--

INSERT INTO `trade_in_values` (`id`, `model`, `base_value`, `superior_value`, `deduction_no_box`, `deduction_screen_issue`, `deduction_battery_issue`, `deduction_no_id`, `deduction_rear_issue`) VALUES
(1, 'iPhone 7', 20000, 25000, 5000, 5000, 5000, 0, 5000),
(2, 'iPhone 7 Plus', 30000, 35000, 5000, 10000, 5000, 0, 10000),
(3, 'iPhone 8', 30000, 40000, 5000, 5000, 5000, 0, 10000),
(4, 'iPhone X', 45000, 50000, 5000, 10000, 5000, 20000, 10000),
(5, 'iPhone XR', 60000, 70000, 5000, 10000, 10000, 20000, 10000),
(6, 'iPhone XS', 50000, 60000, 5000, 10000, 5000, 20000, 10000),
(7, 'iPhone XS Max', 65000, 80000, 5000, 15000, 10000, 20000, 10000),
(8, 'iPhone 11', 85000, 90000, 5000, 10000, 10000, 20000, 10000),
(9, 'iPhone 11 Pro', 95000, 100000, 5000, 15000, 10000, 25000, 10000),
(10, 'iPhone 11 Pro Max', 100000, 115000, 5000, 15000, 10000, 30000, 10000),
(11, 'iPhone 12 mini', 85000, 105000, 5000, 15000, 10000, 30000, 10000),
(12, 'iPhone 12', 110000, 115000, 5000, 15000, 10000, 30000, 15000),
(13, 'iPhone 12 Pro', 120000, 150000, 10000, 20000, 15000, 40000, 20000),
(14, 'iPhone 12 Pro Max', 140000, 185000, 10000, 25000, 20000, 50000, 20000),
(15, 'iPhone 13 Mini', 140000, 145000, 10000, 25000, 20000, 40000, 15000),
(16, 'iPhone 13', 150000, 175000, 10000, 30000, 20000, 50000, 20000),
(17, 'iPhone 13 Pro', 180000, 220000, 10000, 55000, 30000, 70000, 20000),
(18, 'iPhone 13 Pro Max', 210000, 240000, 10000, 70000, 35000, 80000, 20000),
(19, 'iPhone 14', 200000, 230000, 10000, 30000, 30000, 60000, 20000),
(20, 'iPhone 14 Plus', 220000, 340000, 10000, 0, 0, 0, 20000),
(21, 'iPhone 14 Pro', 280000, 300000, 10000, 90000, 40000, 120000, 20000),
(22, 'iPhone 14 Pro Max', 300000, 320000, 10000, 100000, 50000, 120000, 20000),
(23, 'iPhone 15', 280000, 310000, 0, 0, 0, 0, 0),
(24, 'iPhone 15 Pro', 390000, 400000, 0, 0, 0, 0, 0),
(25, 'iPhone 15 Pro Max', 430000, 460000, 0, 0, 0, 0, 0),
(26, 'iPhone 16', 380000, 470000, 0, 0, 0, 0, 0),
(27, 'iPhone 16 Pro', 530000, 570000, 0, 0, 0, 0, 0),
(28, 'iPhone 16 Pro Max', 580000, 650000, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nom_complet` varchar(100) NOT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nom_complet`, `whatsapp`, `email`, `role`, `created_at`) VALUES
(1, 'kone', '$2y$10$XsUnlY4Le0cq8G2YbCpfHeM8yLSHBpkXqobKfHXKo7Uuiyt.eX/YS', 'KONE ABDOUL', '0103119339', '', 'admin', '2025-06-25 16:39:52'),
(2, 'dakouri', '$2y$10$jfiIkkcNL9yR5jXcY.VQxe4uMQN806KfbtBT241hLorHZR.EXJtMC', 'DAKOURI', '12322234', '', 'user', '2025-06-25 16:40:57'),
(3, 'malick', '$2y$10$KrCSk.1Cw.BAMZQT6G02s.nc7mMNnKzeGbDfPpy1y93v9mvQ0Aiw6', 'ABDOUL MAL', '0102940143', '', 'user', '2025-06-25 23:20:02'),
(4, 'azerty', '$2y$10$uJGBoj1uSg4n8py3nsmuO.eZzah/SuD3.NBB66zNRJ/d6UL2soVEu', 'TAY Z', '1361637', '', 'user', '2025-06-26 00:03:57');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
