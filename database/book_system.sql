-- Book Request and Issue System for OLMS
-- This file contains all the necessary tables for the book management system

-- Table for book requests
CREATE TABLE `book_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `request_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `book_id` (`book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table for book issues (when books are actually issued to users)
CREATE TABLE `book_issues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `issue_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `due_date` datetime NOT NULL,
  `return_date` datetime DEFAULT NULL,
  `status` enum('issued','returned','overdue') DEFAULT 'issued',
  `fine_amount` decimal(10,2) DEFAULT 0.00,
  `fine_paid` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `book_id` (`book_id`),
  KEY `request_id` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add some sample books to the books table
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
('The Little Prince', 'https://via.placeholder.com/190x260?text=Little+Prince', 'Antoine de Saint-Exupéry', 'Fiction', 3, 'A poetic tale about love and life');

-- Add foreign key constraints for data integrity
ALTER TABLE `book_requests`
ADD CONSTRAINT `fk_requests_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_requests_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

ALTER TABLE `book_issues`
ADD CONSTRAINT `fk_issues_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_issues_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_issues_request` FOREIGN KEY (`request_id`) REFERENCES `book_requests` (`id`) ON DELETE CASCADE; 