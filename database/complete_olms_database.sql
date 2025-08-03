-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 03, 2025 at 09:36 AM
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
-- Database: `olms`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `fullname`, `email`, `password`, `created_at`) VALUES
(1, 'Library Admin', 'admin@library.com', '$2y$10$4Tmjd3Ay.V2XPpBj4pXBz.vjX/On2ntm.5AmG2oV.eEfBfH4TtUTu', '2025-08-02 15:43:36');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `author` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `cover` varchar(500) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `category`, `description`, `cover`, `quantity`, `created_at`) VALUES
(3, '1984', 'George Orwell', 'Fiction', 'A dystopian novel about totalitarianism', 'https://m.media-amazon.com/images/I/715WdnBHqYL._UF1000,1000_QL80_.jpg', 4, '2025-08-02 15:43:37'),
(6, 'Harry Potter and the Sorcerer s Stone', 'J.K. Rowling', 'Fantasy', 'The first book in the Harry Potter series', 'https://images.moviesanywhere.com/143cdb987186a1c8f94d4f18de211216/fdea56fa-2703-47c1-8da8-70fc5382e1ea.jpg', 5, '2025-08-02 15:43:37'),
(7, 'The 48 Laws of Power', 'Robert Greene', 'Dark Psychology', 'A manual on manipulation, power dynamics, and control', '../uploads/covers/book_7_1754206317.jpg', 3, '2025-08-03 06:30:00'),
(8, 'Atomic Habits', 'James Clear', 'Self-help', 'An evidence-based system to build good habits and break bad ones', 'https://m.media-amazon.com/images/I/91bYsX41DVL._AC_UF1000,1000_QL80_.jpg', 5, '2025-08-03 06:30:00'),
(9, 'The Silent Patient', 'Alex Michaelides', 'Fiction', 'A gripping story of a woman who stops speaking after a shocking crime', '../uploads/covers/book_9_1754206302.jpg', 5, '2025-08-03 06:30:00'),
(10, 'The Subtle Art of Not Giving a F*ck', 'Mark Manson', 'Self-help', 'A counterintuitive approach to living a better life', 'https://m.media-amazon.com/images/I/71QKQ9mwV7L._AC_UF1000,1000_QL80_.jpg', 4, '2025-08-03 06:30:00'),
(11, 'Gone Girl', 'Gillian Flynn', 'Thriller', 'A psychological thriller exploring marriage and manipulation', 'https://m.media-amazon.com/images/I/81af+MCATTL._AC_UF1000,1000_QL80_.jpg', 3, '2025-08-03 06:30:00'),
(12, 'Engineering Mechanics', 'R.K. Bansal', 'Engineering', 'Fundamentals of statics and dynamics in engineering', '../uploads/covers/book_12_1754206348.jpg', 7, '2025-08-03 06:30:00'),
(13, 'Strength of Materials', 'R.K. Rajput', 'Engineering', 'Detailed study of stress, strain, and material strength', '../uploads/covers/book_13_1754206331.jpg', 4, '2025-08-03 06:30:00'),
(14, 'Basic Electrical Engineering', 'V.K. Mehta & Rohit Mehta', 'Academic', 'An introductory book on electrical circuits and systems', '../uploads/covers/book_14_1754205941.jpg', 5, '2025-08-03 06:30:00'),
(15, 'Engineering Mathematics', 'B.S. Grewal', 'Engineering', 'Comprehensive textbook for engineering-level math', '../uploads/covers/book_15_1754206359.jpg', 7, '2025-08-03 06:30:00'),
(16, 'Data Structures Using C', 'Reema Thareja', 'Academic', 'Covers basic to advanced data structures in C language', '../uploads/covers/book_16_1754206020.jpg', 3, '2025-08-03 06:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `book_issues`
--

CREATE TABLE `book_issues` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `issue_date` datetime DEFAULT current_timestamp(),
  `due_date` datetime NOT NULL,
  `return_date` datetime DEFAULT NULL,
  `status` enum('issued','returned','overdue') DEFAULT 'issued',
  `fine_amount` decimal(10,2) DEFAULT 0.00,
  `fine_paid` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_issues`
--

INSERT INTO `book_issues` (`id`, `user_id`, `book_id`, `issue_date`, `due_date`, `return_date`, `status`, `fine_amount`, `fine_paid`) VALUES
(11, 9, 7, '2025-08-03 13:03:05', '2025-08-17 00:00:00', NULL, 'issued', 0.00, 0),
(12, 9, 8, '2025-08-03 13:03:16', '2025-08-17 00:00:00', NULL, 'issued', 0.00, 0),
(13, 11, 15, '2025-08-03 13:03:23', '2025-08-17 00:00:00', NULL, 'issued', 0.00, 0),
(14, 11, 3, '2025-08-03 13:03:38', '2025-08-17 00:00:00', NULL, 'issued', 0.00, 0),
(15, 12, 13, '2025-08-03 13:03:50', '2025-08-17 00:00:00', NULL, 'issued', 0.00, 0),
(16, 12, 16, '2025-08-03 13:04:00', '2025-08-17 00:00:00', NULL, 'issued', 0.00, 0),
(17, 10, 10, '2025-08-03 13:04:06', '2025-08-17 00:00:00', NULL, 'issued', 0.00, 0),
(18, 10, 14, '2025-08-03 13:04:16', '2025-08-17 00:00:00', NULL, 'issued', 0.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `email`, `password`, `address`, `role`, `created_at`) VALUES
(8, 'admin2', 'admin', 'admin@olms', '$2y$10$jdhpuOFVBn9K5PiCQF04WeZ7OQyTM9iaU2XeE43IE7VxHImXisvOi', 'jaipur', 'admin', '2025-08-02 16:06:23'),
(9, 'Mehul Suthar', 'Mehul', 'mehul@gmail.com', '$2y$10$taQ57HDCx1WR.Pzgla/.quddjN6AW0663fFk1NAsMO.u1V2FjOykG', 'jaipur', 'user', '2025-08-03 07:12:29'),
(10, 'Tanish sharma', 'tandev', 'tanish@gmail.com', '$2y$10$gVATAbvd36rPuIK67BYGPeyAHDTbna6HKJlsCiF/GeIl6voUcAykC', 'jaipur', 'user', '2025-08-03 07:13:23'),
(11, 'Sajal singhal', 'Sajal', 'sajal@gmail.com', '$2y$10$xk1VMPL67COjaSXLD0xfp.q44lsTqvoBwuLMEutL1p8e2dKow4zz.', 'jaipur', 'user', '2025-08-03 07:14:13'),
(12, 'shreya saxena', 'shreya', 'shreya@gmail', '$2y$10$669qEPxuxDVaVdSZRSy2c.G9gMeET5HVVLH3Ty4NxI/HMpPrDjBMi', 'jaipur', 'user', '2025-08-03 07:15:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `book_issues`
--
ALTER TABLE `book_issues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `book_issues`
--
ALTER TABLE `book_issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `book_issues`
--
ALTER TABLE `book_issues`
  ADD CONSTRAINT `fk_issues_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_issues_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
