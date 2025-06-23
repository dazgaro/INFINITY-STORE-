-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 23 juin 2025 à 10:04
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
-- Base de données : `site_inscription`
--

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `date_inscription` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `role` varchar(20) DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `username`, `email`, `phone`, `password`, `date_inscription`, `role`) VALUES
(3, 'kone', 'koneabdoulmalick0@gmail.com', '0102940143', '$2y$10$WBp7AsBu4Yc/8XCkDz7ipuwMSeQD9Ceg71ISIJdNxgNLQlWilt1H6', '2025-06-19 23:35:27', 'admin'),
(2, 'dakouri', 'koneabdoulmalick01@gmail.com', '0566696122', '$2y$10$BgYxN3q5g2vzUJRKhCjK8ekf8Omq62GQtNxRRdU.o31wDLBxH6U/e', '2025-06-19 14:24:16', 'user'),
(4, 'dakouri', 'koneabdoulmalick2@gmail.com', '0566696122', '$2y$10$RTjJNAKnHKA3Dk5gTcDH7eVJTiq5IAXL/gBRDwtJBuj5tOGcKNE6e', '2025-06-19 23:56:51', 'user'),
(5, 'junr', 'koneabdoulmalick22@gmail.com', '757646535254', '$2y$10$Qq6f66HJ/1rLxm4YScpqzu6oI22ph/bMVaYvFIZHIoBAC/wF69RrK', '2025-06-22 21:32:12', 'user'),
(6, 'azerty', 'koneabdoulmalick022@gmail.com', '3142453556', '$2y$10$bCs6HAt/cqmbj0W66cBZDOsc4Ap/Eiy0dPNnZ7FBdz7tA9nt2SAla', '2025-06-22 21:35:40', 'user'),
(7, 'asze', 'koneabdoulmalick222@gmail.com', '614351434126', '$2y$10$npZr25H0MIl..fb5XXkfm.tWdqdR7Q2yNj2fW3kLZVp3WH8kLZhC.', '2025-06-22 21:47:41', 'user'),
(8, 'dakouri', 'koneabdoulmalick20@gmail.com', '314245354', '$2y$10$W2TKB0mw0TGR70Yl6HehiulTAttVWSx52C9hTDvYzyRtBi4a3nKi2', '2025-06-23 09:51:37', 'user');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
