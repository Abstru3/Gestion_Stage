-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Mar 11 Février 2025 à 08:32
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
  `lettre_motivation` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `candidatures`
--

INSERT INTO `candidatures` (`id`, `etudiant_id`, `offre_id`, `statut`, `date_candidature`, `cv`, `lettre_motivation`) VALUES
(3, 1, 30, 'acceptee', '2025-02-05 15:05:22', '67a37022928cc.pdf', '67a3702292f3b.pdf'),
(4, 5, 32, 'acceptee', '2025-02-06 14:47:56', '67a4bd8c3e8aa.pdf', '67a4bd8c3f09a.pdf'),
(5, 5, 33, 'en_attente', '2025-02-07 09:36:51', '67a5c62396937.pdf', '67a5c62396d5a.pdf');

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
(2, '03215874859658', 'iutnervers', NULL, NULL, NULL, NULL, '0744813941', 'https://www.exemple.com', NULL, NULL, NULL, 'sssssssssssssssssssssssssssssssssssssss', '42 Rue Des Tailles, 58000 Nevers, France', NULL, 'Besjan Koraqi', 's', 'besjan.koraqii@gmail.com', '$2y$10$QyWJW6rO8Ir0n2TNeMszV.hGdVw307d6PCqflc.cSc6swQRpErKIO', 'entreprise', '2025-02-06 09:29:04', NULL),
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
  `expediteur_id` int(11) NOT NULL,
  `destinataire_id` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `date_envoi` datetime DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('non_lu','lu') DEFAULT 'non_lu',
  `conversation_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `messages`
--

INSERT INTO `messages` (`id`, `expediteur_id`, `destinataire_id`, `contenu`, `date_envoi`, `statut`, `conversation_id`) VALUES
(1, 2, 1, 'salut t trop fort vas y vien cousin', '2025-02-06 11:26:26', 'lu', NULL),
(2, 1, 2, 'ouaisssss mercii', '2025-02-06 11:27:28', 'lu', NULL),
(3, 1, 2, 'mercoiiiiii', '2025-02-06 11:28:29', 'lu', NULL),
(4, 1, 2, 'mercoiiiiii', '2025-02-06 11:28:55', 'lu', NULL),
(5, 1, 2, 'eznfizfne', '2025-02-06 11:29:03', 'lu', NULL),
(6, 2, 1, 'nique toi je rigolais bhaaaaa', '2025-02-06 11:40:48', 'lu', NULL),
(7, 1, 2, 'mais weshhhhh t un chienn toi', '2025-02-06 11:41:21', 'lu', NULL),
(8, 5, 5, 'Peux-tu me dire si parce que pourquoi le fait que cela implique l\'autre chose qui sera là ?', '2025-02-06 14:51:26', 'lu', NULL),
(9, 5, 5, 'oui madame', '2025-02-06 14:52:43', 'lu', NULL),
(10, 2, 1, 'dzdzd', '2025-02-10 10:21:04', 'lu', NULL),
(11, 2, 1, 'salamm', '2025-02-10 10:22:05', 'lu', NULL),
(12, 2, 1, 'sava gros', '2025-02-10 10:22:27', 'lu', NULL),
(13, 2, 1, 'dzdzd', '2025-02-10 10:22:32', 'lu', NULL),
(14, 2, 1, 'ssdsd', '2025-02-10 10:23:55', 'lu', NULL),
(15, 2, 1, 'dsdsd', '2025-02-10 10:24:49', 'lu', NULL),
(16, 2, 1, 'sdsdsd', '2025-02-10 10:24:52', 'lu', NULL),
(17, 2, 1, 'sdsd', '2025-02-10 10:25:09', 'lu', NULL),
(18, 2, 1, 'fgrgtg', '2025-02-10 10:25:12', 'lu', NULL),
(19, 2, 1, 'ùmm', '2025-02-10 10:33:35', 'lu', NULL),
(20, 2, 1, 'dzdz', '2025-02-10 10:34:04', 'lu', NULL),
(21, 2, 1, 'yf', '2025-02-10 10:34:40', 'lu', NULL),
(22, 2, 1, 'rgdgr', '2025-02-10 10:34:43', 'lu', NULL),
(23, 2, 1, 'sdsd', '2025-02-10 10:35:28', 'lu', NULL),
(24, 2, 1, 'sdsd', '2025-02-10 10:42:51', 'lu', NULL),
(25, 2, 1, 'fhth', '2025-02-10 10:58:16', 'lu', NULL),
(26, 2, 1, 'thfh', '2025-02-10 10:58:24', 'lu', NULL),
(27, 2, 1, 'sa dit quoi\r\n\r\n', '2025-02-10 11:09:30', 'lu', NULL),
(28, 2, 1, 'dzdz', '2025-02-10 11:09:32', 'lu', NULL),
(29, 2, 1, 'dzdzd', '2025-02-10 11:09:49', 'lu', NULL),
(30, 2, 1, 'dneznflk\r\n', '2025-02-10 11:12:00', 'lu', NULL),
(31, 2, 1, 'gtgd', '2025-02-10 11:22:49', 'lu', NULL),
(32, 2, 1, 'ss', '2025-02-10 15:30:28', 'lu', NULL),
(33, 2, 1, 'dthy', '2025-02-10 15:30:36', 'lu', NULL),
(34, 2, 1, 'tcgtc', '2025-02-10 15:30:39', 'lu', NULL),
(35, 2, 2, 'yvhyvy', '2025-02-10 15:30:47', 'lu', NULL),
(36, 2, 5, 'opmopm', '2025-02-10 15:30:52', 'non_lu', NULL),
(37, 2, 1, 'thyjuki', '2025-02-10 15:31:21', 'lu', NULL),
(38, 1, 2, 'sdsds', '2025-02-10 16:01:06', 'non_lu', NULL),
(39, 1, 2, 'ssd', '2025-02-10 16:02:46', 'non_lu', NULL),
(40, 1, 2, 'csc', '2025-02-10 16:07:10', 'non_lu', NULL),
(41, 1, 2, 'sdssd', '2025-02-10 16:18:01', 'non_lu', NULL),
(42, 1, 2, 'reg', '2025-02-10 16:23:45', 'non_lu', NULL),
(43, 1, 2, 'dfvdfv', '2025-02-10 16:23:47', 'non_lu', NULL),
(44, 1, 2, 'sdvd', '2025-02-10 16:24:21', 'non_lu', NULL),
(45, 1, 2, 'fefr', '2025-02-10 16:25:04', 'non_lu', NULL),
(46, 1, 2, 'frfrf', '2025-02-10 16:25:12', 'non_lu', NULL),
(47, 1, 2, 'braaa', '2025-02-10 16:25:21', 'non_lu', NULL),
(48, 1, 2, 'yth', '2025-02-10 16:25:53', 'non_lu', NULL),
(49, 1, 2, 'fhfhf', '2025-02-10 16:25:55', 'non_lu', NULL),
(50, 2, 1, 'petite salope que t \r\n', '2025-02-10 16:34:01', 'non_lu', NULL),
(51, 1, 2, 'humm ah bah c screen', '2025-02-10 16:34:54', 'non_lu', NULL),
(52, 1, 2, 'oiolgok', '2025-02-10 16:41:06', 'non_lu', NULL),
(53, 1, 2, 'salammm', '2025-02-10 16:45:25', 'non_lu', NULL),
(54, 1, 2, 'salutt\r\n', '2025-02-10 17:11:16', 'non_lu', NULL),
(55, 2, 1, 'z', '2025-02-10 17:11:48', 'non_lu', NULL),
(56, 2, 1, 'zdzd', '2025-02-10 17:11:52', 'non_lu', NULL),
(57, 2, 1, 'xqxsq', '2025-02-10 17:14:29', 'non_lu', NULL),
(58, 1, 2, 'heinn\r\n', '2025-02-10 17:15:00', 'non_lu', NULL);

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
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `offres_stages`
--

INSERT INTO `offres_stages` (`id`, `entreprise_id`, `titre`, `description`, `date_debut`, `date_fin`, `lieu`, `mode_stage`, `logo`) VALUES
(30, 2, 'Stage numéro 1 en informatique', 'juuuu', '2025-02-05', '2025-02-28', 'nevers , 45 rue des boloss', 'présentiel', 'uploads/logos/202303-7-1.jpg'),
(31, 4, 'Stage numéro 6 en informatique', 'aaa', '2025-02-06', '2026-12-25', 'nevers , 45 rue des boloss', 'distanciel', NULL),
(32, 5, 'Améliorations Front-End', 'Il s\'agirait d\'améliorer les fonctionnalités non finis en front end pour un design pour simple.', '2025-02-15', '2025-05-02', 'QG Quizzine', 'présentiel', 'uploads/logos/logo_quizzine_petit.png'),
(33, 5, 'Entretient du Back-End', 'Gérer l\'entretient du back-end durant', '2025-03-20', '2025-06-18', '', 'distanciel', 'uploads/logos/logo_quizzine_petit.png');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;
--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `offres_stages`
--
ALTER TABLE `offres_stages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
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
