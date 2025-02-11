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
-- Structure de la table `entreprises`
--

CREATE TABLE `entreprises` (
  `id` int(11) NOT NULL,
  `siret` varchar(14) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `code_postal` varchar(10) DEFAULT NULL,
  `pays` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `site_web` varchar(255) DEFAULT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `tva_intracommunautaire` varchar(50) DEFAULT NULL,
  `secteur_activite` varchar(100) DEFAULT NULL,
  `description` text,
  `adresse_facturation` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `nom_contact` varchar(100) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('etudiant','entreprise','admin') NOT NULL DEFAULT 'entreprise',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `icone` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `entreprises`
--

INSERT INTO `entreprises` (`id`, `siret`, `nom`, `adresse`, `ville`, `code_postal`, `pays`, `telephone`, `site_web`, `lieu`, `tva_intracommunautaire`, `secteur_activite`, `description`, `adresse_facturation`, `logo`, `nom_contact`, `username`, `email`, `password`, `role`, `date_creation`, `icone`) VALUES
(2, '03215874859658', 'iutnervers', NULL, NULL, NULL, NULL, '0744813941', 'https://www.exemple.com', NULL, NULL, NULL, 'sssssssssssssssssssssssssssssssssssssss', '42 Rue Des Tailles, 58000 Nevers, France', NULL, 'Besjan Koraqi', 's', 'besjan.koraqii@gmail.com', '$2y$10$QyWJW6rO8Ir0n2TNeMszV.hGdVw307d6PCqflc.cSc6swQRpErKIO', 'entreprise', '2025-02-06 09:29:04', NULL),
(4, '02589632147895', 'Besjan Koraqi', NULL, NULL, NULL, NULL, '0144813944', 'https://www.exemple.com', NULL, 'undefined', NULL, 'sccccccccc', '42 Rue Des Tailles, 58000 Nevers, France', NULL, 'Besjan Koraqi', 'f888', 'qii@gmail.com', '$2y$10$5oDM1wrzjZQLDeh24rvgK.72MiDLEgXCadK.i.uMZR/IR0B4vF7By', 'entreprise', '2025-02-06 09:56:38', NULL),
(5, '61334717555763', 'Quizzine', NULL, NULL, NULL, NULL, '0622462417', 'https://quizzine.fr', NULL, '', NULL, 'Site de quiz en ligne interactif.', '666 rue de la Bite, 58000 Nevers', NULL, 'Henriot Léo', 'Quizzine', 'mail.contact@quizzine.fr', '$2y$10$3ELy3w44yZSThB61P1ficOM0l0Mfto32ox4izy/g1PwJEwp.lkf/2', 'entreprise', '2025-02-06 13:39:01', NULL);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `entreprises`
--
ALTER TABLE `entreprises`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `siret` (`siret`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `entreprises`
--
ALTER TABLE `entreprises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
