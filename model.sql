-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 04 déc. 2024 à 20:56
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `basemodel`
--

-- --------------------------------------------------------

--
-- Structure de la table `model`
--

CREATE TABLE `model` (
  `id` int(11) NOT NULL,
  `Taux_Apprentissage` float NOT NULL,
  `Nombre_Epoques` int(11) NOT NULL,
  `Patience` int(11) NOT NULL,
  `Monitor` enum('val_loss','val_accuracy') NOT NULL,
  `Optimiser` enum('Adam','SGD') NOT NULL,
  `Model_Name` varchar(255) NOT NULL,
  `Activation_Function` enum('Sigmoid','ReLU','Tanh','Softmax') NOT NULL,
  `Validation_Split` float NOT NULL,
  `Test_Split` float NOT NULL,
  `Directory_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `model`
--

INSERT INTO `model` (`id`, `Taux_Apprentissage`, `Nombre_Epoques`, `Patience`, `Monitor`, `Optimiser`, `Model_Name`, `Activation_Function`, `Validation_Split`, `Test_Split`, `Directory_path`) VALUES
(1, 0.0001, 10, 3, 'val_accuracy', 'Adam', 'test', 'Tanh', 0.1, 0.2, 'Array');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `model`
--
ALTER TABLE `model`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `model`
--
ALTER TABLE `model`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
