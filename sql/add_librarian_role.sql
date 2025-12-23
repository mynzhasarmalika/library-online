-- Add librarian role to users table
-- Run this migration to add the librarian role

ALTER TABLE users MODIFY COLUMN role ENUM('admin','librarian','user') NOT NULL DEFAULT 'user';

