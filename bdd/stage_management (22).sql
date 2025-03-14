-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Jeu 13 Mars 2025 à 14:51
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
(7, 7, 32, 'acceptee', '2025-02-11 09:43:33', '67ab0db579039.pdf', '67ab0db579a84.pdf', '2025-02-17 15:04:57', '2025-02-12 10:56:08'),
(8, 8, 31, 'en_attente', '2025-02-11 19:34:47', '67ab984784244.pdf', '67ab984784909.pdf', '2025-02-17 15:05:31', '2025-02-12 11:16:51'),
(9, 8, 32, 'acceptee', '2025-02-11 19:35:09', '67ab985d1eada.pdf', '67ab985d1eebe.pdf', '2025-02-17 15:05:27', '2025-02-12 11:16:51'),
(10, 9, 33, 'refusee', '2025-02-11 19:39:17', '67ab995549559.pdf', '67ab9955498d9.pdf', '2025-02-12 11:15:32', NULL),
(11, 1, 31, 'acceptee', '2025-02-17 15:02:39', '67b3417f1a4a8.pdf', '67b3417f1ab77.pdf', '2025-02-18 16:53:12', '2025-02-18 16:53:12');

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
  `icone` varchar(255) DEFAULT NULL,
  `valide` tinyint(1) DEFAULT '0',
  `certification` tinyint(1) DEFAULT '0',
  `theme_color` varchar(7) DEFAULT '#3498db',
  `bloque` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `entreprises`
--

INSERT INTO `entreprises` (`id`, `siret`, `nom`, `adresse`, `ville`, `code_postal`, `pays`, `telephone`, `site_web`, `lieu`, `tva_intracommunautaire`, `secteur_activite`, `description`, `adresse_facturation`, `logo`, `nom_contact`, `username`, `email`, `password`, `role`, `date_creation`, `icone`, `valide`, `certification`, `theme_color`, `bloque`) VALUES
(2, '03215874859658', 'iutnervers', NULL, NULL, NULL, NULL, '0744813941', 'https://www.exemple.com', NULL, NULL, NULL, 'sssssssssssssssssssssssssssssssssssssssgdshbjnk,lqjdhfgrydhsbujnqkIHDUYGFTHSIJQK?sdihufygrhsjiqk,OIUHDYFGSIJKQ?ZIUHDEYFGRDJUSKQ?DIUHYFGHSJQK', '42 Rue Des Tailles, 58000 Nevers, France', NULL, 'Besjan Koraqi', 's', 'besjan.koraqii@gmail.com', '$2y$10$b56AUHgu7FKeTYKIhJqpcOtgzggBVbUdBjN.hfGgz1uT7QS/JjUW.', 'entreprise', '2025-02-06 09:29:04', '67af4fbc13bce.png', 1, 0, '#3498db', 0),
(4, '02589632147895', 'Besjan Koraqi', NULL, NULL, NULL, NULL, '0144813944', 'https://www.exemple.com', NULL, 'undefined', NULL, 'sccccccccc', '42 Rue Des Tailles, 58000 Nevers, France', NULL, 'Besjan Koraqi', 'f888', 'qii@gmail.com', '$2y$10$b56AUHgu7FKeTYKIhJqpcOtgzggBVbUdBjN.hfGgz1uT7QS/JjUW.', 'entreprise', '2025-02-06 09:56:38', '', 1, 0, '#3498db', 0),
(5, '61334717555763', 'Quizzine', NULL, NULL, NULL, NULL, '0622462417', 'https://quizzine.fr', NULL, '', NULL, 'Site de quiz en ligne interactif.', '666 rue de la Bite, 58000 Nevers', NULL, 'Henriot Léo', 'Quizzine', 'mail.contact@quizzine.fr', '$2y$10$b56AUHgu7FKeTYKIhJqpcOtgzggBVbUdBjN.hfGgz1uT7QS/JjUW.', 'entreprise', '2025-02-06 13:39:01', NULL, 1, 0, '#3498db', 0),
(6, '03215874859651', 'bab job', NULL, NULL, NULL, NULL, '0785974561', 'https://www.exemple.com', NULL, 'undefined', NULL, 'sxdfrgthyjukilompolikujyhtgrfd', '48 rue des bolles', NULL, 'brad pitt', 'bla123', 'jhgfdsaqii@gmail.com', '$2y$10$hVpSJCq9Im/qGrNu3eHVs.4Rbk0V7cnDGFBsK5uSvgAUXpSO8Yh0a', 'entreprise', '2025-02-18 13:20:44', '67b4aac85aa80.png', 1, 0, '#3498db', 0),
(7, '03215874859640', 'Besjan Koraqi', NULL, NULL, NULL, NULL, '0615478965', '', NULL, NULL, NULL, 'blablablabla', '12 rue des perles', NULL, 'Mohammed', 'f111', 'babaoraqii@gmail.com', '$2y$10$ol8RuCKW53cHMGrpaEpVGugVIQCl1082cUHtwSFtvQHg76l5wX.9q', 'entreprise', '2025-03-10 13:24:51', '67cee87461b5e.png', 1, 0, '#3498db', 0);

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
  `icone` varchar(255) DEFAULT NULL,
  `bloque` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `etudiants`
--

INSERT INTO `etudiants` (`id`, `nom`, `prenom`, `date_naissance`, `adresse`, `ville`, `code_postal`, `pays`, `telephone`, `niveau_etude`, `filiere`, `cv`, `username`, `email`, `password`, `role`, `lettre_motivation`, `date_creation`, `icone`, `bloque`) VALUES
(1, 'Koraqi', 'Besjan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'f585', 'aqii@gmail.com', '$2y$10$HwSng.HP7/qC1nYPE/ooFO4lmukHv4Z/mm5CBxVhXSM4CXVsMXZDS', 'etudiant', NULL, '2025-02-06 09:29:04', '67acb93f2c6c4.jpg', 0),
(3, 'Koraqi', 'Besjan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'f', 'i@gmail.com', '$2y$10$X37ad9GrOp8thMeGE49EU.lX4IZXdegAILB0k3nEeWxUcEKCnRFie', 'etudiant', NULL, '2025-02-06 09:53:40', NULL, 0),
(4, 'Henriot', 'Léo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Administrateur', 'admin@admin.fr', '$2y$10$X4QuNJya8RiQO8Dyt/eMFubDEILmzXM0.tcZeirRBtOE3TFPUQezu', 'admin', NULL, '2025-02-06 13:31:37', NULL, 0),
(5, 'Amo', 'Gus', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Amogus', 'amog.us@mail.fr', '$2y$10$gMeexfysIdkjRsXHwwq7EO/SKReE3e97FKePpQOPTTSztG3Ykak96', 'etudiant', NULL, '2025-02-06 13:45:13', NULL, 0);

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
(104, 'C2', 'E1', 'sa va mon gatéé', '2025-02-12 14:17:33', 'lu', 2),
(105, 'C2', 'E1', 'ahhhhh', '2025-02-14 15:39:15', 'lu', 2),
(106, 'E1', 'C2', 'iefjiej', '2025-02-17 14:02:20', 'lu', 2),
(107, 'E5', 'C5', 'hfifjifj', '2025-02-17 14:20:03', 'non_lu', 4),
(108, 'C2', 'E1', 'zéée', '2025-02-18 16:58:59', 'lu', 2),
(109, 'C2', 'E1', 'zzz', '2025-02-18 17:01:23', 'lu', 2),
(110, 'C2', 'E1', 'z', '2025-02-18 17:03:11', 'lu', 2),
(111, 'E1', 'C2', 'zzz', '2025-02-18 17:03:33', 'lu', 2),
(112, 'E1', 'C4', 'zz', '2025-02-18 17:03:36', 'non_lu', 5),
(113, 'C2', 'E1', 'ertyu', '2025-02-18 17:07:19', 'lu', 2),
(114, 'E1', 'C2', 'rurju', '2025-02-18 17:07:42', 'lu', 2),
(115, 'C2', 'E1', 'saluutt', '2025-02-18 17:11:12', 'lu', 2),
(116, 'E1', 'C2', 'eeeeeeeeee', '2025-02-18 17:11:30', 'lu', 2),
(117, 'E1', 'C2', 'eeeeeeee', '2025-02-18 17:11:31', 'lu', 2),
(118, 'E1', 'C4', 'rrrrrrrr', '2025-02-18 17:11:37', 'non_lu', 5),
(119, 'C2', 'E1', 'szdefrgt', '2025-02-19 09:48:19', 'lu', 2),
(120, 'E1', 'C2', 'dfg', '2025-02-19 09:48:59', 'lu', 2),
(121, 'E1', 'C2', 'fdgfdg', '2025-02-19 09:49:02', 'lu', 2),
(122, 'E1', 'C2', 'dfgdgf', '2025-02-19 09:49:03', 'lu', 2),
(123, 'C2', 'E1', 'rtyuio', '2025-02-19 09:53:53', 'lu', 2),
(124, 'E1', 'C2', 'ertyui', '2025-02-19 09:58:13', 'lu', 2),
(125, 'C2', 'E1', 'dddddddddddddddddddddddddddd', '2025-02-19 09:58:34', 'lu', 2),
(126, 'C2', 'E1', 'gfg', '2025-02-19 10:25:19', 'lu', 2),
(127, 'C2', 'E1', 'bhhug', '2025-02-19 10:34:44', 'lu', 2),
(128, 'C2', 'E1', 'bhhug', '2025-02-19 10:34:44', 'lu', 2),
(129, 'C2', 'E1', 'sq', '2025-02-19 10:35:37', 'lu', 2),
(130, 'C2', 'E1', 'sq', '2025-02-19 10:35:37', 'lu', 2),
(131, 'C2', 'E1', 'zsedrftgh', '2025-02-19 10:35:41', 'lu', 2),
(132, 'C2', 'E1', 'zsedrftgh', '2025-02-19 10:35:41', 'lu', 2),
(133, 'C2', 'E1', 'zsedrftgh', '2025-02-19 10:35:41', 'lu', 2),
(134, 'C2', 'E1', 'zsedrftgh', '2025-02-19 10:35:41', 'lu', 2),
(135, 'C2', 'E1', 'gb', '2025-02-19 10:35:47', 'lu', 2),
(136, 'C2', 'E1', 'gb', '2025-02-19 10:35:47', 'lu', 2),
(137, 'C2', 'E1', 'gb', '2025-02-19 10:35:47', 'lu', 2),
(138, 'C2', 'E1', 'gb', '2025-02-19 10:35:47', 'lu', 2),
(139, 'C2', 'E1', 'gb', '2025-02-19 10:35:47', 'lu', 2),
(140, 'C2', 'E1', 'gb', '2025-02-19 10:35:47', 'lu', 2),
(141, 'C2', 'E1', 'gb', '2025-02-19 10:35:47', 'lu', 2),
(142, 'C2', 'E1', 'gb', '2025-02-19 10:35:47', 'lu', 2),
(143, 'C2', 'E1', 'iu', '2025-02-19 10:40:06', 'lu', 2),
(144, 'C2', 'E1', 'iu', '2025-02-19 10:40:06', 'lu', 2),
(145, 'C2', 'E1', 'fefef', '2025-02-19 10:40:48', 'lu', 2),
(146, 'C2', 'E1', 'fefef', '2025-02-19 10:40:48', 'lu', 2),
(147, 'C2', 'E1', 'efefe', '2025-02-19 10:40:59', 'lu', 2),
(148, 'E1', 'C2', 'efefsfesf', '2025-02-19 10:55:59', 'lu', 2),
(149, 'E1', 'C4', 'sesfe', '2025-02-19 10:56:01', 'non_lu', 5),
(150, 'E1', 'C2', 'sefsf', '2025-02-19 10:56:05', 'lu', 2),
(151, 'E1', 'C4', 'esfsef', '2025-02-19 10:56:08', 'non_lu', 5),
(152, 'C2', 'E1', 'v', '2025-02-19 10:56:30', 'lu', 2),
(153, 'C2', 'E1', 'vbc bien vu', '2025-02-19 10:56:48', 'lu', 2),
(154, 'C2', 'E1', 'vbc bien vu', '2025-02-19 10:56:48', 'lu', 2),
(155, 'C2', 'E1', 'hj', '2025-02-19 10:57:39', 'lu', 2),
(156, 'C2', 'E1', 'tryuiuo', '2025-02-19 11:03:25', 'lu', 2),
(157, 'C2', 'E1', 'tgft', '2025-02-19 11:03:32', 'lu', 2),
(158, 'E1', 'C2', 'fghj', '2025-02-19 11:09:02', 'lu', 2),
(159, 'E1', 'C2', 'fghj', '2025-02-19 11:09:02', 'lu', 2),
(160, 'C2', 'E1', 'dsfgh', '2025-02-19 11:10:06', 'lu', 2),
(161, 'E1', 'C2', 'frgthyj', '2025-02-19 11:12:12', 'lu', 2),
(162, 'E1', 'C2', 'dsdd', '2025-02-19 11:33:04', 'lu', 2),
(163, 'C2', 'E1', 'tyukiuo', '2025-02-19 11:41:49', 'lu', 2),
(164, 'C2', 'E1', 'tyukiuo', '2025-02-19 11:41:49', 'lu', 2),
(165, 'C2', 'E1', 'tyukiuo', '2025-02-19 11:41:49', 'lu', 2),
(166, 'C2', 'E1', 'tyukiuo', '2025-02-19 11:41:49', 'lu', 2);

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
  `domaine` varchar(100) DEFAULT NULL,
  `remuneration` decimal(10,2) DEFAULT NULL,
  `pays` varchar(100) DEFAULT 'France',
  `ville` varchar(100) DEFAULT NULL,
  `code_postal` varchar(10) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `departement` varchar(100) DEFAULT NULL,
  `date_publication` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pourvu` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `offres_stages`
--

INSERT INTO `offres_stages` (`id`, `entreprise_id`, `titre`, `description`, `date_debut`, `date_fin`, `lieu`, `mode_stage`, `logo`, `email_contact`, `lien_candidature`, `domaine`, `remuneration`, `pays`, `ville`, `code_postal`, `region`, `departement`, `date_publication`, `pourvu`) VALUES
(30, 2, 'Stage numéro 1 en informatique', 'juuuullllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllll', '2025-02-13', '2025-02-28', '', 'présentiel', 'uploads/logos/202303-7-1.jpg', 'besjan.koraqii@gmail.com', '', 'developpement_web', '500.00', 'France', 'Chenôve', '58000', 'Bourgogne-Franche-Comté', '21', '2025-02-11 13:27:43', 0),
(31, 4, 'Stage numéro 6 en informatique', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '2025-02-19', '2025-02-28', 'nevers , 45 rue des boloss', 'distanciel', 'wallpaper_1692838578503ebbe9e24db4d76843d4ec1cf8176c.jpeg', 'zdf.koraqii@gmail.com', '', 'developpement_mobile', '600.00', 'France', 'Decize', '58300', 'Bourgogne-Franche-Comté', '58', '2025-02-11 13:27:43', 0),
(32, 5, 'Améliorations Front-End', 'Il s\'agirait d\'améliorer les fonctionnalités non finis en front end pour un design pour simple.', '2025-02-15', '2025-05-02', 'QG Quizzine', 'présentiel', 'uploads/logos/logo_quizzine_petit.png', '', NULL, NULL, NULL, 'France', NULL, NULL, NULL, NULL, '2025-02-11 13:27:43', 0),
(33, 5, 'Entretient du Back-End', 'Gérer l\'entretient du back-end durant', '2025-03-20', '2025-06-18', '', 'distanciel', 'uploads/logos/logo_quizzine_petit.png', '', NULL, NULL, NULL, 'France', NULL, NULL, NULL, NULL, '2025-02-11 13:27:43', 0),
(34, 2, 'Stage numéro 4 en informatique', 'Le meilleur je vous dit', '2025-02-12', '2025-08-01', '', 'distanciel', 'profile-image-exemple.png', 'qwsxdcfvgbhqii@gmail.com', '', 'logistique', '417.00', 'France', 'Clamecy', '58000', 'Bourgogne-Franche-Comté', '58', '2025-02-11 13:27:43', 0),
(35, 2, 'Stage numéro 1 en informatique', 'blablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblal', '2025-02-14', NULL, '', 'présentiel', 'Capture d\'écran 2025-01-29 093508.png', 'besjan.koraqii@gmail.com', '', 'developpement_web', '500.00', 'France', 'Nevers', '58000', 'Bourgogne-Franche-Comté', '58', '2025-02-12 09:38:25', 0),
(36, 2, 'Le stage a besjan  2', 'blablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablablablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablablabbalbabblablababllbablalbabllbablblablballblablballblaalblalablblabllabblalblablabla', '2025-02-13', NULL, '', 'présentiel', 'Capture d\'écran 2025-01-29 093508.png', 'besjan.koraqii@gmail.com', '', 'commerce_international', '417.00', 'France', 'Dijon', '58000', 'Bourgogne-Franche-Comté', '21', '2025-02-12 14:44:50', 0),
(37, 2, 'Stage numéro 1 en informatique', '$sql = &amp;quot;INSERT INTO offres_stages (\r\n    entreprise_id, \r\n    titre, \r\n    description, \r\n    email_contact, \r\n    lien_candidature, \r\n    date_debut,\r\n    domaine, \r\n    remuneration, \r\n    pays, \r\n    ville, \r\n    code_postal, \r\n    region, \r\n    departement,\r\n    mode_stage\r\n) VALUES (\r\n    :entreprise_id, \r\n    :titre, \r\n    :description, \r\n    :email_contact, \r\n    :lien_candidature, \r\n    :date_debut,\r\n    :domaine, \r\n    :remuneration, \r\n    :pays, \r\n    :ville, \r\n    :code_postal, \r\n    :region, \r\n    :departement,\r\n    :mode_stage\r\n)&amp;quot;;\r\n', '2025-02-21', NULL, NULL, 'présentiel', 'uploads/logos/202303-7-1.jpg', 'besjan.koraqii@gmail.com', '', 'finance', '500.00', 'France', 'Nevers', '58000', 'Bourgogne-Franche-Comté', '58', '2025-02-14 09:48:34', 0),
(38, 2, 'Stage numéro 1 en informatique', '    &lt;div class=&quot;form-group&quot;&gt;\r\n        &lt;label for=&quot;date_debut&quot;&gt;Date de début*&lt;/label&gt;\r\n        &lt;input type=&quot;date&quot; id=&quot;date_debut&quot; name=&quot;date_debut&quot; required value=&quot;&lt;?= htmlspecialchars($_SESSION[\'form_data\'][\'date_debut\'] ?? \'\') ?&gt;&quot;&gt;\r\n    &lt;/div&gt;\r\n\r\n    &lt;div class=&quot;form-group&quot;&gt;\r\n        &lt;label for=&quot;domaine&quot;&gt;Domaine*&lt;/label&gt;\r\n        &lt;select id=&quot;domaine&quot; name=&quot;domaine&quot; required&gt;\r\n            &lt;option value=&quot;&quot;&gt;Sélectionnez un domaine&lt;/option&gt;\r\n            &lt;option value=&quot;informatique&quot; &lt;?= $_SESSION[\'form_data\'][\'domaine\'] == \'informatique\' ? \'selected\' : \'\' ?&gt;&gt;Informatique&lt;/option&gt;\r\n            &lt;option value=&quot;marketing&quot; &lt;?= $_SESSION[\'form_data\'][\'domaine\'] == \'marketing\' ? \'selected\' : \'\' ?&gt;&gt;Marketing&lt;/option&gt;\r\n            &lt;option value=&quot;finance&quot; &lt;?= $_SESSION[\'form_data\'][\'domaine\'] == \'finance\' ? \'selected\' : \'\' ?&gt;&gt;Finance&lt;/option&gt;\r\n            &lt;!-- Ajoutez d\'autres options si nécessaire --&gt;\r\n        &lt;/select&gt;\r\n    &lt;/div&gt;\r\n\r\n    &lt;div class=&quot;form-group&quot;&gt;\r\n        &lt;label for=&quot;remuneration&quot;&gt;Rémunération*&lt;/label&gt;\r\n        &lt;select id=&quot;remuneration&quot; name=&quot;remuneration&quot; required&gt;\r\n            &lt;option value=&quot;&quot;&gt;Sélectionnez une rémunération&lt;/option&gt;\r\n            &lt;option value=&quot;salaire&quot; &lt;?= $_SESSION[\'form_data\'][\'remuneration\'] == \'salaire\' ? \'selected\' : \'\' ?&gt;&gt;Salaire&lt;/option&gt;\r\n            &lt;option value=&quot;indemnité&quot; &lt;?= $_SESSION[\'form_data\'][\'remuneration\'] == \'indemnité\' ? \'selected\' : \'\' ?&gt;&gt;Indemnité&lt;/option&gt;\r\n            &lt;option value=&quot;autre&quot; &lt;?= $_SESSION[\'form_data\'][\'remuneration\'] == \'autre\' ? \'selected\' : \'\' ?&gt;&gt;Autre&lt;/option&gt;\r\n        &lt;/select&gt;\r\n    &lt;/div&gt;\r\n\r\n    &lt;div class=&quot;form-group&quot;&gt;\r\n        &lt;label for=&quot;ville&quot;&gt;Ville*&lt;/label&gt;\r\n        &lt;input type=&quot;text&quot; id=&quot;ville&quot; name=&quot;ville&quot; required value=&quot;&lt;?= htmlspecialchars($_SESSION[\'form_data\'][\'ville\'] ?? \'\') ?&gt;&quot;&gt;\r\n    &lt;/div&gt;\r\n\r\n    &lt;div class=&quot;form-group&quot;&gt;\r\n        &lt;label for=&quot;code_postal&quot;&gt;Code postal*&lt;/label&gt;\r\n        &lt;input type=&quot;text&quot; id=&quot;code_postal&quot; name=&quot;code_postal&quot; required value=&quot;&lt;?= htmlspecialchars($_SESSION[\'form_data\'][\'code_postal\'] ?? \'\') ?&gt;&quot;&gt;\r\n    &lt;/div&gt;\r\n\r\n    &lt;div class=&quot;form-group&quot;&gt;\r\n        &lt;label for=&quot;region&quot;&gt;Région*&lt;/label&gt;\r\n        &lt;select id=&quot;region&quot; name=&quot;region&quot; required&gt;\r\n            &lt;option value=&quot;&quot;&gt;Sélectionnez une région&lt;/option&gt;\r\n            &lt;option value=&quot;ile-de-france&quot; &lt;?= $_SESSION[\'form_data\'][\'region\'] == \'ile-de-france\' ? \'selected\' : \'\' ?&gt;&gt;Île-de-France&lt;/option&gt;\r\n            &lt;option value=&quot;provence-alpes-côte-d-azur&quot; &lt;?= $_SESSION[\'form_data\'][\'region\'] == \'provence-alpes-côte-d-azur\' ? \'selected\' : \'\' ?&gt;&gt;Provence-Alpes-Côte d\'Azur&lt;/option&gt;\r\n            &lt;!-- Ajoutez d\'autres options si nécessaire --&gt;\r\n        &lt;/select&gt;\r\n    &lt;/div&gt;\r\n\r\n    &lt;div class=&quot;form-group&quot;&gt;\r\n        &lt;label for=&quot;departement&quot;&gt;Département*&lt;/label&gt;\r\n        &lt;select id=&quot;departement&quot; name=&quot;departement&quot; required&gt;\r\n            &lt;option value=&quot;&quot;&gt;Sélectionnez un département&lt;/option&gt;\r\n            &lt;option value=&quot;75&quot; &lt;?= $_SESSION[\'form_data\'][\'departement\'] == \'75\' ? \'selected\' : \'\' ?&gt;&gt;Paris&lt;/option&gt;\r\n            &lt;option value=&quot;06&quot; &lt;?= $_SESSION[\'form_data\'][\'departement\'] == \'06\' ? \'selected\' : \'\' ?&gt;&gt;Alpes-Maritimes&lt;/option&gt;\r\n            &lt;!-- Ajoutez d\'autres options si nécessaire --&gt;\r\n        &lt;/select&gt;\r\n    &lt;/div&gt;\r\n\r\n    &lt;div class=&quot;form-group&quot;&gt;\r\n        &lt;label for=&quot;mode_stage&quot;&gt;Mode de stage*&lt;/label&gt;\r\n        &lt;select id=&quot;mode_stage&quot; name=&quot;mode_stage&quot; required&gt;\r\n            &lt;option value=&quot;&quot;&gt;Sélectionnez un mode de stage&lt;/option&gt;\r\n            &lt;option value=&quot;présentiel&quot; &lt;?= $_SESSION[\'form_data\'][\'mode_stage\'] == \'présentiel\' ? \'selected\' : \'\' ?&gt;&gt;Présentiel&lt;/option&gt;\r\n            &lt;option value=&quot;distanciel&quot; &lt;?= $_SESSION[\'form_data\'][\'mode_stage\'] == \'distanciel\' ? \'selected\' : \'\' ?&gt;&gt;Distanciel&lt;/option&gt;\r\n        &lt;/select&gt;\r\n    &lt;/div&gt;\r\n\r\n    &lt;button type=&quot;submit&quot; name=&quot;next_step&quot; value=&quot;2&quot;&gt;Suivant&lt;/button&gt;\r\n&lt;/form&gt;', '2025-02-14', NULL, '', 'présentiel', 'uploads/logos/202303-7-1.jpg', 'besjan.koraqii@gmail.com', '', 'cybersecurite', '500.00', 'France', 'Nevers', '58000', 'Bourgogne-Franche-Comté', '58', '2025-02-14 10:20:53', 0),
(39, 2, 'SFR stage 6 mois', 'qdssfdfsdfsdfhsfshkfjsdjcjsncnsejecjuezhxiuhe,hiouz,,o,zihoexde,iuhoxdxde,ziuh,ihuexd,iuhediho,uxedz,iuxez,iuhoexdzho,iuedho,iu,hoiuedh,xzuh,duxixdh,iuzh,u,iuz,iuhzho,iuzho,xiuxz,dihoxdze,hoixdhoe,ziu,dihoexzuedh,xziu,ihodzuex', '2025-02-14', NULL, '', 'présentiel', 'uploads/logos/202303-7-1.jpg', 'besjan.koraqii@gmail.com', '', 'developpement_mobile', '417.00', 'France', 'Decize', '58300', 'Bourgogne-Franche-Comté', '58', '2025-02-14 10:33:17', 0),
(40, 2, 'Stage numéro 1 en informatique', 'xwsdfrgthyjukilokjhgfdfrgthyjukiloijhgfdesefrgthygtygtrfjuhifdijuesfdjuifdijufdruijfrijufrijufdrijudrijudrijudijufdijijufdijfrijfijfdijfdrijdifidjfijfdjifjdidjiofdijok,sijuhgfrefrpoeijurefijupofreijupocferijupofri', '2025-02-14', '2025-02-28', '', 'présentiel', 'uploads/logos/202303-7-1.jpg', 'besjan.koraqii@gmail.com', '', 'developpement_mobile', '800.00', 'France', 'Nevers', '58000', 'Bourgogne-Franche-Comté', '58', '2025-02-14 10:37:50', 0),
(42, 2, 'Stage numéro 1 en informatique', 'function getFormValue($field) {\r\n    return isset($_SESSION[\'form_data\'][$field]) ? $_SESSION[\'form_data\'][$field] : \'\';\r\n}\r\nfunction getFormValue($field) {\r\n    return isset($_SESSION[\'form_data\'][$field]) ? $_SESSION[\'form_data\'][$field] : \'\';\r\n}\r\n', '2025-02-21', '2025-02-27', '', 'distanciel', 'uploads/logos/67b87e5f4a390.png', 'besjan.koraqii@gmail.com', '', 'ressources_humaines', '417.00', 'France', 'Imphy', '58160', 'Bourgogne-Franche-Comté', '58', '2025-02-14 14:10:11', 0),
(43, 2, 'Stage en informatique', 'sssssssssssssssssssssssssssssssssssssssgdshbjnk,lqjdhfgrydhsbujnqkIHDUYGFTHSIJQK?sdihufygrhsjiqk,OIUHDYFGSIJKQ?ZIUHDEYFGRDJUSKQ?DIUHYFGHSJQKsssssssssssssssssssssssssssssssssssssssgdshbjnk,lqjdhfgrydhsbujnqkIHDUYGFTHSIJQK?sdihufygrhsjiqk,OIUHDYFGSIJKQ?ZIUHDEYFGRDJUSKQ?DIUHYFGHSJQKsssssssssssssssssssssssssssssssssssssssgdshbjnk,lqjdhfgrydhsbujnqkIHDUYGFTHSIJQK?sdihufygrhsjiqk,OIUHDYFGSIJKQ?ZIUHDEYFGRDJUSKQ?DIUHYFGHSJQKsssssssssssssssssssssssssssssssssssssssgdshbjnk,lqjdhfgrydhsbujnqkIHDUYGFTHSIJQK?sdihufygrhsjiqk,OIUHDYFGSIJKQ?ZIUHDEYFGRDJUSKQ?DIUHYFGHSJQKsssssssssssssssssssssssssssssssssssssssgdshbjnk,lqjdhfgrydhsbujnqkIHDUYGFTHSIJQK?sdihufygrhsjiqk,OIUHDYFGSIJKQ?ZIUHDEYFGRDJUSKQ?DIUHYFGHSJQK', '2025-02-21', '2025-02-28', '', 'distanciel', 'uploads/logos/67b87e243ae32.png', 'besjan.koraqii@gmail.com', '', 'developpement_web', '417.00', 'France', 'Cosne-Cours-sur-Loire', '58200', 'Bourgogne-Franche-Comté', '58', '2025-02-17 10:27:26', 0),
(44, 2, 'Stage numéro 1000 en informatique', 'eseeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee', '2025-02-21', '2025-05-23', '', 'présentiel', NULL, 'besjan.koraqii@gmail.com', '', 'developpement_web', '417.00', 'France', 'Imphy', '58160', 'Bourgogne-Franche-Comté', '58', '2025-02-21 15:12:35', 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT pour la table `competences`
--
ALTER TABLE `competences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `entreprises`
--
ALTER TABLE `entreprises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;
--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `offres_stages`
--
ALTER TABLE `offres_stages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;
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
