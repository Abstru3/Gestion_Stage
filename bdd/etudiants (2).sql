-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Mar 11 Février 2025 à 08:27
-- Version du serveur :  5.7.11
-- Version de PHP :  7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `stage_management`
--

-- --------------------------------------------------------

--
-- Structure de la table `etudiants`
--

CREATE TABLE `etudiants` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `code_postal` varchar(10) DEFAULT NULL,
  `pays` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `niveau_etude` varchar(50) DEFAULT NULL,
  `filiere` varchar(100) DEFAULT NULL,
  `cv` varchar(255) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('etudiant','entreprise','admin') NOT NULL DEFAULT 'etudiant',
  `lettre_motivation` text,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `icone` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `etudiants`
--

INSERT INTO `etudiants` (`id`, `nom`, `prenom`, `date_naissance`, `adresse`, `ville`, `code_postal`, `pays`, `telephone`, `niveau_etude`, `filiere`, `cv`, `username`, `email`, `password`, `role`, `lettre_motivation`, `date_creation`, `icone`) VALUES
(1, 'Koraqi', 'Besjan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'f585', 'aqii@gmail.com', '$2y$10$QUo31Cnq3k5YPDzaLjGyn.z1D9rWD5DalNBYzSibwyDMz7EiLnvYq', 'etudiant', NULL, '2025-02-06 09:29:04', NULL),
(2, 'Koraqi', 'Besjan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 'raqii@gmail.com', '$2y$10$QUo31Cnq3k5YPDzaLjGyn.z1D9rWD5DalNBYzSibwyDMz7EiLnvYq', 'admin', NULL, '2025-02-06 09:29:04', NULL),
(3, 'Koraqi', 'Besjan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'f', 'i@gmail.com', '$2y$10$X37ad9GrOp8thMeGE49EU.lX4IZXdegAILB0k3nEeWxUcEKCnRFie', 'etudiant', NULL, '2025-02-06 09:53:40', NULL),
(4, 'Henriot', 'Léo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Administrateur', 'admin@admin.fr', '$2y$10$X4QuNJya8RiQO8Dyt/eMFubDEILmzXM0.tcZeirRBtOE3TFPUQezu', 'admin', NULL, '2025-02-06 13:31:37', NULL),
(5, 'Amo', 'Gus', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Amogus', 'amog.us@mail.fr', '$2y$10$gMeexfysIdkjRsXHwwq7EO/SKReE3e97FKePpQOPTTSztG3Ykak96', 'etudiant', NULL, '2025-02-06 13:45:13', NULL);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `etudiants`
--
ALTER TABLE `etudiants`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `etudiants`
--
ALTER TABLE `etudiants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
