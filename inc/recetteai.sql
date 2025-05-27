-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : dim. 25 mai 2025 à 12:56
-- Version du serveur : 8.0.30
-- Version de PHP : 8.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `recetteai`
--
CREATE DATABASE IF NOT EXISTS `recetteai` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `recetteai`;

-- --------------------------------------------------------

--
-- Structure de la table `administrateur`
--

CREATE TABLE `administrateur` (
  `id` int NOT NULL,
  `nom` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `prenom` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `date_inscription` datetime DEFAULT CURRENT_TIMESTAMP,
  `role` enum('superadmin','moderateur','editeur') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'moderateur'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `administrateur`
--

INSERT INTO `administrateur` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `date_inscription`, `role`) VALUES
(1, 'GRAZIANI', 'Alexandre', 'graziani1112@gmail.com', '$2y$12$YlekwmMnL55WNeMSXA5Op.k6kcJhHik9C/Lo.7Ef5G27WWE9K2wSa', '2025-05-15 15:07:47', 'superadmin'),
(6, 'LAUDET', 'Chloé', 'chloelaudet@gmail.com', '$2y$12$eeUwRC11h2dc7aevOEnoO.h9upfCuckNOXwdzGsG1Wi2AHpsYFxem', '2025-05-15 23:31:32', 'editeur'),
(7, 'GRAZIANI', 'Tiffany', 'tiffany@gmail.com', '$2y$12$Z.60G2Yypw5eKu5y6fzMWOLRlCrpAgwdnIXCaFDqxprt4iiFIZvYe', '2025-05-15 23:32:04', 'moderateur'),
(9, 'MISERI', 'Argentina', 'argentina@gmail.com', '$2y$12$5NkS1iBmcjUS9CtbmF1l6.oE.SkCpZzF0dwPTgzVqYMNryKZ9.7pO', '2025-05-17 19:15:16', 'editeur'),
(10, 'GRAZIANI', 'Elodie', 'elodie@gmail.com', '$2y$12$K20Fh/DHX3TkpOuOPsHwqulz/5wV7lWfm6vAUvBRVtr8NNbjOt.Hi', '2025-05-17 19:16:31', 'moderateur'),
(11, 'SEYLER', 'Christine', 'christine@gmail.com', '$2y$12$Hk8hA/.e.w6kbZyrR/cjyeafzNQsucKCREj04xQNGBiYNosdB.LCu', '2025-05-17 19:17:05', 'moderateur'),
(13, 'BOTTI', 'Emma', 'emma@gmail.com', '$2y$12$egCVVtbboxJCVRiuLE3BFu2IEQeMMduwjFpACIzNt/Key0srlLVsu', '2025-05-19 11:47:41', 'moderateur');

-- --------------------------------------------------------

--
-- Structure de la table `administrateur_actions`
--

CREATE TABLE `administrateur_actions` (
  `id` int NOT NULL,
  `id_admin` int NOT NULL,
  `table_modifiee` enum('utilisateur','administrateur','recette','ingredient','categorie','etiquette') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_element` int NOT NULL,
  `action` enum('ajout','modification','suppression') COLLATE utf8mb4_general_ci NOT NULL,
  `date_action` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `administrateur_actions`
--

INSERT INTO `administrateur_actions` (`id`, `id_admin`, `table_modifiee`, `id_element`, `action`, `date_action`) VALUES
(1, 1, 'administrateur', 4, 'suppression', '2025-05-15 23:30:41'),
(2, 1, 'administrateur', 6, 'ajout', '2025-05-15 23:31:32'),
(3, 1, 'administrateur', 7, 'ajout', '2025-05-15 23:32:04'),
(4, 1, 'administrateur', 6, 'modification', '2025-05-15 23:36:36'),
(5, 1, 'administrateur', 6, 'modification', '2025-05-15 23:36:44'),
(6, 1, 'administrateur', 6, 'modification', '2025-05-15 23:36:57'),
(7, 1, 'administrateur', 7, 'modification', '2025-05-15 23:37:05'),
(8, 1, 'administrateur', 7, 'modification', '2025-05-15 23:37:27'),
(9, 1, 'recette', 4, 'modification', '2025-05-16 00:14:23'),
(10, 1, 'recette', 4, 'modification', '2025-05-16 00:17:27'),
(11, 1, 'ingredient', 86, 'ajout', '2025-05-16 14:20:59'),
(12, 1, 'ingredient', 87, 'ajout', '2025-05-16 14:22:05'),
(13, 1, 'ingredient', 87, 'suppression', '2025-05-16 14:22:19'),
(14, 1, 'ingredient', 83, 'modification', '2025-05-16 14:32:36'),
(15, 1, 'ingredient', 88, 'ajout', '2025-05-16 14:32:46'),
(16, 1, 'ingredient', 83, 'suppression', '2025-05-16 14:33:03'),
(17, 1, 'ingredient', 88, 'modification', '2025-05-16 14:33:16'),
(18, 1, 'ingredient', 88, 'modification', '2025-05-16 14:33:28'),
(19, 1, 'categorie', 12, 'modification', '2025-05-16 15:07:22'),
(20, 1, 'categorie', 4, 'modification', '2025-05-16 15:09:05'),
(21, 1, 'categorie', 17, 'modification', '2025-05-16 15:09:24'),
(22, 1, 'categorie', 17, 'modification', '2025-05-16 15:09:54'),
(23, 1, 'categorie', 3, 'modification', '2025-05-16 15:10:29'),
(24, 1, 'categorie', 7, 'modification', '2025-05-16 15:10:39'),
(25, 1, 'categorie', 1, 'modification', '2025-05-16 15:11:02'),
(26, 1, 'categorie', 2, 'modification', '2025-05-16 15:11:19'),
(27, 1, 'categorie', 8, 'modification', '2025-05-16 15:11:52'),
(28, 1, 'categorie', 10, 'modification', '2025-05-16 15:12:17'),
(29, 1, 'categorie', 6, 'modification', '2025-05-16 15:12:34'),
(30, 1, 'categorie', 11, 'modification', '2025-05-16 15:12:45'),
(31, 1, 'categorie', 5, 'modification', '2025-05-16 15:13:21'),
(32, 1, 'categorie', 14, 'modification', '2025-05-16 15:13:43'),
(33, 1, 'categorie', 13, 'modification', '2025-05-16 15:14:02'),
(34, 1, 'categorie', 9, 'modification', '2025-05-16 15:14:30'),
(35, 1, 'categorie', 16, 'modification', '2025-05-16 15:14:50'),
(36, 1, 'categorie', 15, 'modification', '2025-05-16 15:15:06'),
(37, 1, 'categorie', 11, 'modification', '2025-05-16 15:16:18'),
(38, 1, 'etiquette', 30, 'modification', '2025-05-16 15:28:07'),
(39, 1, 'administrateur', 1, 'modification', '2025-05-17 18:16:10'),
(40, 1, 'administrateur', 1, 'modification', '2025-05-17 18:28:31'),
(41, 1, 'administrateur', 1, 'modification', '2025-05-17 18:30:08'),
(42, 1, 'administrateur', 1, 'modification', '2025-05-17 18:30:15'),
(43, 1, 'administrateur', 1, 'modification', '2025-05-17 18:33:29'),
(44, 1, 'administrateur', 1, 'modification', '2025-05-17 18:33:34'),
(45, 1, 'administrateur', 1, 'modification', '2025-05-17 18:33:41'),
(46, 1, 'administrateur', 8, 'ajout', '2025-05-17 18:37:46'),
(47, 1, 'administrateur', 8, 'suppression', '2025-05-17 18:40:22'),
(48, 1, 'administrateur', 1, 'modification', '2025-05-17 18:40:31'),
(49, 1, 'administrateur', 1, 'modification', '2025-05-17 18:40:44'),
(50, 1, 'administrateur', 1, 'modification', '2025-05-17 18:40:49'),
(51, 1, 'administrateur', 1, 'modification', '2025-05-17 18:40:59'),
(52, 1, 'administrateur', 1, 'modification', '2025-05-17 18:41:12'),
(53, 1, 'administrateur', 1, 'modification', '2025-05-17 18:52:38'),
(54, 1, 'administrateur', 1, 'modification', '2025-05-17 19:12:03'),
(55, 1, 'administrateur', 1, 'modification', '2025-05-17 19:12:28'),
(56, 1, 'administrateur', 1, 'modification', '2025-05-17 19:12:51'),
(57, 1, 'administrateur', 1, 'modification', '2025-05-17 19:13:35'),
(58, 1, 'administrateur', 1, 'modification', '2025-05-17 19:14:15'),
(59, 1, 'administrateur', 6, 'modification', '2025-05-17 19:14:35'),
(60, 1, 'administrateur', 9, 'ajout', '2025-05-17 19:15:16'),
(61, 1, 'administrateur', 10, 'ajout', '2025-05-17 19:16:31'),
(62, 1, 'administrateur', 11, 'ajout', '2025-05-17 19:17:05'),
(63, 1, 'administrateur', 9, 'modification', '2025-05-17 19:18:21'),
(64, 1, 'administrateur', 7, 'modification', '2025-05-17 19:35:52'),
(65, 1, 'administrateur', 7, 'modification', '2025-05-17 19:38:24'),
(66, 1, 'administrateur', 7, 'modification', '2025-05-17 19:42:38'),
(67, 1, 'administrateur', 7, 'modification', '2025-05-17 19:44:11'),
(68, 1, 'administrateur', 12, 'ajout', '2025-05-17 19:46:38'),
(69, 1, 'administrateur', 12, 'suppression', '2025-05-17 19:46:43'),
(70, 1, 'administrateur', 7, 'modification', '2025-05-17 19:51:01'),
(71, 1, 'administrateur', 7, 'modification', '2025-05-17 19:56:45'),
(72, 7, 'categorie', 18, 'ajout', '2025-05-17 21:06:35'),
(73, 7, 'categorie', 18, 'modification', '2025-05-17 21:07:03'),
(74, 7, 'categorie', 18, 'suppression', '2025-05-17 21:07:33'),
(75, 7, 'etiquette', 34, 'ajout', '2025-05-17 21:31:07'),
(76, 7, 'etiquette', 34, 'modification', '2025-05-17 21:31:34'),
(77, 7, 'ingredient', 90, 'ajout', '2025-05-17 21:35:29'),
(78, 7, 'ingredient', 91, 'ajout', '2025-05-17 21:35:32'),
(79, 7, 'ingredient', 92, 'ajout', '2025-05-17 21:35:34'),
(80, 7, 'ingredient', 93, 'ajout', '2025-05-17 21:35:36'),
(81, 7, 'ingredient', 94, 'ajout', '2025-05-17 21:35:37'),
(82, 7, 'ingredient', 95, 'ajout', '2025-05-17 21:35:39'),
(83, 7, 'ingredient', 96, 'ajout', '2025-05-17 21:35:42'),
(84, 7, 'ingredient', 97, 'ajout', '2025-05-17 21:36:24'),
(85, 7, 'ingredient', 90, 'modification', '2025-05-17 21:36:47'),
(86, 7, 'ingredient', 90, 'suppression', '2025-05-17 21:36:52'),
(87, 7, 'ingredient', 94, 'suppression', '2025-05-17 21:36:59'),
(88, 7, 'ingredient', 93, 'suppression', '2025-05-17 21:37:05'),
(89, 7, 'ingredient', 91, 'suppression', '2025-05-17 21:37:09'),
(90, 7, 'ingredient', 95, 'suppression', '2025-05-17 21:37:13'),
(91, 7, 'ingredient', 97, 'suppression', '2025-05-17 21:37:42'),
(92, 7, 'ingredient', 96, 'suppression', '2025-05-17 21:37:46'),
(93, 7, 'ingredient', 92, 'suppression', '2025-05-17 21:38:00'),
(94, 1, 'recette', 11, 'ajout', '2025-05-17 22:21:30'),
(95, 1, 'recette', 11, 'suppression', '2025-05-17 22:31:16'),
(96, 1, 'administrateur', 13, 'ajout', '2025-05-19 11:47:41'),
(97, 1, 'recette', 1, 'modification', '2025-05-19 14:51:29'),
(98, 1, 'recette', 2, 'modification', '2025-05-19 15:01:37'),
(99, 1, 'recette', 3, 'modification', '2025-05-19 15:03:20'),
(100, 1, 'recette', 4, 'modification', '2025-05-19 15:03:46'),
(101, 1, 'recette', 5, 'modification', '2025-05-19 15:04:13'),
(102, 1, 'recette', 6, 'modification', '2025-05-19 15:04:56'),
(103, 1, 'recette', 7, 'modification', '2025-05-19 15:05:34'),
(104, 1, 'recette', 8, 'modification', '2025-05-19 15:06:21'),
(105, 1, 'recette', 9, 'modification', '2025-05-19 15:07:38'),
(106, 1, 'recette', 10, 'modification', '2025-05-19 15:08:56'),
(107, 1, 'recette', 2, 'modification', '2025-05-19 15:09:44'),
(108, 1, 'categorie', 12, 'modification', '2025-05-19 15:35:58'),
(109, 1, 'categorie', 7, 'modification', '2025-05-19 15:36:11'),
(110, 1, 'categorie', 3, 'modification', '2025-05-19 15:36:24'),
(111, 1, 'categorie', 17, 'modification', '2025-05-19 15:36:41'),
(112, 1, 'categorie', 4, 'modification', '2025-05-19 15:36:52'),
(113, 1, 'categorie', 1, 'modification', '2025-05-19 15:37:01'),
(114, 1, 'categorie', 8, 'modification', '2025-05-19 15:37:10'),
(115, 1, 'categorie', 2, 'modification', '2025-05-19 15:37:18'),
(116, 1, 'categorie', 10, 'modification', '2025-05-19 15:38:11'),
(117, 1, 'categorie', 6, 'modification', '2025-05-19 15:38:19'),
(118, 1, 'categorie', 11, 'modification', '2025-05-19 15:38:46'),
(119, 1, 'categorie', 16, 'modification', '2025-05-19 15:38:53'),
(120, 1, 'categorie', 15, 'modification', '2025-05-19 15:39:13'),
(121, 1, 'categorie', 10, 'modification', '2025-05-19 15:39:23'),
(122, 1, 'categorie', 6, 'modification', '2025-05-19 15:39:32'),
(123, 1, 'categorie', 5, 'modification', '2025-05-19 15:39:47'),
(124, 1, 'categorie', 9, 'modification', '2025-05-19 15:39:55'),
(125, 1, 'categorie', 14, 'modification', '2025-05-19 15:40:04'),
(126, 1, 'categorie', 13, 'modification', '2025-05-19 15:40:12'),
(127, 1, 'categorie', 3, 'modification', '2025-05-19 15:40:18'),
(129, 1, 'recette', 12, 'suppression', '2025-05-20 20:44:17'),
(130, 1, 'administrateur', 14, 'suppression', '2025-05-20 20:44:49'),
(131, 1, 'ingredient', 99, 'ajout', '2025-05-20 20:45:32'),
(132, 1, 'ingredient', 100, 'ajout', '2025-05-20 20:45:39'),
(133, 1, 'ingredient', 101, 'ajout', '2025-05-20 20:45:51'),
(134, 1, 'ingredient', 102, 'ajout', '2025-05-20 20:45:59'),
(135, 1, 'ingredient', 102, 'suppression', '2025-05-20 20:46:14'),
(136, 1, 'ingredient', 103, 'ajout', '2025-05-20 20:46:25'),
(137, 1, 'ingredient', 104, 'ajout', '2025-05-20 20:46:30'),
(138, 1, 'ingredient', 105, 'ajout', '2025-05-20 20:46:34'),
(139, 1, 'ingredient', 106, 'ajout', '2025-05-20 20:46:49'),
(140, 1, 'ingredient', 107, 'ajout', '2025-05-20 20:57:41'),
(141, 1, 'ingredient', 108, 'ajout', '2025-05-20 20:57:53'),
(142, 1, 'ingredient', 109, 'ajout', '2025-05-20 20:58:02'),
(143, 1, 'ingredient', 110, 'ajout', '2025-05-20 20:58:10'),
(144, 1, 'recette', 13, 'ajout', '2025-05-21 15:19:19'),
(145, 1, 'categorie', 19, 'ajout', '2025-05-22 16:20:13'),
(146, 1, 'ingredient', 98, 'suppression', '2025-05-22 16:21:21'),
(147, 1, 'administrateur', 11, 'modification', '2025-05-22 16:24:45'),
(148, 1, 'categorie', 19, 'suppression', '2025-05-25 13:16:52');

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `id` int NOT NULL,
  `id_admin` int NOT NULL,
  `nom` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `descriptif` text COLLATE utf8mb4_general_ci,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `couleur` varchar(7) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '#FFFFFF',
  `couleurTexte` enum('#FFFFFF','#121212') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '#FFFFFF',
  `image_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id`, `id_admin`, `nom`, `descriptif`, `date_creation`, `couleur`, `couleurTexte`, `image_url`) VALUES
(1, 1, 'Cuisine Française', 'Plats traditionnels français', '2025-03-17 21:22:16', '#ff5733', '#FFFFFF', 'public/assets/img/categories/categorie_1_1747401062.webp'),
(2, 1, 'Cuisine Italienne', 'Spécialités italiennes (pâtes, pizza...)', '2025-03-17 21:22:16', '#ffbd33', '#121212', 'public/assets/img/categories/categorie_2_1747401079.webp'),
(3, 1, 'Cuisine Asiatique', 'Cuisine chinoise, japonaise, thaï...', '2025-03-17 21:22:16', '#06d6a0', '#121212', 'public/assets/img/categories/categorie_3_1747401029.webp'),
(4, 1, 'Cuisine Espagnole', 'Tapas, paellas, tortillas', '2025-03-17 21:22:16', '#ffd700', '#121212', 'public/assets/img/categories/categorie_4_1747400945.webp'),
(5, 1, 'Cuisine Tunisienne', 'Couscous, briks, ojja...', '2025-03-17 21:22:16', '#e63946', '#FFFFFF', 'public/assets/img/categories/categorie_5_1747401201.webp'),
(6, 1, 'Cuisine Marocaine', 'Tajines, couscous, pastilla...', '2025-03-17 21:22:16', '#8d4a3b', '#FFFFFF', 'public/assets/img/categories/categorie_6_1747401153.webp'),
(7, 1, 'Cuisine Algérienne', 'Chorba, couscous, bourek...', '2025-03-17 21:22:16', '#d4a373', '#FFFFFF', 'public/assets/img/categories/categorie_7_1747401039.webp'),
(8, 1, 'Cuisine Grecque', 'Moussaka, souvlaki, tzatziki...', '2025-03-17 21:22:16', '#0077b6', '#FFFFFF', 'public/assets/img/categories/categorie_8_1747401112.webp'),
(9, 1, 'Cuisine Turque', 'Döner, baklava, börek...', '2025-03-17 21:22:16', '#d62828', '#FFFFFF', 'public/assets/img/categories/categorie_9_1747401270.webp'),
(10, 1, 'Cuisine Libanaise', 'Mezzés, houmous, falafel...', '2025-03-17 21:22:16', '#f77f00', '#FFFFFF', 'public/assets/img/categories/categorie_10_1747401137.webp'),
(11, 1, 'Cuisine Mexicaine', 'Tacos, quesadillas, enchiladas...', '2025-03-17 21:22:16', '#d7263d', '#FFFFFF', 'public/assets/img/categories/categorie_11_1747401378.webp'),
(12, 1, 'Cuisine Africaine', 'Cuisine de l’Afrique subsaharienne (Thieboudienne, Mafé...)', '2025-03-17 21:22:16', '#bf8b67', '#FFFFFF', 'public/assets/img/categories/categorie_12_1747400842.webp'),
(13, 1, 'Cuisine Végétarienne', 'Plats sans viande ni poisson', '2025-03-17 21:22:16', '#2ecc71', '#121212', 'public/assets/img/categories/categorie_13_1747401242.webp'),
(14, 1, 'Cuisine Vegan', 'Plats sans produits animaux', '2025-03-17 21:22:16', '#00b894', '#121212', 'public/assets/img/categories/categorie_14_1747401223.webp'),
(15, 1, 'Cuisine Sans Gluten', 'Adaptée aux intolérants', '2025-03-17 21:22:16', '#ffc300', '#121212', 'public/assets/img/categories/categorie_15_1747401306.webp'),
(16, 1, 'Cuisine Rapide &amp;amp; Facile', 'Recettes prêtes en &amp;lt; 30 min', '2025-03-17 21:22:16', '#6c757d', '#FFFFFF', 'public/assets/img/categories/categorie_16_1747401290.webp'),
(17, 1, 'Cuisine des Fêtes', 'Plats festifs (Noël, Ramadan, Anniversaire, Pâques, Aïd...)', '2025-03-17 21:22:16', '#ffffff', '#121212', 'public/assets/img/categories/categorie_17_1747400994.webp');

-- --------------------------------------------------------

--
-- Structure de la table `etiquette`
--

CREATE TABLE `etiquette` (
  `id` int NOT NULL,
  `id_admin` int NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `descriptif` text COLLATE utf8mb4_general_ci,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `etiquette`
--

INSERT INTO `etiquette` (`id`, `id_admin`, `nom`, `descriptif`, `date_creation`) VALUES
(1, 1, 'Entrées froides', 'Apéritifs et salades fraîches', '2025-03-17 21:35:06'),
(2, 1, 'Entrées chaudes', 'Soupes et plats chauds', '2025-03-17 21:35:06'),
(3, 1, 'Plats principaux', 'Recettes complètes', '2025-03-17 21:35:06'),
(4, 1, 'Accompagnements', 'Riz, légumes, purées...', '2025-03-17 21:35:06'),
(5, 1, 'Desserts rapides', 'Préparation en moins de 15 min', '2025-03-17 21:35:06'),
(6, 1, 'Desserts gourmands', 'Pâtisseries et sucreries', '2025-03-17 21:35:06'),
(7, 1, 'Boissons', 'Jus, smoothies, cocktails', '2025-03-17 21:35:06'),
(8, 1, 'Pain & Boulangerie', 'Recettes de pains et viennoiseries', '2025-03-17 21:35:06'),
(9, 1, 'Sans cuisson', 'Recettes crues', '2025-03-17 21:35:06'),
(10, 1, 'Cuisson au four', 'Plats, gâteaux, gratins', '2025-03-17 21:35:06'),
(11, 1, 'Cuisson à la vapeur', 'Légumes, poissons', '2025-03-17 21:35:06'),
(12, 1, 'Cuisson à la poêle', 'Viandes, sautés', '2025-03-17 21:35:06'),
(13, 1, 'Cuisson mijotée', 'Plats en sauce, ragoûts', '2025-03-17 21:35:06'),
(14, 1, 'Cuisson rapide', 'Préparation en moins de 15 min', '2025-03-17 21:35:06'),
(15, 1, 'Végétarien', 'Sans viande ni poisson', '2025-03-17 21:35:06'),
(16, 1, 'Vegan', 'Sans produits animaux', '2025-03-17 21:35:06'),
(17, 1, 'Sans gluten', 'Adapté aux intolérants', '2025-03-17 21:35:06'),
(18, 1, 'Sans lactose', 'Sans produits laitiers', '2025-03-17 21:35:06'),
(19, 1, 'Keto (Cétogène)', 'Faible en glucides', '2025-03-17 21:35:06'),
(20, 1, 'Paleo', 'Alimentation ancestrale	(salade de noix, viande grillée, etc)', '2025-03-17 21:35:06'),
(21, 1, 'Recettes express', 'Préparation en moins de 15 min', '2025-03-17 21:35:06'),
(22, 1, 'Repas du soir', 'Plats légers et rapides', '2025-03-17 21:35:06'),
(23, 1, 'Petit-déjeuner', 'Matin gourmand ou rapide', '2025-03-17 21:35:06'),
(24, 1, 'Brunch', 'Idéal pour le week-end', '2025-03-17 21:35:06'),
(25, 1, 'Cuisine festive', 'Plats pour Noël, anniversaires...', '2025-03-17 21:35:06'),
(26, 1, 'Recettes estivales', 'Légères et rafraîchissantes', '2025-03-17 21:35:06'),
(27, 1, 'Recettes hivernales', 'Plats réconfortants', '2025-03-17 21:35:06'),
(28, 1, 'À base de poulet', 'Viandes blanches', '2025-03-17 21:35:06'),
(29, 1, 'À base de poisson', 'Poissons et fruits de mer', '2025-03-17 21:35:06'),
(30, 1, 'À base d’oeufs', 'Plats avec des oeufs', '2025-03-17 21:35:06'),
(31, 1, 'À base de fromage', 'Fromage en ingrédient clé', '2025-03-17 21:35:06'),
(32, 1, 'À base de chocolat', 'Gourmandises chocolatées', '2025-03-17 21:35:06'),
(33, 1, 'À base de légumes', 'Recettes végétales', '2025-03-17 21:35:06'),
(34, 7, 'à base de sauce', 'Tout type de plat en sauce', '2025-05-17 21:31:07');

-- --------------------------------------------------------

--
-- Structure de la table `ingredient`
--

CREATE TABLE `ingredient` (
  `id` int NOT NULL,
  `id_admin` int DEFAULT NULL,
  `nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ingredient`
--

INSERT INTO `ingredient` (`id`, `id_admin`, `nom`, `date_creation`) VALUES
(1, NULL, 'farine', '2025-05-13 11:16:13'),
(2, NULL, 'sucre', '2025-05-13 11:16:13'),
(3, NULL, 'sel', '2025-05-13 11:16:13'),
(4, NULL, 'poivre', '2025-05-13 11:16:13'),
(5, NULL, 'beurre', '2025-05-13 11:16:13'),
(6, NULL, 'huile d\'olive', '2025-05-13 11:16:13'),
(7, NULL, 'huile de tournesol', '2025-05-13 11:16:13'),
(8, NULL, 'lait', '2025-05-13 11:16:13'),
(10, NULL, 'levure chimique', '2025-05-13 11:16:13'),
(11, NULL, 'levure boulangère', '2025-05-13 11:16:13'),
(12, NULL, 'bicarbonate de soude', '2025-05-13 11:16:13'),
(13, NULL, 'tomate', '2025-05-13 11:16:13'),
(14, NULL, 'oignon', '2025-05-13 11:16:13'),
(15, NULL, 'ail', '2025-05-13 11:16:13'),
(16, NULL, 'pomme de terre', '2025-05-13 11:16:13'),
(17, NULL, 'carotte', '2025-05-13 11:16:13'),
(18, NULL, 'courgette', '2025-05-13 11:16:13'),
(19, NULL, 'poivron', '2025-05-13 11:16:13'),
(20, NULL, 'champignon de paris', '2025-05-13 11:16:13'),
(21, NULL, 'fromage râpé', '2025-05-13 11:16:13'),
(22, NULL, 'crème fraîche', '2025-05-13 11:16:13'),
(23, NULL, 'crème liquide', '2025-05-13 11:16:13'),
(24, NULL, 'viande hachée', '2025-05-13 11:16:13'),
(25, NULL, 'poulet', '2025-05-13 11:16:13'),
(26, NULL, 'jambon', '2025-05-13 11:16:13'),
(27, NULL, 'lardon', '2025-05-13 11:16:13'),
(28, NULL, 'saumon', '2025-05-13 11:16:13'),
(29, NULL, 'thon', '2025-05-13 11:16:13'),
(30, NULL, 'riz', '2025-05-13 11:16:13'),
(31, NULL, 'pâtes', '2025-05-13 11:16:13'),
(32, NULL, 'spaghetti', '2025-05-13 11:16:13'),
(33, NULL, 'penne', '2025-05-13 11:16:13'),
(34, NULL, 'eau', '2025-05-13 11:16:13'),
(35, NULL, 'bouillon de volaille', '2025-05-13 11:16:13'),
(36, NULL, 'bouillon de légumes', '2025-05-13 11:16:13'),
(37, NULL, 'bouillon de bœuf', '2025-05-13 11:16:13'),
(38, NULL, 'curry', '2025-05-13 11:16:13'),
(39, NULL, 'paprika', '2025-05-13 11:16:13'),
(40, NULL, 'thym', '2025-05-13 11:16:13'),
(41, NULL, 'basilic', '2025-05-13 11:16:13'),
(42, NULL, 'persil', '2025-05-13 11:16:13'),
(43, NULL, 'coriandre', '2025-05-13 11:16:13'),
(44, NULL, 'cumin', '2025-05-13 11:16:13'),
(45, NULL, 'cannelle', '2025-05-13 11:16:13'),
(46, NULL, 'girofle', '2025-05-13 11:16:13'),
(47, NULL, 'vanille', '2025-05-13 11:16:13'),
(48, NULL, 'chocolat noir', '2025-05-13 11:16:13'),
(49, NULL, 'chocolat au lait', '2025-05-13 11:16:13'),
(50, NULL, 'sucre vanillé', '2025-05-13 11:16:13'),
(51, NULL, 'miel', '2025-05-13 11:16:13'),
(52, NULL, 'sirop d\'érable', '2025-05-13 11:16:13'),
(53, NULL, 'pain', '2025-05-13 11:16:13'),
(54, NULL, 'pain de mie', '2025-05-13 11:16:13'),
(55, NULL, 'yaourt nature', '2025-05-13 11:16:13'),
(56, NULL, 'yaourt grec', '2025-05-13 11:16:13'),
(57, NULL, 'maïzena', '2025-05-13 11:16:13'),
(58, NULL, 'noix', '2025-05-13 11:16:13'),
(59, NULL, 'noisettes', '2025-05-13 11:16:13'),
(60, NULL, 'amandes', '2025-05-13 11:16:13'),
(61, NULL, 'lentilles', '2025-05-13 11:16:13'),
(62, NULL, 'pois chiches', '2025-05-13 11:16:13'),
(63, NULL, 'haricots rouges', '2025-05-13 11:16:13'),
(64, NULL, 'épinards', '2025-05-13 11:16:13'),
(65, NULL, 'petits pois', '2025-05-13 11:16:13'),
(66, NULL, 'brocoli', '2025-05-13 11:16:13'),
(67, NULL, 'chou-fleur', '2025-05-13 11:16:13'),
(68, NULL, 'salade', '2025-05-13 11:16:13'),
(69, NULL, 'concombre', '2025-05-13 11:16:13'),
(70, NULL, 'avocat', '2025-05-13 11:16:13'),
(71, NULL, 'pâte brisée', '2025-05-13 11:45:17'),
(76, NULL, 'pâte à pizza', '2025-05-13 11:51:17'),
(77, NULL, 'sauce tomate', '2025-05-13 11:51:17'),
(78, NULL, 'mozzarella', '2025-05-13 11:51:17'),
(80, NULL, 'nouilles', '2025-05-14 09:18:06'),
(81, NULL, 'germes de soja', '2025-05-14 09:18:06'),
(82, NULL, 'sauce tamarin', '2025-05-14 09:18:06'),
(84, NULL, 'merguez', '2025-05-14 09:47:30'),
(85, NULL, 'oeuf', '2025-05-14 09:47:30'),
(86, 1, 'banane', '2025-05-16 14:20:59'),
(88, 1, 'cacahuète', '2025-05-16 14:32:46'),
(89, NULL, 'voiture', '2025-05-16 16:42:41'),
(99, 1, 'farine de blé', '2025-05-20 20:45:32'),
(100, 1, 'farine de sarrasin', '2025-05-20 20:45:39'),
(101, 1, 'olive', '2025-05-20 20:45:51'),
(103, 1, 'fraise', '2025-05-20 20:46:25'),
(104, 1, 'pêche', '2025-05-20 20:46:30'),
(105, 1, 'nectarine', '2025-05-20 20:46:34'),
(106, 1, 'pomme', '2025-05-20 20:46:49'),
(107, 1, 'sucre de canne', '2025-05-20 20:57:41'),
(108, 1, 'dorade', '2025-05-20 20:57:53'),
(109, 1, 'semoule fine', '2025-05-20 20:58:02'),
(110, 1, 'semoule', '2025-05-20 20:58:10');

-- --------------------------------------------------------

--
-- Structure de la table `liste_personnelle_ingredients`
--

CREATE TABLE `liste_personnelle_ingredients` (
  `id_utilisateur` int NOT NULL,
  `id_ingredient` int NOT NULL,
  `quantite` decimal(10,2) NOT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Déchargement des données de la table `liste_personnelle_ingredients`
--

INSERT INTO `liste_personnelle_ingredients` (`id_utilisateur`, `id_ingredient`, `quantite`, `date_creation`) VALUES
(1, 5, 1.00, '2025-05-24 21:01:50'),
(1, 8, 1.00, '2025-05-24 21:01:50'),
(1, 12, 1.00, '2025-05-24 21:01:50'),
(1, 13, 1.00, '2025-05-24 21:01:50'),
(1, 18, 1.00, '2025-05-24 21:01:50'),
(1, 41, 1.00, '2025-05-24 21:01:50'),
(1, 45, 1.00, '2025-05-24 21:01:50'),
(1, 60, 1.00, '2025-05-24 21:01:50'),
(1, 70, 1.00, '2025-05-24 21:01:50'),
(1, 77, 1.00, '2025-05-24 21:01:50'),
(1, 88, 1.00, '2025-05-24 21:01:50'),
(6, 41, 1.00, '2025-05-22 15:42:33'),
(7, 77, 1.00, '2025-05-22 16:34:16');

-- --------------------------------------------------------

--
-- Structure de la table `liste_recette_ingredients`
--

CREATE TABLE `liste_recette_ingredients` (
  `id_recette` int NOT NULL,
  `id_ingredient` int NOT NULL,
  `quantite` decimal(10,2) NOT NULL,
  `id_unite` int DEFAULT NULL
) ;

--
-- Déchargement des données de la table `liste_recette_ingredients`
--

INSERT INTO `liste_recette_ingredients` (`id_recette`, `id_ingredient`, `quantite`, `id_unite`) VALUES
(1, 23, 300.00, 5),
(1, 27, 50.00, 1),
(1, 71, 1.00, 7),
(1, 85, 3.00, 7),
(2, 41, 3.00, 18),
(2, 77, 30.00, 5),
(2, 78, 1.00, 7),
(4, 14, 3.00, 7),
(4, 15, 7.00, 17),
(4, 30, 500.00, 1),
(6, 3, 1.00, 9),
(6, 14, 3.00, 7),
(6, 24, 300.00, 1),
(6, 51, 150.00, 5),
(6, 60, 200.00, 1),
(13, 1, 250.00, 1),
(13, 4, 1.00, 9),
(13, 5, 1.00, 9),
(13, 6, 15.00, 6),
(13, 11, 1.00, 13),
(13, 21, 150.00, 1),
(13, 26, 200.00, 1),
(13, 85, 4.00, 7),
(13, 101, 200.00, 1);

-- --------------------------------------------------------

--
-- Structure de la table `recette`
--

CREATE TABLE `recette` (
  `id` int NOT NULL,
  `id_admin` int NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `descriptif` text COLLATE utf8mb4_general_ci,
  `instructions` text COLLATE utf8mb4_general_ci,
  `image_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `temps_preparation` int NOT NULL,
  `temps_cuisson` int NOT NULL,
  `difficulte` enum('facile','moyenne','difficile') COLLATE utf8mb4_general_ci NOT NULL,
  `id_categorie` int NOT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Déchargement des données de la table `recette`
--

INSERT INTO `recette` (`id`, `id_admin`, `nom`, `descriptif`, `instructions`, `image_url`, `temps_preparation`, `temps_cuisson`, `difficulte`, `id_categorie`, `date_creation`) VALUES
(1, 1, 'Quiche Lorraine', 'Tarte salée française à base de lardons, œufs et crème fraîche.', '##1. Préchauffez le four à 180°C.\r\n##2. Étalez la pâte dans un moule.\r\n##3. Faites revenir les lardons.\r\n##4. Battez les œufs avec la crème.\r\n##5. Disposez les lardons, versez le mélange.\r\n##6. Enfournez 35 min.', 'images/recette_1_1747659089.jpg', 15, 35, 'facile', 1, '2025-03-23 16:32:38'),
(2, 1, 'Pizza Margherita', 'La plus classique des pizzas italiennes, à base de tomate, mozzarella et basilic.', '##1. Préchauffez le four à 220°C.\r\n##2. Étalez la pâte.\r\n##3. Étalez la sauce tomate.\r\n##4. Ajoutez la mozzarella en tranches.\r\n##5. Cuire 12 minutes.\r\n##6. Ajoutez le basilic frais à la sortie du four.', 'images/recette_2_1747659697.jpg', 20, 12, 'facile', 2, '2025-03-23 16:32:38'),
(3, 1, 'Pad Thaï au Poulet', 'Plat thaïlandais sucré-salé à base de nouilles de riz, poulet, œufs et cacahuètes.', '##1. Faites tremper les nouilles.\r\n##2. Faites sauter le poulet.\r\n##3. Ajoutez œufs, germes de soja, nouilles.\r\n##4. Assaisonnez avec sauce tamarin et sucre.\r\n##5. Parsemez de cacahuètes.', 'images/recette_3_1747659800.jpg', 25, 10, 'moyenne', 3, '2025-03-23 16:32:38'),
(4, 1, 'Paella aux Fruits de Mer', 'Un incontournable de la cuisine espagnole, plein de couleurs et de saveurs.', '##Etape 1: Faites revenir l’oignon, l’ail.\r\n##Etape 2: Ajoutez le riz.\r\n##Etape 3: Ajoutez les fruits de mer et le bouillon.\r\n##Etape 4: Laissez mijoter sans remuer.\r\n##Etape 5: Parsemez de persil.', 'images/recette_4_1747659826.jpg', 30, 40, 'difficile', 4, '2025-03-23 16:32:38'),
(5, 1, 'Ojja Tunisienne', 'Plat tunisien à base de merguez, tomates, poivrons et œufs.', '##1. Faites revenir l’ail et l’oignon.\r\n##2. Ajoutez les poivrons et tomates.\r\n##3. Laissez mijoter 15 min.\r\n##4. Ajoutez les merguez.\r\n##5. Cassez les œufs dessus en fin de cuisson.', 'images/recette_5_1747659853.jpg', 15, 25, 'moyenne', 5, '2025-03-23 16:32:38'),
(6, 1, 'Tajine aux Pruneaux', 'Plat marocain sucré-salé à base de viande, pruneaux et amandes.', '##1. Faites revenir la viande avec les oignons.\r\n##2. Ajoutez les épices et un peu d’eau.\r\n##3. Laissez mijoter 1h.\r\n##4. Ajoutez les pruneaux et miel.\r\n##5. Parsemez d’amandes grillées.', 'images/recette_6_1747659896.jpg', 20, 60, 'difficile', 6, '2025-03-23 16:32:38'),
(7, 1, 'Chorba Algérienne', 'Soupe traditionnelle algérienne aux légumes, viande et vermicelles.', '##1. Faites revenir la viande avec l’oignon.\r\n##2. Ajoutez carottes, courgettes, pois chiches.\r\n##3. Couvrez d’eau et laissez mijoter.\r\n##4. Ajoutez les vermicelles et coriandre à la fin.', 'images/recette_7_1747659934.jpg', 20, 45, 'moyenne', 7, '2025-03-23 16:32:38'),
(8, 1, 'Moussaka Grecque', 'Gratin d’aubergines, viande hachée, béchamel et fromage.', '##1. Faites griller les aubergines.\r\n##2. Préparez la sauce viande/tomate.\r\n##3. Préparez une béchamel.\r\n##4. Alternez les couches dans un plat.\r\n##5. Cuisez 40 minutes.', 'images/recette_8_1747659981.jpg', 30, 40, 'difficile', 8, '2025-03-23 16:32:38'),
(9, 1, 'Baklava Turque', 'Pâtisserie feuilletée sucrée à base de miel, pistaches et noix.', '##1. Beurrez les feuilles de filo.\r\n##2. Ajoutez les couches de fruits secs.\r\n##3. Découpez en losanges.\r\n##4. Cuisez au four.\r\n##5. Arrosez de sirop de miel.', 'images/recette_9_1747660058.jpg', 45, 25, 'difficile', 9, '2025-03-23 16:32:38'),
(10, 1, 'Falafel Libanais', 'Boulettes croustillantes de pois chiches et épices, typiques du Moyen-Orient.', '##1. Mixez pois chiches, ail, oignon, épices.\r\n##2. Formez des boulettes.\r\n##3. Faites-les frire dans l’huile chaude.\r\n##4. Servez avec sauce tahini ou yaourt.', 'images/recette_10_1747660136.jpg', 25, 10, 'moyenne', 10, '2025-03-23 16:32:38'),
(13, 1, 'Cake aux olives', 'Recette à base d&#039;olive', '##Étape 1\r\n\r\nPréchauffer le four à Th 6 (180°C). Entretemps, mélanger la farine et les oeufs jusqu&#039;à obtenir un mélange onctueux. Ajouter l&#039;huile et l&#039;équivalent d&#039;1 verre de vin blanc sec.\r\n\r\n##Étape 2\r\n\r\nEgoutter les olives, les fariner légèrement et les incorporer à la pâte.\r\n\r\n##Étape 3\r\n\r\nAjouter le jambon, bien malaxer et verser le gruyère râpé et la levure; bien poivrer, mais ne pas saler à cause du jambon!\r\n\r\n##Étape 4\r\nBeurrer un moule à cake, y verser la pâte jusqu&#039;aux 2/3.\r\n\r\n##Étape 5\r\nEnfourner le cake pendant 50 min à Th 6 (180°C).\r\n\r\n##Étape 6\r\nSi le dessus du cake prend une teinte dorée trop rapidement, le couvrir d&#039;une feuille de papier alu et le laisser cuire ainsi.', 'images/recette_1747833559.jpg', 20, 50, 'facile', 1, '2025-05-21 15:19:19');

-- --------------------------------------------------------

--
-- Structure de la table `recette_etiquette`
--

CREATE TABLE `recette_etiquette` (
  `id_recette` int NOT NULL,
  `id_etiquette` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `recette_etiquette`
--

INSERT INTO `recette_etiquette` (`id_recette`, `id_etiquette`) VALUES
(10, 2),
(13, 2),
(1, 3),
(3, 3),
(6, 3),
(9, 5),
(13, 8),
(2, 10),
(8, 10),
(9, 10),
(3, 12),
(4, 12),
(13, 14),
(10, 15),
(10, 21),
(9, 23),
(4, 25),
(4, 26),
(13, 26),
(1, 27),
(6, 27),
(3, 28),
(4, 29),
(13, 33);

-- --------------------------------------------------------

--
-- Structure de la table `recette_favorite`
--

CREATE TABLE `recette_favorite` (
  `id_utilisateur` int NOT NULL,
  `id_recette` int NOT NULL,
  `date_enregistrement` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `recette_favorite`
--

INSERT INTO `recette_favorite` (`id_utilisateur`, `id_recette`, `date_enregistrement`) VALUES
(1, 1, '2025-05-25 14:17:54'),
(1, 2, '2025-05-25 09:26:18'),
(1, 5, '2025-05-25 14:18:00'),
(1, 13, '2025-05-25 14:17:49'),
(6, 1, '2025-05-22 15:28:36'),
(7, 5, '2025-05-22 16:06:49');

-- --------------------------------------------------------

--
-- Structure de la table `unite_mesure`
--

CREATE TABLE `unite_mesure` (
  `id` int NOT NULL,
  `nom` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `abreviation` varchar(10) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `unite_mesure`
--

INSERT INTO `unite_mesure` (`id`, `nom`, `abreviation`) VALUES
(1, 'gramme', 'g'),
(2, 'kilogramme', 'kg'),
(3, 'milligramme', 'mg'),
(4, 'litre', 'L'),
(5, 'millilitre', 'ml'),
(6, 'centilitre', 'cl'),
(7, 'pièce', 'pc'),
(8, 'tranche', 'tr'),
(9, 'pincée', 'pn'),
(10, 'cuillère à café', 'cac'),
(11, 'cuillère à soupe', 'cas'),
(12, 'verre', 'verre'),
(13, 'sachet', 'sachet'),
(14, 'bol', 'bol'),
(15, 'boule', 'boule'),
(16, 'tasse', 'tasse'),
(17, 'portion', 'prt'),
(18, 'feuille', 'f'),
(19, 'dsdqs', 'dsqd');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int NOT NULL,
  `id_admin` int DEFAULT NULL,
  `nom` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `prenom` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `date_inscription` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `id_admin`, `nom`, `prenom`, `email`, `mot_de_passe`, `date_inscription`) VALUES
(1, NULL, 'GRAZIANI', 'Alexandre', 'graziani1112@gmail.com', '$2y$12$FNkJFsMdPaliW18/iK4u7uOD.EH4JYmKxmzbcvj7M12s/1F/P6/hy', '2025-05-12 13:06:59'),
(2, NULL, 'GRAZIANI', 'Tiffany', 'tiffany@gmail.com', '$2y$12$1ftAcgUKJQLS22XHWvkfSOtBHh1LwJQv/lbS8IBuFh5uUppDCJwMW', '2025-05-12 14:06:32'),
(6, NULL, 'FOURATI', 'Islem', 'islemfourati@gmail.com', '$2y$12$U.9jVS9MxgGhqNlFCUa15efBcOn1nxtT8NJB6/H1cnJPrrszJ3HmC', '2025-05-22 15:25:12'),
(7, NULL, 'OUNADJELA', 'Abdelkader', 'okadus@gmail.com', '$2y$12$B6JzZbAYhXJUBGzZjwVc7.1ibjW/RZ7r0.7LkVLo/CYf8MP090kbK', '2025-05-22 15:49:06');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `administrateur`
--
ALTER TABLE `administrateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `administrateur_actions`
--
ALTER TABLE `administrateur_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Index pour la table `etiquette`
--
ALTER TABLE `etiquette`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Index pour la table `ingredient`
--
ALTER TABLE `ingredient`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Index pour la table `liste_personnelle_ingredients`
--
ALTER TABLE `liste_personnelle_ingredients`
  ADD PRIMARY KEY (`id_utilisateur`,`id_ingredient`),
  ADD KEY `fk_lpi_ingredient` (`id_ingredient`);

--
-- Index pour la table `liste_recette_ingredients`
--
ALTER TABLE `liste_recette_ingredients`
  ADD PRIMARY KEY (`id_recette`,`id_ingredient`),
  ADD KEY `fk_lri_unite` (`id_unite`),
  ADD KEY `lri_ibfk_2` (`id_ingredient`);

--
-- Index pour la table `recette`
--
ALTER TABLE `recette`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `id_categorie` (`id_categorie`);

--
-- Index pour la table `recette_etiquette`
--
ALTER TABLE `recette_etiquette`
  ADD PRIMARY KEY (`id_recette`,`id_etiquette`),
  ADD KEY `id_etiquette` (`id_etiquette`);

--
-- Index pour la table `recette_favorite`
--
ALTER TABLE `recette_favorite`
  ADD PRIMARY KEY (`id_utilisateur`,`id_recette`),
  ADD KEY `id_recette` (`id_recette`);

--
-- Index pour la table `unite_mesure`
--
ALTER TABLE `unite_mesure`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_admin` (`id_admin`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `administrateur`
--
ALTER TABLE `administrateur`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `administrateur_actions`
--
ALTER TABLE `administrateur_actions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `etiquette`
--
ALTER TABLE `etiquette`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT pour la table `ingredient`
--
ALTER TABLE `ingredient`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT pour la table `recette`
--
ALTER TABLE `recette`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `unite_mesure`
--
ALTER TABLE `unite_mesure`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `administrateur_actions`
--
ALTER TABLE `administrateur_actions`
  ADD CONSTRAINT `administrateur_actions_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `administrateur` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD CONSTRAINT `categorie_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `administrateur` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `etiquette`
--
ALTER TABLE `etiquette`
  ADD CONSTRAINT `etiquette_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `administrateur` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `ingredient`
--
ALTER TABLE `ingredient`
  ADD CONSTRAINT `ingredient_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `administrateur` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `liste_personnelle_ingredients`
--
ALTER TABLE `liste_personnelle_ingredients`
  ADD CONSTRAINT `fk_lpi_ingredient` FOREIGN KEY (`id_ingredient`) REFERENCES `ingredient` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lpi_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `liste_personnelle_ingredients_ibfk_2` FOREIGN KEY (`id_ingredient`) REFERENCES `ingredient` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `liste_recette_ingredients`
--
ALTER TABLE `liste_recette_ingredients`
  ADD CONSTRAINT `fk_lri_unite` FOREIGN KEY (`id_unite`) REFERENCES `unite_mesure` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `lri_ibfk_1` FOREIGN KEY (`id_recette`) REFERENCES `recette` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `lri_ibfk_2` FOREIGN KEY (`id_ingredient`) REFERENCES `ingredient` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Contraintes pour la table `recette`
--
ALTER TABLE `recette`
  ADD CONSTRAINT `recette_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `administrateur` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recette_ibfk_2` FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `recette_etiquette`
--
ALTER TABLE `recette_etiquette`
  ADD CONSTRAINT `recette_etiquette_ibfk_1` FOREIGN KEY (`id_recette`) REFERENCES `recette` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recette_etiquette_ibfk_2` FOREIGN KEY (`id_etiquette`) REFERENCES `etiquette` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `recette_favorite`
--
ALTER TABLE `recette_favorite`
  ADD CONSTRAINT `recette_favorite_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recette_favorite_ibfk_2` FOREIGN KEY (`id_recette`) REFERENCES `recette` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `utilisateur_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `administrateur` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
