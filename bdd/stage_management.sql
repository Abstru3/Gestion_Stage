-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Mer 19 Mars 2025 à 14:18
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
  `etudiant_id` int(11) NOT NULL,
  `offre_id` int(11) NOT NULL,
  `statut` enum('en_attente','acceptee','refusee') DEFAULT 'en_attente',
  `date_candidature` datetime DEFAULT CURRENT_TIMESTAMP,
  `lettre_motivation` varchar(255) NOT NULL,
  `date_modification` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_lecture` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `candidatures`
--

INSERT INTO `candidatures` (`id`, `etudiant_id`, `offre_id`, `statut`, `date_candidature`, `lettre_motivation`, `date_modification`, `date_lecture`) VALUES
(3, 1, 30, 'en_attente', '2025-02-05 15:05:22', '67a3702292f3b.pdf', '2025-02-12 10:47:31', NULL),
(4, 5, 32, 'acceptee', '2025-02-06 14:47:56', '67a4bd8c3f09a.pdf', '2025-02-12 14:55:04', '2025-02-12 14:55:04'),
(5, 5, 33, 'acceptee', '2025-02-07 09:36:51', '67a5c62396d5a.pdf', '2025-02-12 14:38:01', '2025-02-12 14:38:01'),
(6, 6, 32, 'refusee', '2025-02-11 09:36:22', '67ab0c069b856.pdf', '2025-02-12 11:17:29', '2025-02-12 11:17:29'),
(7, 7, 32, 'acceptee', '2025-02-11 09:43:33', '67ab0db579a84.pdf', '2025-02-17 14:03:01', '2025-02-17 14:03:01'),
(8, 8, 31, 'en_attente', '2025-02-11 19:34:47', '67ab984784909.pdf', '2025-02-12 11:16:51', '2025-02-12 11:16:51'),
(9, 8, 32, 'acceptee', '2025-02-11 19:35:09', '67ab985d1eebe.pdf', '2025-02-12 11:16:51', '2025-02-12 11:16:51'),
(10, 9, 33, 'refusee', '2025-02-11 19:39:17', '67ab9955498d9.pdf', '2025-02-12 11:15:32', NULL),
(11, 5, 34, 'en_attente', '2025-02-14 16:03:15', '67af5b3326bd4.pdf', '2025-02-14 16:03:22', '2025-02-14 16:03:22'),
(12, 7, 37, 'acceptee', '2025-02-17 14:20:29', '67b3379d36d9a.pdf', '2025-02-17 14:21:39', '2025-02-17 14:21:39'),
(13, 6, 35, 'en_attente', '2025-03-14 09:55:00', '67d3eee475441_lettre_6.pdf', '2025-03-14 09:55:11', '2025-03-14 09:55:11'),
(14, 7, 31, 'en_attente', '2025-03-14 09:58:30', '67d3efb66cd6f_lettre_7.pdf', '2025-03-14 09:58:33', '2025-03-14 09:58:33');

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
(2, '03215874859658', 'iutnervers', NULL, NULL, NULL, NULL, '0744813941', 'https://www.exemple.com', NULL, NULL, NULL, 'sssssssssssssssssssssssssssssssssssssss', '42 Rue Des Tailles, 58000 Nevers, France', NULL, 'Besjan Koraqi', 's', 'besjan.koraqii@gmail.com', '$2y$10$QyWJW6rO8Ir0n2TNeMszV.hGdVw307d6PCqflc.cSc6swQRpErKIO', 'entreprise', '2025-02-06 09:29:04', NULL, 1, 0, '#3498db', 0),
(4, '02589632147895', 'Besjan Koraqi', NULL, NULL, NULL, NULL, '0144813944', 'https://www.exemple.com', NULL, 'undefined', NULL, 'sccccccccc', '42 Rue Des Tailles, 58000 Nevers, France', NULL, 'Besjan Koraqi', 'f888', 'qii@gmail.com', '$2y$10$5oDM1wrzjZQLDeh24rvgK.72MiDLEgXCadK.i.uMZR/IR0B4vF7By', 'entreprise', '2025-02-06 09:56:38', '67adf9224b5dd.jpg', 1, 0, '#3498db', 0),
(5, '61334717555763', 'Quizzine', NULL, NULL, NULL, NULL, '0622462417', 'https://quizzine.fr', NULL, 'undefined', NULL, 'Site de quiz en ligne interactif.', '666 rue de la Bite, 58000 Nevers', NULL, 'Henriot Léo', 'Quizzine', 'mail.contact@quizzine.fr', '$2y$10$3ELy3w44yZSThB61P1ficOM0l0Mfto32ox4izy/g1PwJEwp.lkf/2', 'entreprise', '2025-02-06 13:39:01', '67acbdf83f26a.png', 1, 1, '#fecc16', 0),
(6, '64936023098967', 'Google', NULL, NULL, NULL, NULL, '0606060606', 'https://google.com', NULL, 'undefined', NULL, 'C\'est Google, là où on cherche des trucs.', 'Google Inc. 1600 Amphitheatre Parkway, Mountain View, CA', NULL, 'Sundar Pichai', 'Google', 'mail@google.com', '$2y$10$nApYzkieTHanPbqWKIx8ZObpdP8KPzxtXSGeryipHOTQEy4Ou.fxi', 'entreprise', '2025-02-13 09:45:56', '67adbf83679ec.png', 1, 1, '#22a3ce', 0),
(7, '69156287504519', 'Carrefour Nevers', NULL, NULL, NULL, NULL, '0669696969', 'https://www.carrefour.fr/magasin/nevers-marzy', NULL, 'undefined', NULL, 'On vend des produits dans des supermarchés (et d\'autres trucs qu\'on oublie).', '7 Rue Etienne Litaud, 58000 Nevers', NULL, 'Supermarché Carrefour', 'Carrefour', 'contact@carrefour.fr', '$2y$10$6wWLcb1vo3.fHiuhkB1JR.tjKDEuMXd4Cs0mqTunh/4EGJq2g.GDe', 'entreprise', '2025-02-13 14:45:50', '67b33644b859c.jpg', 1, 0, '#3498db', 0);

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
  `bloque` tinyint(1) DEFAULT '0',
  `preference` enum('stage','alternance') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `etudiants`
--

INSERT INTO `etudiants` (`id`, `nom`, `prenom`, `date_naissance`, `adresse`, `ville`, `code_postal`, `pays`, `telephone`, `niveau_etude`, `filiere`, `cv`, `username`, `email`, `password`, `role`, `lettre_motivation`, `date_creation`, `icone`, `bloque`, `preference`) VALUES
(1, 'Koraqi', 'Besjan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'f585', 'aqii@gmail.com', '$2y$10$QUo31Cnq3k5YPDzaLjGyn.z1D9rWD5DalNBYzSibwyDMz7EiLnvYq', 'etudiant', NULL, '2025-02-06 09:29:04', NULL, 0, NULL),
(2, 'Koraqi', 'Besjan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 'raqii@gmail.com', '$2y$10$QUo31Cnq3k5YPDzaLjGyn.z1D9rWD5DalNBYzSibwyDMz7EiLnvYq', 'admin', NULL, '2025-02-06 09:29:04', NULL, 0, NULL),
(3, 'Koraqi', 'Besjan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'f', 'i@gmail.com', '$2y$10$X37ad9GrOp8thMeGE49EU.lX4IZXdegAILB0k3nEeWxUcEKCnRFie', 'etudiant', NULL, '2025-02-06 09:53:40', NULL, 0, NULL),
(4, 'Henriot', 'Léo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Administrateur', 'admin@admin.fr', '$2y$10$X4QuNJya8RiQO8Dyt/eMFubDEILmzXM0.tcZeirRBtOE3TFPUQezu', 'admin', NULL, '2025-02-06 13:31:37', NULL, 0, NULL),
(5, 'Amo', 'Gus', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '67d2ddd15b8a1.pdf', 'Amogus', 'amog.us@mail.fr', '$2y$10$gMeexfysIdkjRsXHwwq7EO/SKReE3e97FKePpQOPTTSztG3Ykak96', 'etudiant', NULL, '2025-02-06 13:45:13', '67b3330dc867e.png', 0, 'alternance'),
(6, 'm5wc', '8594', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '67d3eed07bc3a.pdf', 'm5wc', 'm5wc@gmail.com', '$2y$10$fWULE5e7z3KOCtyorD9/BOiLT45LgyIqdtlapSZ.Yl5FagNViAdG2', 'etudiant', NULL, '2025-02-11 08:35:02', NULL, 0, NULL),
(7, 'Henriot', 'Léo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '67d2f566e52de.pdf', 'LeoH_', 'leo.henriot58@gmail.com', '$2y$10$GiRhAKllqofO2y.i6atVeeumv7x28j8JQpo1wmPg6erUv/LSMmW6q', 'etudiant', NULL, '2025-02-11 08:43:02', NULL, 0, NULL),
(8, 'Etudiant', 'Nevers', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'etudiant58', 'etudiant@exemple.fr', '$2y$10$vVCFOs0DPcyAhQtvGu1lQ.7WS8DYs.ngFKKXsTRzYHVJJJZQHXzzW', 'etudiant', NULL, '2025-02-11 18:34:03', NULL, 0, NULL),
(9, 'e', 'e', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'e', 'e@e.fr', '$2y$10$Ml7YaBjGUdwQQNWJwt1Tx.DQYzVoTJ2Sz/VvDbUYrrJMMSqMeuCZa', 'etudiant', NULL, '2025-02-11 18:38:37', NULL, 0, NULL);

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
(20, 'iutnervers', 'entreprise', 'J\'aime bien', '2025-02-10 10:29:21', '2025-02-10 10:29:21'),
(21, 'Quizzine', 'entreprise', 'Je donne mon avis', '2025-02-11 10:12:54', '2025-02-11 10:12:54'),
(22, 'Amo Gus', 'etudiant', 'Amo Gus', '2025-02-12 14:10:56', '2025-02-12 14:10:56'),
(23, 'Google', 'entreprise', 'Avis de google', '2025-02-13 10:46:44', '2025-02-13 10:46:44'),
(24, 'Carrefour Nevers', 'entreprise', 'C\'est super trop cool j\'adore vraiment merci trop merci les gens whoa trop bien j\'adore j\'aime trop', '2025-02-13 15:06:24', '2025-02-13 15:06:24'),
(25, 'Quizzine', 'entreprise', 'bngfnh,;y;u', '2025-02-18 07:19:57', '2025-02-18 07:19:57'),
(26, 'Amo Gus', 'etudiant', 'mi', '2025-02-18 16:30:36', '2025-02-18 16:30:36');

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
(59, 'E2', 'E1', 'salut jeune étudiant, je vous accepte mais j\'aimerais vous posez quelques questions', '2025-02-11 09:50:36', 'non_lu', NULL),
(60, 'E1', 'E2', 'Bonjour, oui posez toutes les questions qui vous chantes', '2025-02-11 10:04:19', 'non_lu', NULL),
(61, 'E2', 'E1', 'Merci pour votre réactivité donc j\'aimerais savoir si vous possédez un moyen de transport ?', '2025-02-11 10:07:37', 'non_lu', NULL),
(62, 'E2', 'E1', 'Merci pour votre réactivité donc j\'aimerais savoir si vous possédez un moyen de transport ?', '2025-02-11 10:07:43', 'non_lu', NULL),
(63, 'E2', 'E1', 'jnckzc', '2025-02-11 10:07:55', 'non_lu', NULL),
(64, 'E1', 'E2', 'oui', '2025-02-11 10:09:04', 'non_lu', NULL),
(65, 'E1', 'E2', 'fd', '2025-02-11 10:09:14', 'non_lu', NULL),
(66, 'E1', 'E2', 'rfddg', '2025-02-11 10:09:16', 'non_lu', NULL),
(67, 'E1', 'E2', 'fd', '2025-02-11 10:09:32', 'non_lu', NULL),
(68, 'E2', 'E1', 'bla', '2025-02-11 10:10:07', 'non_lu', NULL),
(69, 'E2', 'E1', 'sdsd', '2025-02-11 10:12:14', 'non_lu', 1),
(70, 'E2', 'E1', 'efsfe', '2025-02-11 10:13:17', 'non_lu', 1),
(71, 'E2', 'E1', 'lk', '2025-02-11 10:17:36', 'non_lu', 1),
(72, 'E2', 'E1', 'uol', '2025-02-11 10:18:20', 'non_lu', 1),
(73, 'E2', 'E1', 'dfgh', '2025-02-11 10:24:33', 'non_lu', 1),
(74, 'E2', 'E1', '!:;,nb', '2025-02-11 10:24:38', 'non_lu', 1),
(75, 'E1', 'E2', 'frgthj', '2025-02-11 10:25:10', 'non_lu', 1),
(76, 'E1', 'E2', 'rtgh', '2025-02-11 10:34:22', 'non_lu', 1),
(77, 'E1', 'E2', 'dzdz', '2025-02-11 10:52:00', 'non_lu', 1),
(78, 'E1', 'E2', 'dezde', '2025-02-11 10:59:57', 'non_lu', 1),
(79, 'E2', 'E3', 'salut ta le même prénom que ton frère', '2025-02-11 11:06:21', 'non_lu', 2),
(80, 'E2', 'E1', 'dzdz', '2025-02-11 11:16:13', 'non_lu', 1),
(81, 'E2', 'E3', 'xsxs', '2025-02-11 15:24:46', 'non_lu', 2),
(94, 'C5', 'E5', 'salut', '2025-02-11 16:13:11', 'lu', 3),
(95, 'C5', 'E7', 'salut', '2025-02-11 16:13:53', 'lu', 4),
(96, 'C5', 'E7', 'hiuiui', '2025-02-11 16:15:48', 'lu', 4),
(97, 'C5', 'E7', 'mm', '2025-02-11 16:15:55', 'lu', 4),
(98, 'C5', 'E7', 'salut mec', '2025-02-11 16:20:12', 'lu', 4),
(99, 'C5', 'E5', 'salut mec', '2025-02-11 16:20:56', 'lu', 3),
(100, 'E5', 'C5', 'egrureygyuerqhg', '2025-02-11 16:24:02', 'lu', 3),
(101, 'C5', 'E5', 'oui', '2025-02-11 16:24:25', 'lu', 3),
(102, 'C5', 'E6', 'ok mec', '2025-02-11 16:24:43', 'lu', 5),
(103, 'E6', 'C5', 'oui', '2025-02-11 16:24:56', 'lu', 5),
(104, 'E5', 'C5', 'gezgrehty', '2025-02-11 16:31:18', 'lu', 3),
(105, 'E5', 'C5', 'salut', '2025-02-11 16:31:22', 'lu', 3),
(106, 'E5', 'C5', 'htyj', '2025-02-11 16:33:51', 'lu', 3),
(107, 'E5', 'C5', 'dzefz', '2025-02-11 16:33:54', 'lu', 3),
(108, 'E5', 'C5', 'adde', '2025-02-11 16:38:28', 'lu', 3),
(109, 'E5', 'C5', 'lsaurt', '2025-02-11 16:56:56', 'lu', 3),
(110, 'C5', 'E7', 'aaaa', '2025-02-11 19:32:36', 'lu', 4),
(111, 'C5', 'E7', 'ffff', '2025-02-11 19:32:38', 'lu', 4),
(112, 'C5', 'E5', 'salut', '2025-02-12 09:22:29', 'lu', 3),
(113, 'C5', 'E7', 'ok mec', '2025-02-12 09:33:27', 'lu', 4),
(114, 'C5', 'E5', 'tu as deux messages', '2025-02-12 10:03:15', 'lu', 3),
(115, 'E5', 'C5', 'Tu as deux messages ?', '2025-02-12 10:04:38', 'lu', 3),
(116, 'E5', 'C5', 'un dexuièeme', '2025-02-12 10:04:42', 'lu', 3),
(117, 'C5', 'E5', '1', '2025-02-12 10:05:13', 'lu', 3),
(118, 'C5', 'E5', '2', '2025-02-12 10:05:15', 'lu', 3),
(119, 'C5', 'E5', '3', '2025-02-12 10:05:16', 'lu', 3),
(120, 'E5', 'C5', '1', '2025-02-12 10:10:01', 'lu', 3),
(121, 'E5', 'C5', '2', '2025-02-12 10:10:03', 'lu', 3),
(122, 'E5', 'C5', '3', '2025-02-12 10:10:03', 'lu', 3),
(123, 'C5', 'E5', 'skbiidi 1', '2025-02-12 10:10:22', 'lu', 3),
(124, 'C5', 'E5', '2', '2025-02-12 10:10:23', 'lu', 3),
(125, 'C5', 'E5', '3', '2025-02-12 10:10:24', 'lu', 3),
(126, 'C5', 'E5', '4', '2025-02-12 10:10:25', 'lu', 3),
(127, 'C5', 'E8', 'Salut étudiant', '2025-02-12 10:11:36', 'lu', 6),
(128, 'E8', 'C5', '1', '2025-02-12 10:15:05', 'lu', 6),
(129, 'E8', 'C5', '2', '2025-02-12 10:15:06', 'lu', 6),
(130, 'E8', 'C5', '3', '2025-02-12 10:15:07', 'lu', 6),
(131, 'C5', 'E8', '1', '2025-02-12 10:15:26', 'lu', 6),
(132, 'C5', 'E8', '2', '2025-02-12 10:15:27', 'lu', 6),
(133, 'C5', 'E8', '3', '2025-02-12 10:15:28', 'lu', 6),
(134, 'C5', 'E8', '4', '2025-02-12 10:15:29', 'lu', 6),
(135, 'C5', 'E8', '5', '2025-02-12 10:15:30', 'lu', 6),
(136, 'C5', 'E8', '6', '2025-02-12 10:15:31', 'lu', 6),
(137, 'C5', 'E8', '7', '2025-02-12 10:15:32', 'lu', 6),
(138, 'C5', 'E8', '8', '2025-02-12 10:15:33', 'lu', 6),
(139, 'C5', 'E8', '9', '2025-02-12 10:15:35', 'lu', 6),
(140, 'C5', 'E8', '10', '2025-02-12 10:15:36', 'lu', 6),
(141, 'C5', 'E8', '11', '2025-02-12 10:15:37', 'lu', 6),
(142, 'C5', 'E5', '1', '2025-02-12 10:28:22', 'lu', 3),
(143, 'C5', 'E5', '2', '2025-02-12 10:28:23', 'lu', 3),
(144, 'C5', 'E5', '3', '2025-02-12 10:28:24', 'lu', 3),
(145, 'C5', 'E5', '4', '2025-02-12 10:28:25', 'lu', 3),
(146, 'C5', 'E5', '5', '2025-02-12 10:28:25', 'lu', 3),
(147, 'E5', 'C5', '1', '2025-02-12 10:29:17', 'lu', 3),
(148, 'E5', 'C5', '2', '2025-02-12 10:29:18', 'lu', 3),
(149, 'E5', 'C5', '3', '2025-02-12 10:29:19', 'lu', 3),
(150, 'E5', 'C5', '4', '2025-02-12 10:29:20', 'lu', 3),
(151, 'E5', 'C5', '5', '2025-02-12 10:29:21', 'lu', 3),
(152, 'E6', 'C5', 'ok', '2025-02-12 10:34:31', 'lu', 5),
(153, 'E6', 'C5', 'ok', '2025-02-12 10:34:32', 'lu', 5),
(154, 'E5', 'C5', 'salut', '2025-02-12 10:50:58', 'lu', 3),
(155, 'C5', 'E8', 'bonjour étudiant nevers', '2025-02-12 11:16:38', 'lu', 6),
(156, 'E5', 'C5', 'salut', '2025-02-12 11:26:44', 'lu', 3),
(157, 'C5', 'E5', 'ok mon loulou', '2025-02-12 11:27:01', 'lu', 3),
(158, 'C5', 'E5', 'salut', '2025-02-12 11:50:27', 'lu', 3),
(159, 'C5', 'E5', 'getrhtr', '2025-02-12 14:34:44', 'lu', 3),
(160, 'E5', 'C5', 'salut', '2025-02-12 15:37:17', 'lu', 3),
(161, 'C6', 'E5', 'Salut amogus', '2025-02-14 16:04:17', 'lu', 7),
(162, 'E5', 'C6', 'ok', '2025-02-14 16:18:52', 'non_lu', 7),
(163, 'C5', 'E5', 'hjketgnonhtr', '2025-02-18 08:18:26', 'lu', 3),
(164, 'E5', 'C6', 'hbvyvtv', '2025-02-18 17:32:03', 'non_lu', 7),
(165, 'C5', 'E9', 'rfytbb', '2025-02-21 16:23:21', 'non_lu', 8);

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
  `mode_stage` varchar(20) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `email_contact` varchar(255) NOT NULL DEFAULT '',
  `lien_candidature` varchar(255) DEFAULT NULL,
  `domaine` varchar(100) DEFAULT NULL,
  `pays` varchar(100) DEFAULT 'France',
  `ville` varchar(100) DEFAULT NULL,
  `code_postal` varchar(10) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `departement` varchar(100) DEFAULT NULL,
  `date_publication` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type_offre` enum('stage','alternance') DEFAULT 'stage',
  `niveau_etude` varchar(100) DEFAULT NULL,
  `type_contrat` enum('apprentissage','professionnalisation') DEFAULT NULL,
  `rythme_alternance` varchar(100) DEFAULT NULL,
  `formation_visee` varchar(255) DEFAULT NULL,
  `ecole_partenaire` varchar(255) DEFAULT NULL,
  `type_remuneration` varchar(20) DEFAULT NULL COMMENT 'Pour les alternances: smic27, smic43, etc.',
  `pourvu` tinyint(1) DEFAULT '0',
  `remuneration` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Contient à la fois les offres de stages et les offres d''alternance';

--
-- Contenu de la table `offres_stages`
--

INSERT INTO `offres_stages` (`id`, `entreprise_id`, `titre`, `description`, `date_debut`, `date_fin`, `lieu`, `mode_stage`, `logo`, `email_contact`, `lien_candidature`, `domaine`, `pays`, `ville`, `code_postal`, `region`, `departement`, `date_publication`, `type_offre`, `niveau_etude`, `type_contrat`, `rythme_alternance`, `formation_visee`, `ecole_partenaire`, `type_remuneration`, `pourvu`, `remuneration`) VALUES
(30, 2, 'Stage numéro 1 en informatique', 'Test! !! ! !', '2025-02-05', '2025-02-28', 'nevers , 45 rue des boloss', 'présentiel', 'uploads/logos/202303-7-1.jpg', 'besjan.koraqii@gmail.com', NULL, 'developpement_web', 'France', 'Chenôve', '58000', 'Bourgogne-Franche-Comté', '21', '2025-02-12 15:23:34', 'stage', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(31, 4, 'Stage numéro 6 en informatique', 'aaa', '2025-02-06', '2026-12-25', 'nevers , 45 rue des boloss', 'distanciel', NULL, '', NULL, NULL, 'France', NULL, NULL, NULL, NULL, '2025-02-12 15:23:34', 'stage', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(32, 5, 'Améliorations Front-End', 'Il s\'agirait d\'améliorer les fonctionnalités non finis en front end pour un design pour simple.', '2025-02-15', '2025-05-02', 'QG Quizzine', 'présentiel', 'uploads/logos/logo_quizzine_petit.png', '', NULL, NULL, 'France', NULL, NULL, NULL, NULL, '2025-02-12 15:23:34', 'stage', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(33, 5, 'Entretient du Back-End', 'Gérer l\'entretient du back-end durant', '2025-03-20', '2025-06-18', '', 'distanciel', 'uploads/logos/logo_quizzine_petit.png', '', NULL, NULL, 'France', NULL, NULL, NULL, NULL, '2025-02-12 15:23:34', 'stage', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(34, 6, 'Coder des trucs de Google', 'Rejoignez l’équipe Google pour un stage enrichissant où vous participerez au développement de solutions innovantes. Vous travaillerez sur des projets techniques stimulants, en utilisant des technologies avancées pour résoudre des problématiques complexes. Vous aurez l’opportunité de collaborer avec des experts de l’industrie, de développer vos compétences techniques et de contribuer à des projets impactants à grande échelle. Ce stage vous permettra d’acquérir une expérience précieuse dans un environnement dynamique et créatif.', '2025-04-25', '2026-08-15', '', 'présentiel', NULL, 'contact@google.com', '', 'developpement_web', 'France', 'Auxerre', '89000', 'Bourgogne-Franche-Comté', '89', '2025-02-13 10:23:17', 'stage', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(35, 6, 'Google encore', 'La mission du stage chez Google est la suivante, il faudra faire beaucoup de trucs, coder des autres trucs, etc... La description est assez longue, je ne sais pas quoi écrire mais personne ne lira ça dans tout les cas donc bon...', '2025-03-22', '2025-06-14', '', 'présentiel', NULL, 'contact@google.com', '', 'reseaux', 'France', NULL, NULL, NULL, NULL, '2025-02-13 10:33:44', 'stage', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(36, 6, 'Google - 3e annonce', 'AJbauydvYVBAthyivTYcvYFtrdydIYDydYURSDutdFufUOFUOfUCkhIYcyacyocaOYOSCsayUACCAYQSYIcaiciYAYCSaysicYiacsycyCSYOCayiscayctCSIYciyCSyicYYacyiscacsYFCScsyaicYICSYaicsayfCYAFcysfACYaifcsyaFCSayfscYAGSCvyvsbab HUSObaijPBaub AIhaygAYFYUFa', '2025-05-24', '2025-10-23', '', 'distanciel', NULL, 'contact@google.com', '', 'developpement_mobile', 'France', '', '', '', '', '2025-02-13 10:41:12', 'stage', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(37, 7, 'Stage en Assistance Rayon Informatique', 'Au sein du magasin Carrefour, vous intégrerez l’équipe du rayon informatique et multimédia pour un stage enrichissant. Sous la supervision du responsable de rayon, vous participerez à la gestion des stocks, à la mise en rayon des produits et à l’actualisation des étiquettes de prix. Vous assisterez également les clients en les conseillant sur les produits high-tech (ordinateurs, périphériques, accessoires, logiciels). Vous apprendrez à utiliser les outils de gestion de stock et à assurer un service client de qualité. En collaboration avec l’équipe, vous veillerez à la bonne présentation du rayon et à la mise en avant des promotions. Ce stage vous offrira une immersion dans le secteur de la grande distribution, en développant vos compétences en relation client et en logistique. Dynamique et curieux, vous aimez le contact avec les clients et avez un intérêt pour l’informatique.', '2025-07-18', '2025-08-08', '', 'présentiel', NULL, 'contact@google.com', '', 'vente', 'France', 'Nevers', '58000', 'Bourgogne-Franche-Comté', '58', '2025-02-17 13:18:57', 'stage', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(38, 5, 'Alternance de Quizzine', 'zzazehjgzbuhv hegfvzyizfev zefgyze iz feygfz ezfuyzefuzefzb e fzehzgyfzegfizefzfy uuyfzevyuifgzetgugfzehzufehz ufyzeyfzgeugfuzg zzfeugzuigfzeu eguigyfeziuge fugguzgefgizgezufgu euigzefugzefui guiguzefguy gzefuig zugfzuegzefg gfgz eguiezfg uz.', '2025-03-22', '2025-08-29', '', 'présentiel', NULL, 'mail.contact@quizzine.fr', '', 'developpement_web', 'France', 'Nevers', '58000', 'Bourgogne-Franche-Comté', '58', '2025-03-18 09:14:02', 'alternance', 'bac+2', 'apprentissage', '2sem_1sem', '', 'IUT Nevers', 'smic43', 0, '774'),
(39, 7, 'Alternance chez Carrefour', 'aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab aaabab.', '2025-03-29', '2025-12-27', '', 'présentiel', NULL, 'carrefour@contact.fr', '', 'vente', 'France', 'Nevers', '58000', 'Bourgogne-Franche-Comté', '58', '2025-03-18 10:52:09', 'alternance', 'autre', 'professionnalisation', '1sem_1sem', '', '', NULL, 0, '5600');

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
  ADD KEY `entreprise_id` (`entreprise_id`),
  ADD KEY `idx_type_offre` (`type_offre`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT pour la table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;
--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `offres_stages`
--
ALTER TABLE `offres_stages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
--
-- AUTO_INCREMENT pour la table `stages`
--
ALTER TABLE `stages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `candidatures`
--
ALTER TABLE `candidatures`
  ADD CONSTRAINT `candidatures_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id`),
  ADD CONSTRAINT `candidatures_ibfk_2` FOREIGN KEY (`offre_id`) REFERENCES `offres_stages` (`id`);

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
