-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Jeu 13 Février 2025 à 13:42
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
-- Structure de la table `offres_stages`
--

CREATE TABLE `offres_stages` (
  `id` int(11) NOT NULL,
  `entreprise_id` int(11) DEFAULT NULL,
  `titre` varchar(100) NOT NULL,
  `description` text,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `mode_stage` enum('distanciel','présentiel') NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `email_contact` varchar(255) NOT NULL,
  `lien_candidature` varchar(255) DEFAULT NULL,
  `domaine` varchar(100) DEFAULT NULL,
  `remuneration` decimal(10,2) DEFAULT NULL,
  `pays` varchar(100) DEFAULT 'France',
  `ville` varchar(100) DEFAULT NULL,
  `code_postal` varchar(10) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `departement` varchar(100) DEFAULT NULL,
  `date_publication` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `offres_stages`
--

INSERT INTO `offres_stages` (`id`, `entreprise_id`, `titre`, `description`, `date_debut`, `date_fin`, `lieu`, `mode_stage`, `logo`, `email_contact`, `lien_candidature`, `domaine`, `remuneration`, `pays`, `ville`, `code_postal`, `region`, `departement`, `date_publication`) VALUES
(30, 2, 'Stage numéro 1 en informatique', 'juuuullllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllll', '2025-02-13', '2025-02-28', '', 'présentiel', 'uploads/logos/202303-7-1.jpg', 'besjan.koraqii@gmail.com', '', 'developpement_web', '500.00', 'France', 'Chenôve', '58000', 'Bourgogne-Franche-Comté', '21', '2025-02-11 13:27:43'),
(31, 4, 'Stage numéro 6 en informatique', 'aaa', '2025-02-06', '2026-12-25', 'nevers , 45 rue des boloss', 'distanciel', 'wallpaper_1692838578503ebbe9e24db4d76843d4ec1cf8176c.jpeg', '', NULL, NULL, NULL, 'France', NULL, NULL, NULL, NULL, '2025-02-11 13:27:43'),
(32, 5, 'Améliorations Front-End', 'Il s\'agirait d\'améliorer les fonctionnalités non finis en front end pour un design pour simple.', '2025-02-15', '2025-05-02', 'QG Quizzine', 'présentiel', 'uploads/logos/logo_quizzine_petit.png', '', NULL, NULL, NULL, 'France', NULL, NULL, NULL, NULL, '2025-02-11 13:27:43'),
(33, 5, 'Entretient du Back-End', 'Gérer l\'entretient du back-end durant', '2025-03-20', '2025-06-18', '', 'distanciel', 'uploads/logos/logo_quizzine_petit.png', '', NULL, NULL, NULL, 'France', NULL, NULL, NULL, NULL, '2025-02-11 13:27:43'),
(34, 2, 'Stage numéro 4 en informatique', 'Le meilleur je vous dit', '2025-02-12', '2025-08-01', '', 'distanciel', 'profile-image-exemple.png', 'qwsxdcfvgbhqii@gmail.com', '', 'logistique', '417.00', 'France', 'Clamecy', '58000', 'Bourgogne-Franche-Comté', '58', '2025-02-11 13:27:43'),
(35, 2, 'Stage numéro 1 en informatique', 'blablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblal', '2025-02-14', NULL, '', 'présentiel', 'Capture d\'écran 2025-01-29 093508.png', 'besjan.koraqii@gmail.com', '', 'developpement_web', '500.00', 'France', 'Nevers', '58000', 'Bourgogne-Franche-Comté', '58', '2025-02-12 09:38:25'),
(36, 2, 'Le stage a besjan  2', 'blablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablablablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablabla', '2025-02-13', NULL, '', 'présentiel', 'Capture d\'écran 2025-01-29 093508.png', 'besjan.koraqii@gmail.com', '', 'commerce_international', '417.00', 'France', 'Dijon', '58000', 'Bourgogne-Franche-Comté', '21', '2025-02-12 14:44:50');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `offres_stages`
--
ALTER TABLE `offres_stages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entreprise_id` (`entreprise_id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `offres_stages`
--
ALTER TABLE `offres_stages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `offres_stages`
--
ALTER TABLE `offres_stages`
  ADD CONSTRAINT `offres_stages_ibfk_1` FOREIGN KEY (`entreprise_id`) REFERENCES `entreprises` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
