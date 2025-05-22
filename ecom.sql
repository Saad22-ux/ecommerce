-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: May 20, 2025 at 01:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecom`
--

-- --------------------------------------------------------

--
-- Table structure for table `card_payments`
--

CREATE TABLE `card_payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `card_number` varchar(20) NOT NULL,
  `card_holder` varchar(100) NOT NULL,
  `expiry_date` varchar(7) NOT NULL,
  `cvv` varchar(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `card_payments`
--

INSERT INTO `card_payments` (`id`, `order_id`, `card_number`, `card_holder`, `expiry_date`, `cvv`, `created_at`) VALUES
(1, 9, '5400213564012', 'ilyas', '05/2026', '812', '2025-05-19 06:49:46'),
(2, 10, '5214786321547', 'youssef', '02/2030', '451', '2025-05-19 06:58:56'),
(3, 12, '154251555587', 'amin', '25/2028', '548', '2025-05-19 10:04:00'),
(4, 14, '4445555447878', 'anas', '02/2029', '123', '2025-05-19 22:27:09');

-- --------------------------------------------------------

--
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
(9, 2, 'card', '2025-05-19 08:49:46'),
(10, 2, 'card', '2025-05-19 08:58:56'),
(11, 2, 'cash', '2025-05-19 09:03:49'),
(12, 2, 'card', '2025-05-19 12:04:00'),
(13, 2, 'cash', '2025-05-19 17:31:25'),
(14, 2, 'card', '2025-05-20 00:27:09');

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
(16, 9, 6, 1),
(17, 10, 5, 1),
(18, 10, 6, 1),
(19, 11, 6, 2),
(20, 12, 6, 1),
(21, 12, 5, 1),
(22, 13, 7, 1),
(23, 14, 5, 1),
(24, 14, 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` float NOT NULL,
  `image` varchar(255) NOT NULL,
  `categorie` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `categorie`, `age`, `quantity`) VALUES
(5, 'Call of  Duty', 'Call of Duty ou COD (en français l\'« Appel du devoir ») est une série de jeux vidéo de tir à la première personne sur la guerre', 200, 'uploads/1747636591_1747513715_téléchargement.jpeg', 'Action', 20, 1),
(6, 'eFootball', 'En mode Équipe de rêve, vous pouvez créer votre propre équipe en recrutant vos joueurs et managers préférés.', 350, 'uploads/1747637036_1747573860_téléchargement.jpeg', 'Jeux de sport', 16, 1),
(7, '8ball pool', '8 Ball Pool is an addictive challenging game based on real 3D pool games, where you will challenge your friends online.', 100, 'uploads/1747638354_téléchargement (1).jpeg', 'Jeux de rôle', 14, 1),
(8, 'Chess ', 'Free online chess server. Play chess in a clean interface. No registration, no ads, no plugin required. Play chess with the computer', 50, 'uploads/1747698415_téléchargement (2).jpeg', 'Stratégie', 12, 20),
(9, 'Forza Horizon 5', 'Explorez les paysages changeants du Mexique dans ce jeu en monde ouvert et conduisez des centaines de voitures phénoménales', 330, 'uploads/1747698539_téléchargement (1).png', 'Jeux de rôle', 10, 4),
(10, 'Fortnite', 'Fortnite est un jeu en ligne développé par Epic Games sous la forme de différents modes de jeu qui partagent le même gameplay général et le même moteur de jeu.', 500, 'uploads/1747698753_téléchargement (3).jpeg', 'Action', 18, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','client') NOT NULL,
  `fullName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `role`, `fullName`) VALUES
(1, 'saad@gmail.com', 'Saad2003', 'admin', 'Elasri'),
(2, 'ilyas@gmail.com', 'Ilyas2003', 'client', 'Ilyas nmrani');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `card_payments`
--
ALTER TABLE `card_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

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
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `card_payments`
--
ALTER TABLE `card_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `card_payments`
--
ALTER TABLE `card_payments`
  ADD CONSTRAINT `card_payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
