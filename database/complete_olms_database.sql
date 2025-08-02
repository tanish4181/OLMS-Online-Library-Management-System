-- Complete OLMS Database for College Library Management System
-- This file contains the complete database structure and sample data
-- Import this file to set up the entire system

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `olms` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `olms`;

-- Drop existing tables if they exist (for clean import)
DROP TABLE IF EXISTS `book_issues`;
DROP TABLE IF EXISTS `book_requests`;
DROP TABLE IF EXISTS `books`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `admin`;

-- Users table (students and staff)
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL UNIQUE,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `address` text,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Admin table (for admin login)
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Books table
CREATE TABLE `books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `author` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` text,
  `cover` varchar(500) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Book issues table (when books are issued to users)
CREATE TABLE `book_issues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `issue_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `due_date` datetime NOT NULL,
  `return_date` datetime DEFAULT NULL,
  `status` enum('issued','returned','overdue') DEFAULT 'issued',
  `fine_amount` decimal(10,2) DEFAULT 0.00,
  `fine_paid` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `book_id` (`book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add foreign key constraints for data integrity
ALTER TABLE `book_issues`
ADD CONSTRAINT `fk_issues_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_issues_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

-- Insert sample admin user
-- Password: password (hashed with bcrypt)
INSERT INTO `admin` (`fullname`, `email`, `password`) VALUES
('Library Admin', 'admin@library.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample users
-- Password: password (hashed with bcrypt)
INSERT INTO `users` (`fullname`, `username`, `email`, `password`, `address`, `role`) VALUES
('John Doe', 'john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123 Main St, City', 'user'),
('Jane Smith', 'jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '456 Oak Ave, Town', 'user'),
('Bob Johnson', 'bob_johnson', 'bob@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '789 Pine Rd, Village', 'user'),
('Alice Brown', 'alice_brown', 'alice@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '321 Elm St, Borough', 'user'),
('Charlie Wilson', 'charlie_wilson', 'charlie@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '654 Maple Dr, County', 'user');

-- Insert sample books
INSERT INTO `books` (`title`, `cover`, `author`, `category`, `quantity`, `description`) VALUES
('The Great Gatsby', 'https://via.placeholder.com/190x260?text=Great+Gatsby', 'F. Scott Fitzgerald', 'Fiction', 3, 'A classic American novel about the Jazz Age'),
('To Kill a Mockingbird', 'https://via.placeholder.com/190x260?text=Mockingbird', 'Harper Lee', 'Fiction', 2, 'A powerful story about racial injustice'),
('1984', 'https://via.placeholder.com/190x260?text=1984', 'George Orwell', 'Fiction', 4, 'A dystopian novel about totalitarianism'),
('Pride and Prejudice', 'https://via.placeholder.com/190x260?text=Pride+Prejudice', 'Jane Austen', 'Romance', 2, 'A classic romance novel'),
('The Hobbit', 'https://via.placeholder.com/190x260?text=Hobbit', 'J.R.R. Tolkien', 'Fantasy', 3, 'An epic fantasy adventure'),
('Harry Potter and the Sorcerer''s Stone', 'https://via.placeholder.com/190x260?text=Harry+Potter', 'J.K. Rowling', 'Fantasy', 5, 'The first book in the Harry Potter series'),
('The Catcher in the Rye', 'https://via.placeholder.com/190x260?text=Catcher+Rye', 'J.D. Salinger', 'Fiction', 2, 'A coming-of-age story'),
('Lord of the Flies', 'https://via.placeholder.com/190x260?text=Lord+Flies', 'William Golding', 'Fiction', 3, 'A novel about human nature and society'),
('The Alchemist', 'https://via.placeholder.com/190x260?text=Alchemist', 'Paulo Coelho', 'Fiction', 4, 'A philosophical novel about following your dreams'),
('The Little Prince', 'https://via.placeholder.com/190x260?text=Little+Prince', 'Antoine de Saint-Exupéry', 'Fiction', 3, 'A poetic tale about love and life'),
('Introduction to Computer Science', 'https://via.placeholder.com/190x260?text=CS+Intro', 'John Smith', 'Computer Science', 4, 'A comprehensive introduction to computer science concepts'),
('Advanced Mathematics', 'https://via.placeholder.com/190x260?text=Math+Adv', 'Dr. Emily Brown', 'Mathematics', 3, 'Advanced mathematical concepts and theories'),
('History of World War II', 'https://via.placeholder.com/190x260?text=WWII+History', 'Prof. Michael Wilson', 'History', 2, 'Detailed account of World War II events'),
('Physics Fundamentals', 'https://via.placeholder.com/190x260?text=Physics', 'Dr. Sarah Johnson', 'Science', 3, 'Basic principles of physics for students'),
('English Literature Classics', 'https://via.placeholder.com/190x260?text=English+Lit', 'Prof. David Thompson', 'Literature', 4, 'Collection of classic English literature works'),
('Data Structures and Algorithms', 'https://via.placeholder.com/190x260?text=Data+Structures', 'Dr. Robert Chen', 'Computer Science', 3, 'Comprehensive guide to data structures and algorithms'),
('Calculus Made Easy', 'https://via.placeholder.com/190x260?text=Calculus', 'Prof. Lisa Wang', 'Mathematics', 2, 'An accessible introduction to calculus'),
('The Art of Programming', 'https://via.placeholder.com/190x260?text=Programming', 'James Miller', 'Computer Science', 5, 'Learn programming fundamentals and best practices'),
('World History: Ancient Times', 'https://via.placeholder.com/190x260?text=Ancient+History', 'Dr. Patricia Garcia', 'History', 3, 'Explore ancient civilizations and their impact'),
('Chemistry for Beginners', 'https://via.placeholder.com/190x260?text=Chemistry', 'Prof. Kevin Lee', 'Science', 4, 'Introduction to basic chemistry concepts');

-- Insert some sample book issues (optional - for testing)
INSERT INTO `book_issues` (`user_id`, `book_id`, `issue_date`, `due_date`, `status`) VALUES
(1, 1, NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY), 'issued'),
(2, 3, NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY), 'issued'),
(3, 5, NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY), 'issued');

-- Display import confirmation
SELECT 'Database setup completed successfully!' as status;
SELECT COUNT(*) as total_users FROM users;
SELECT COUNT(*) as total_books FROM books;
SELECT COUNT(*) as total_issues FROM book_issues; 