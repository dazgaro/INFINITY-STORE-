-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 23 juin 2025 à 10:03
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
-- Base de données : `base`
--

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
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`),
  KEY `idx_model` (`model`)
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `phone_prices`
--

INSERT INTO `phone_prices` (`id`, `type`, `model`, `storage`, `price`) VALUES
(3, 'neuf', 'iPhone 16 Pro Max', '256g', 785.00),
(4, 'neuf', 'iPhone 16 Pro', '256g', 685.00),
(5, 'neuf', 'iPhone 16 Pro', '128g', 675.00),
(6, 'neuf', 'iPhone 16 Plus', '256g', 665.00),
(7, 'neuf', 'iPhone 15 Pro Max', '512g', 635.00),
(8, 'neuf', 'iPhone 15 Pro Max', '256g', 615.00),
(9, 'neuf', 'iPhone 16', '256g', 575.00),
(10, 'neuf', 'iPhone 15 Pro', '256g', 555.00),
(11, 'neuf', 'iPhone 16 Plus', '128g', 550.00),
(12, 'neuf', 'iPhone 15 Pro', '128g', 515.00),
(13, 'neuf', 'iPhone 16', '128g', 495.00),
(14, 'neuf', 'iPhone 14 Pro Max', '256g', 485.00),
(15, 'neuf', 'iPhone 14 Pro Max', '128g', 460.00),
(16, 'neuf', 'iPhone 15', '256g', 455.00),
(17, 'neuf', 'iPhone 14 Pro', '256g', 445.00),
(18, 'neuf', 'iPhone 15', '128g', 395.00),
(19, 'neuf', 'iPhone 14 Pro', '128g', 385.00),
(20, 'neuf', 'iPhone 13 Pro Max', '256g', 375.00),
(21, 'neuf', 'iPhone 13 Pro Max', '128g', 350.00),
(22, 'neuf', 'iPhone 14', '256g', 325.00),
(23, 'neuf', 'iPhone 14', '128g', 295.00),
(24, 'neuf', 'iPhone 13 Pro', '128g', 290.00),
(25, 'neuf', 'iPhone 12 Pro Max', '128g', 285.00),
(26, 'neuf', 'iPhone 12 Pro', '128g', 245.00),
(27, 'neuf', 'iPhone 13', '128g', 245.00),
(28, 'neuf', 'iPhone 12', '64g', 175.00),
(29, 'neuf', 'iPhone 11', '128g', 150.00),
(30, 'neuf', 'iPhone 11', '64g', 140.00),
(31, 'neuf', 'iPhone XR', '128g', 125.00),
(32, 'neuf', 'iPhone XR', '64g', 120.00),
(33, 'neuf', 'iPhone X', '64g', 100.00),
(34, 'quasi', 'iPhone 15 Pro Max', '256g', 565.00),
(35, 'quasi', 'iPhone 14 Pro Max', '128g', 415.00),
(36, 'quasi', 'iPhone 14 Pro', '128g', 370.00),
(37, 'quasi', 'iPhone 13 Pro Max', '256g', 310.00),
(38, 'quasi', 'iPhone 13 Pro Max', '128g', 300.00),
(39, 'quasi', 'iPhone 13 Pro', '256g', 280.00),
(40, 'quasi', 'iPhone 13 Pro', '128g', 260.00),
(41, 'quasi', 'iPhone 12 Pro Max', '128g', 260.00),
(42, 'quasi', 'iPhone 13', '128g', 195.00),
(43, 'quasi', 'iPhone 12 Pro', '128g', 185.00),
(44, 'quasi', 'iPhone 13 Mini', '128g', 180.00),
(45, 'quasi', 'iPhone 11 Pro', '64g', 150.00),
(46, 'quasi', 'iPhone 12', '64g', 150.00),
(47, 'quasi', 'iPhone 11', '64g', 125.00),
(48, 'quasi', 'iPhone 12 Mini', '64g', 125.00),
(49, 'quasi', 'iPhone XR', '64g', 105.00),
(50, 'quasi', 'iPhone X', '64g', 90.00);

-- --------------------------------------------------------

--
-- Structure de la table `submissions`
--

DROP TABLE IF EXISTS `submissions`;
CREATE TABLE IF NOT EXISTS `submissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `form_type` enum('achat','troc','vente') NOT NULL,
  `nom` varchar(100) NOT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `data` json NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `submissions`
--

INSERT INTO `submissions` (`id`, `form_type`, `nom`, `whatsapp`, `email`, `data`, `ip`, `user_agent`, `created_at`) VALUES
(1, 'achat', 'KONE', '0103119339', 'koneabdoulmalick2@gmail.com', '{\"details\": {\"type\": \"neuf\", \"modele\": \"iPhone 16 Pro|128g\", \"commentaire\": \"\"}, \"metadata\": {\"ip\": \"::1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36\"}}', NULL, NULL, '2025-06-22 14:34:27'),
(2, 'troc', 'DAKOURI', '06238364883', 'koneabdoulmalick2@gmail.com', '{\"details\": {\"new_type\": \"neuf\", \"old_model\": \"iPhone 11 Pro Max\", \"conditions\": [\"no_box\", \"screen_issue\", \"battery_issue\", \"no_id\", \"rear_issue\"], \"new_modele\": \"iPhone 16 Pro|256g\", \"old_storage\": \"superieur\"}, \"metadata\": {\"ip\": \"::1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36\"}}', NULL, NULL, '2025-06-22 14:36:32'),
(3, 'vente', 'JUNE', '223273282', 'koneabdoulmalick2@gmail.com', '{\"details\": {\"model\": \"IPHONE 15\", \"has_box\": \"oui\", \"storage\": \"128\", \"other_info\": \"\", \"functionality\": \"aucune\", \"body_condition\": \"propre\", \"has_accessories\": \"non\", \"screen_condition\": \"INTACT\", \"battery_condition\": \"CASSE\", \"accessories_details\": \"\"}, \"metadata\": {\"ip\": \"::1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36\"}}', NULL, NULL, '2025-06-22 14:37:44'),
(4, 'achat', 'ABDOUL', '12322234', 'koneabdoulmalick2@gmail.com', '{\"details\": {\"type\": \"neuf\", \"modele\": \"iPhone 15 Pro Max|256g\", \"commentaire\": \"DDGDFGFGFG\"}, \"metadata\": {\"ip\": \"::1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36\"}}', NULL, NULL, '2025-06-22 21:48:38'),
(5, 'troc', 'DAJS', '361461', 'koneabdoulmalick2@gmail.com', '{\"details\": {\"new_type\": \"neuf\", \"old_model\": \"iPhone 11\", \"conditions\": [\"no_box\", \"screen_issue\", \"battery_issue\", \"no_id\"], \"new_modele\": \"iPhone 13|128g\", \"old_storage\": \"superieur\"}, \"metadata\": {\"ip\": \"::1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36\"}}', NULL, NULL, '2025-06-22 21:49:40');

-- --------------------------------------------------------

--
-- Structure de la table `trade_in_values`
--

DROP TABLE IF EXISTS `trade_in_values`;
CREATE TABLE IF NOT EXISTS `trade_in_values` (
  `id` int NOT NULL AUTO_INCREMENT,
  `model` varchar(50) NOT NULL,
  `base_value` decimal(10,2) NOT NULL,
  `superior_value` decimal(10,2) NOT NULL,
  `deduction_no_box` decimal(10,2) DEFAULT '0.00',
  `deduction_screen_issue` decimal(10,2) DEFAULT '0.00',
  `deduction_battery_issue` decimal(10,2) DEFAULT '0.00',
  `deduction_no_id` decimal(10,2) DEFAULT '0.00',
  `deduction_rear_issue` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `idx_model` (`model`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `trade_in_values`
--

INSERT INTO `trade_in_values` (`id`, `model`, `base_value`, `superior_value`, `deduction_no_box`, `deduction_screen_issue`, `deduction_battery_issue`, `deduction_no_id`, `deduction_rear_issue`) VALUES
(4, 'iPhone 7 Plus', 30.00, 35.00, 5.00, 10.00, 5.00, 0.00, 10.00),
(3, 'iPhone 7', 20.00, 25.00, 5.00, 5.00, 5.00, 0.00, 5.00),
(5, 'iPhone 8', 30.00, 40.00, 5.00, 5.00, 5.00, 0.00, 10.00),
(6, 'iPhone X', 45.00, 50.00, 5.00, 10.00, 5.00, 20.00, 10.00),
(7, 'iPhone XR', 60.00, 70.00, 5.00, 10.00, 10.00, 20.00, 10.00),
(8, 'iPhone XS', 50.00, 60.00, 5.00, 10.00, 5.00, 20.00, 10.00),
(9, 'iPhone XS Max', 65.00, 80.00, 5.00, 15.00, 10.00, 20.00, 10.00),
(10, 'iPhone 11', 85.00, 90.00, 5.00, 10.00, 10.00, 20.00, 10.00),
(11, 'iPhone 11 Pro', 95.00, 100.00, 5.00, 15.00, 10.00, 25.00, 10.00),
(12, 'iPhone 11 Pro Max', 100.00, 115.00, 5.00, 15.00, 10.00, 30.00, 10.00),
(13, 'iPhone 12 mini', 85.00, 105.00, 5.00, 15.00, 10.00, 30.00, 10.00),
(14, 'iPhone 12', 110.00, 115.00, 5.00, 15.00, 10.00, 30.00, 15.00),
(15, 'iPhone 12 Pro', 120.00, 150.00, 10.00, 20.00, 15.00, 40.00, 20.00),
(16, 'iPhone 12 Pro Max', 140.00, 185.00, 10.00, 25.00, 20.00, 50.00, 20.00),
(17, 'iPhone 13 Mini', 140.00, 145.00, 10.00, 25.00, 20.00, 40.00, 15.00),
(18, 'iPhone 13', 150.00, 175.00, 10.00, 30.00, 20.00, 50.00, 20.00),
(19, 'iPhone 13 Pro', 180.00, 220.00, 10.00, 55.00, 30.00, 70.00, 20.00),
(20, 'iPhone 13 Pro Max', 210.00, 240.00, 10.00, 70.00, 35.00, 80.00, 20.00),
(21, 'iPhone 14', 200.00, 230.00, 10.00, 30.00, 30.00, 60.00, 20.00),
(22, 'iPhone 14 Plus', 220.00, 340.00, 10.00, 0.00, 0.00, 0.00, 20.00),
(23, 'iPhone 14 Pro', 280.00, 300.00, 10.00, 90.00, 40.00, 120.00, 20.00),
(24, 'iPhone 14 Pro Max', 300.00, 320.00, 10.00, 100.00, 50.00, 120.00, 20.00),
(25, 'iPhone 15', 280.00, 310.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(26, 'iPhone 15 Pro', 390.00, 400.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(27, 'iPhone 15 Pro Max', 430.00, 460.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(28, 'iPhone 16', 380.00, 470.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(29, 'iPhone 16 Pro', 530.00, 570.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(30, 'iPhone 16 Pro Max', 580.00, 650.00, 0.00, 0.00, 0.00, 0.00, 0.00);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
