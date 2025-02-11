-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Mar 11 Février 2025 à 08:26
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

--
-- Index pour les tables exportées
--

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expediteur_id` (`expediteur_id`),
  ADD KEY `destinataire_id` (`destinataire_id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
