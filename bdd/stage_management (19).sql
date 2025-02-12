-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Mer 12 Février 2025 à 14:48
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
-- Structure de la table `candidatures`
--

CREATE TABLE `candidatures` (
  `id` int(11) NOT NULL,
  `etudiant_id` int(11) DEFAULT NULL,
  `offre_id` int(11) DEFAULT NULL,
  `statut` enum('en_attente','acceptee','refusee') DEFAULT 'en_attente',
  `date_candidature` datetime DEFAULT CURRENT_TIMESTAMP,
  `cv` varchar(255) NOT NULL,
  `lettre_motivation` varchar(255) NOT NULL,
  `date_modification` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_lecture` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `candidatures`
--

INSERT INTO `candidatures` (`id`, `etudiant_id`, `offre_id`, `statut`, `date_candidature`, `cv`, `lettre_motivation`, `date_modification`, `date_lecture`) VALUES
(3, 1, 30, 'acceptee', '2025-02-05 15:05:22', '67a37022928cc.pdf', '67a3702292f3b.pdf', '2025-02-12 14:18:00', '2025-02-12 14:18:00'),
(4, 5, 32, 'acceptee', '2025-02-06 14:47:56', '67a4bd8c3e8aa.pdf', '67a4bd8c3f09a.pdf', '2025-02-12 11:50:43', '2025-02-12 11:50:43'),
(5, 5, 33, 'acceptee', '2025-02-07 09:36:51', '67a5c62396937.pdf', '67a5c62396d5a.pdf', '2025-02-12 11:50:02', '2025-02-12 11:50:02'),
(6, 6, 32, 'refusee', '2025-02-11 09:36:22', '67ab0c069b15a.pdf', '67ab0c069b856.pdf', '2025-02-12 11:17:29', '2025-02-12 11:17:29'),
(7, 7, 32, 'en_attente', '2025-02-11 09:43:33', '67ab0db579039.pdf', '67ab0db579a84.pdf', '2025-02-12 10:58:23', '2025-02-12 10:56:08'),
(8, 8, 31, 'en_attente', '2025-02-11 19:34:47', '67ab984784244.pdf', '67ab984784909.pdf', '2025-02-12 11:16:51', '2025-02-12 11:16:51'),
(9, 8, 32, 'acceptee', '2025-02-11 19:35:09', '67ab985d1eada.pdf', '67ab985d1eebe.pdf', '2025-02-12 11:16:51', '2025-02-12 11:16:51'),
(10, 9, 33, 'refusee', '2025-02-11 19:39:17', '67ab995549559.pdf', '67ab9955498d9.pdf', '2025-02-12 11:15:32', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `competences`
--

CREATE TABLE `competences` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
(2, '03215874859658', 'iutnervers', NULL, NULL, NULL, NULL, '0744813941', 'https://www.exemple.com', NULL, NULL, NULL, 'sssssssssssssssssssssssssssssssssssssss', '42 Rue Des Tailles, 58000 Nevers, France', NULL, 'Besjan Koraqi', 's', 'besjan.koraqii@gmail.com', '$2y$10$QyWJW6rO8Ir0n2TNeMszV.hGdVw307d6PCqflc.cSc6swQRpErKIO', 'entreprise', '2025-02-06 09:29:04', '67acb38ba5271.jpg'),
(4, '02589632147895', 'Besjan Koraqi', NULL, NULL, NULL, NULL, '0144813944', 'https://www.exemple.com', NULL, 'undefined', NULL, 'sccccccccc', '42 Rue Des Tailles, 58000 Nevers, France', NULL, 'Besjan Koraqi', 'f888', 'qii@gmail.com', '$2y$10$5oDM1wrzjZQLDeh24rvgK.72MiDLEgXCadK.i.uMZR/IR0B4vF7By', 'entreprise', '2025-02-06 09:56:38', NULL),
(5, '61334717555763', 'Quizzine', NULL, NULL, NULL, NULL, '0622462417', 'https://quizzine.fr', NULL, '', NULL, 'Site de quiz en ligne interactif.', '666 rue de la Bite, 58000 Nevers', NULL, 'Henriot Léo', 'Quizzine', 'mail.contact@quizzine.fr', '$2y$10$3ELy3w44yZSThB61P1ficOM0l0Mfto32ox4izy/g1PwJEwp.lkf/2', 'entreprise', '2025-02-06 13:39:01', NULL);

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

-- --------------------------------------------------------

--
-- Structure de la table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `role` enum('etudiant','entreprise','admin') NOT NULL,
  `feedback` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `feedback`
--

INSERT INTO `feedback` (`id`, `user_name`, `role`, `feedback`, `created_at`, `date`) VALUES
(11, 'Administrateur', 'admin', 'Avis d\'administrateur 1', '2025-02-07 14:48:38', '2025-02-07 14:48:38'),
(12, 'Administrateur', 'admin', 'Avis d\'administrateur 2', '2025-02-07 14:49:15', '2025-02-07 14:49:15'),
(13, 'Administrateur', 'admin', 'Avis d\'administrateur 3', '2025-02-07 14:49:18', '2025-02-07 14:49:18'),
(14, 'Quizzine', 'entreprise', 'Avis d\'entreprise 1', '2025-02-07 14:49:54', '2025-02-07 14:49:54'),
(15, 'Quizzine', 'entreprise', 'Avis d\'entreprise 2', '2025-02-07 14:49:56', '2025-02-07 14:49:56'),
(16, 'Quizzine', 'entreprise', 'Avis d\'entreprise 3', '2025-02-07 14:49:59', '2025-02-07 14:49:59'),
(17, 'Amo Gus', 'etudiant', 'Avis d\'étudiant 1', '2025-02-07 14:50:18', '2025-02-07 14:50:18'),
(18, 'Amo Gus', 'etudiant', 'Avis d\'étudiant 2', '2025-02-07 14:50:22', '2025-02-07 14:50:22'),
(19, 'Amo Gus', 'etudiant', 'Avis d\'étudiant 3', '2025-02-07 14:50:24', '2025-02-07 14:50:24'),
(20, 'iutnervers', 'entreprise', 'J\'aime bien', '2025-02-10 10:29:21', '2025-02-10 10:29:21');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `expediteur_id` varchar(20) DEFAULT NULL,
  `destinataire_id` varchar(20) DEFAULT NULL,
  `contenu` text NOT NULL,
  `date_envoi` datetime DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('non_lu','lu') DEFAULT 'non_lu',
  `conversation_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `messages`
--

INSERT INTO `messages` (`id`, `expediteur_id`, `destinataire_id`, `contenu`, `date_envoi`, `statut`, `conversation_id`) VALUES
(95, '1', '2', 'sss', '2025-02-11 16:21:57', 'non_lu', 1),
(96, 'E1', 'C2', 'cscded', '2025-02-11 16:25:50', 'lu', 2),
(97, 'C2', 'E1', '"e"r"r', '2025-02-11 16:26:28', 'lu', 2),
(98, 'C2', 'E1', 'tgrfhgy', '2025-02-12 10:39:08', 'lu', 2),
(99, 'C2', 'E1', 'thfhtfh', '2025-02-12 10:39:10', 'lu', 2),
(100, 'C2', 'E1', 'hfth', '2025-02-12 10:39:11', 'lu', 2),
(101, 'C2', 'E3', 'fthfthf', '2025-02-12 10:39:13', 'non_lu', 3),
(102, 'C2', 'E3', 'fhfth', '2025-02-12 10:39:14', 'non_lu', 3),
(103, 'C2', 'E3', 'fthf', '2025-02-12 10:39:16', 'non_lu', 3),
(104, 'C2', 'E1', 'sa va mon gatéé', '2025-02-12 14:17:33', 'lu', 2);

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `etudiant_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `date_notification` datetime DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('non_lu','lu') DEFAULT 'non_lu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `notifications`
--

INSERT INTO `notifications` (`id`, `etudiant_id`, `message`, `date_notification`, `statut`) VALUES
(1, 1, 'Vous avez reçu un nouveau message d\'une entreprise.', '2025-02-06 11:26:26', 'non_lu'),
(2, 1, 'Vous avez reçu un nouveau message d\'une entreprise.', '2025-02-06 11:40:48', 'non_lu'),
(3, 5, 'Vous avez reçu un nouveau message d\'une entreprise.', '2025-02-06 14:51:26', 'non_lu');

-- --------------------------------------------------------

--
-- Structure de la table `offres_competences`
--

CREATE TABLE `offres_competences` (
  `offre_id` int(11) NOT NULL,
  `competence_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `duree` varchar(50) NOT NULL,
  `domaine` varchar(100) DEFAULT NULL,
  `remuneration` decimal(10,2) DEFAULT NULL,
  `teletravail` tinyint(1) DEFAULT '0',
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

INSERT INTO `offres_stages` (`id`, `entreprise_id`, `titre`, `description`, `date_debut`, `date_fin`, `lieu`, `mode_stage`, `logo`, `email_contact`, `lien_candidature`, `duree`, `domaine`, `remuneration`, `teletravail`, `pays`, `ville`, `code_postal`, `region`, `departement`, `date_publication`) VALUES
(30, 2, 'Stage numéro 1 en informatique', 'juuuullllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllll', '2025-02-13', '2025-02-28', '', 'présentiel', 'uploads/logos/202303-7-1.jpg', 'besjan.koraqii@gmail.com', '', '3 mois', 'developpement_web', '500.00', 1, 'France', 'Chenôve', '58000', 'Bourgogne-Franche-Comté', '21', '2025-02-11 13:27:43'),
(31, 4, 'Stage numéro 6 en informatique', 'aaa', '2025-02-06', '2026-12-25', 'nevers , 45 rue des boloss', 'distanciel', NULL, '', NULL, '', NULL, NULL, 0, 'France', NULL, NULL, NULL, NULL, '2025-02-11 13:27:43'),
(32, 5, 'Améliorations Front-End', 'Il s\'agirait d\'améliorer les fonctionnalités non finis en front end pour un design pour simple.', '2025-02-15', '2025-05-02', 'QG Quizzine', 'présentiel', 'uploads/logos/logo_quizzine_petit.png', '', NULL, '', NULL, NULL, 0, 'France', NULL, NULL, NULL, NULL, '2025-02-11 13:27:43'),
(33, 5, 'Entretient du Back-End', 'Gérer l\'entretient du back-end durant', '2025-03-20', '2025-06-18', '', 'distanciel', 'uploads/logos/logo_quizzine_petit.png', '', NULL, '', NULL, NULL, 0, 'France', NULL, NULL, NULL, NULL, '2025-02-11 13:27:43'),
(34, 2, 'Stage numéro 4 en informatique', 'Le meilleur je vous dit', '2025-02-12', '2025-08-01', '', 'distanciel', NULL, 'qwsxdcfvgbhqii@gmail.com', '', '2 mois', 'logistique', '417.00', 1, 'France', 'Clamecy', '58000', 'Bourgogne-Franche-Comté', '58', '2025-02-11 13:27:43'),
(35, 2, 'Stage numéro 1 en informatique', 'blablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblal', '2025-02-14', NULL, '', 'présentiel', NULL, 'besjan.koraqii@gmail.com', '', '2 mois', 'developpement_web', '500.00', 1, 'France', 'Nevers', '58000', 'Bourgogne-Franche-Comté', '58', '2025-02-12 09:38:25'),
(36, 2, 'Le stage a besjan  2', 'blablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablablablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablabla', '2025-02-13', NULL, '', 'présentiel', NULL, 'besjan.koraqii@gmail.com', '', '2 mois', 'commerce_international', '417.00', 1, 'France', 'Dijon', '58000', 'Bourgogne-Franche-Comté', '21', '2025-02-12 14:44:50');

-- --------------------------------------------------------

--
-- Structure de la table `stages`
--

CREATE TABLE `stages` (
  `id` int(11) NOT NULL,
  `etudiant_id` int(11) NOT NULL,
  `offre_id` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `statut` enum('en_cours','terminé','annulé') DEFAULT 'en_cours'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `candidatures`
--
ALTER TABLE `candidatures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `etudiant_id` (`etudiant_id`,`offre_id`),
  ADD KEY `offre_id` (`offre_id`);

--
-- Index pour la table `competences`
--
ALTER TABLE `competences`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `entreprises`
--
ALTER TABLE `entreprises`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `siret` (`siret`);

--
-- Index pour la table `etudiants`
--
ALTER TABLE `etudiants`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expediteur_id` (`expediteur_id`),
  ADD KEY `destinataire_id` (`destinataire_id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_fk` (`etudiant_id`);

--
-- Index pour la table `offres_competences`
--
ALTER TABLE `offres_competences`
  ADD PRIMARY KEY (`offre_id`,`competence_id`),
  ADD KEY `competence_id` (`competence_id`);

--
-- Index pour la table `offres_stages`
--
ALTER TABLE `offres_stages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entreprise_id` (`entreprise_id`);

--
-- Index pour la table `stages`
--
ALTER TABLE `stages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `etudiant_id` (`etudiant_id`),
  ADD KEY `offre_id` (`offre_id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `candidatures`
--
ALTER TABLE `candidatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT pour la table `competences`
--
ALTER TABLE `competences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `entreprises`
--
ALTER TABLE `entreprises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `etudiants`
--
ALTER TABLE `etudiants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;
--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `offres_stages`
--
ALTER TABLE `offres_stages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT pour la table `stages`
--
ALTER TABLE `stages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_fk` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `offres_competences`
--
ALTER TABLE `offres_competences`
  ADD CONSTRAINT `offres_competences_ibfk_1` FOREIGN KEY (`offre_id`) REFERENCES `offres_stages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `offres_competences_ibfk_2` FOREIGN KEY (`competence_id`) REFERENCES `competences` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `offres_stages`
--
ALTER TABLE `offres_stages`
  ADD CONSTRAINT `offres_stages_ibfk_1` FOREIGN KEY (`entreprise_id`) REFERENCES `entreprises` (`id`);

--
-- Contraintes pour la table `stages`
--
ALTER TABLE `stages`
  ADD CONSTRAINT `stages_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id`),
  ADD CONSTRAINT `stages_ibfk_2` FOREIGN KEY (`offre_id`) REFERENCES `offres_stages` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
