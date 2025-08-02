-- College Library Management System Database
-- This file contains the simplified database structure for a real college library

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
INSERT INTO `admin` (`fullname`, `email`, `password`) VALUES
('Library Admin', 'admin@library.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample users
INSERT INTO `users` (`fullname`, `username`, `email`, `password`, `address`, `role`) VALUES
('John Doe', 'john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123 Main St, City', 'user'),
('Jane Smith', 'jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '456 Oak Ave, Town', 'user'),
('Bob Johnson', 'bob_johnson', 'bob@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '789 Pine Rd, Village', 'user');

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
('English Literature Classics', 'https://via.placeholder.com/190x260?text=English+Lit', 'Prof. David Thompson', 'Literature', 4, 'Collection of classic English literature works'); 