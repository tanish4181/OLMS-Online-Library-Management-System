-- Fix Database Structure for College Library System
-- This script removes the old request system references

-- Drop the foreign key constraint that references book_requests
ALTER TABLE `book_issues` DROP FOREIGN KEY `fk_issues_request`;

-- Remove the request_id column from book_issues table
ALTER TABLE `book_issues` DROP COLUMN `request_id`;

-- Drop the book_requests table if it exists
DROP TABLE IF EXISTS `book_requests`;

-- Now add the correct foreign key constraints
ALTER TABLE `book_issues`
ADD CONSTRAINT `fk_issues_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_issues_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE; 