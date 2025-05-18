-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
<<<<<<< HEAD
-- Host: 127.0.0.1:3307
-- Generation Time: May 19, 2025 at 12:08 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12
=======
-- Hôte : 127.0.0.1
-- Généré le : dim. 18 mai 2025 à 13:39
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12
>>>>>>> 20dadbd594a7d6a240c7478cfae99d8e0e2fcad5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
<<<<<<< HEAD
-- Database: `ecom`
=======
-- Base de données : `ecom`
>>>>>>> 20dadbd594a7d6a240c7478cfae99d8e0e2fcad5
--

-- --------------------------------------------------------

--
<<<<<<< HEAD
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `payment_method`, `created_at`) VALUES
(5, 2, 'cash', '2025-05-18 15:12:09'),
(6, 2, 'cash', '2025-05-18 15:20:56'),
(7, 2, 'cash', '2025-05-18 23:55:07'),
(8, 2, 'card', '2025-05-18 23:59:01');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(10, 5, 0, 4),
(11, 5, 1, 3),
(12, 5, 2, 1),
(13, 6, 4, 1),
(14, 7, 4, 2),
(15, 8, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
=======
-- Structure de la table `products`
>>>>>>> 20dadbd594a7d6a240c7478cfae99d8e0e2fcad5
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` float NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
<<<<<<< HEAD
-- Dumping data for table `products`
=======
-- Déchargement des données de la table `products`
>>>>>>> 20dadbd594a7d6a240c7478cfae99d8e0e2fcad5
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`) VALUES
(1, 'PUBG', 'description pour pubg mobile', 150, 'uploads/1747512236_pubg.jpg'),
<<<<<<< HEAD
(3, 'call of duty : warzone', 'jeux de tactics et de battlegro', 300, 'uploads/1747570461_images.jpeg'),
(4, 'eFootball', 'En mode Équipe de rêve, vous pouvez créer votre propre équipe en recrutant vos joueurs et managers préférés.', 350, 'uploads/1747573860_téléchargement.jpeg');
=======
(2, 'call of duty: warzone', 'jeux de tactics et de battleground', 300, 'uploads/1747513715_téléchargement.jpeg');
>>>>>>> 20dadbd594a7d6a240c7478cfae99d8e0e2fcad5

-- --------------------------------------------------------

--
<<<<<<< HEAD
-- Table structure for table `user`
=======
-- Structure de la table `user`
>>>>>>> 20dadbd594a7d6a240c7478cfae99d8e0e2fcad5
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','client') NOT NULL,
  `fullName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
<<<<<<< HEAD
-- Dumping data for table `user`
=======
-- Déchargement des données de la table `user`
>>>>>>> 20dadbd594a7d6a240c7478cfae99d8e0e2fcad5
--

INSERT INTO `user` (`id`, `email`, `password`, `role`, `fullName`) VALUES
(1, 'saad@gmail.com', 'Saad2003', 'admin', 'Elasri'),
(2, 'ilyas@gmail.com', 'Ilyas2003', 'client', 'Ilyas nmrani');

--
<<<<<<< HEAD
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
=======
-- Index pour les tables déchargées
--

--
-- Index pour la table `products`
>>>>>>> 20dadbd594a7d6a240c7478cfae99d8e0e2fcad5
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
<<<<<<< HEAD
-- Indexes for table `user`
=======
-- Index pour la table `user`
>>>>>>> 20dadbd594a7d6a240c7478cfae99d8e0e2fcad5
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
<<<<<<< HEAD
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
=======
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `user`
>>>>>>> 20dadbd594a7d6a240c7478cfae99d8e0e2fcad5
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
