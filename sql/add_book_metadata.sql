-- Migration: Add book metadata fields
ALTER TABLE books 
ADD COLUMN isbn VARCHAR(20) NULL,
ADD COLUMN pages INT NULL,
ADD COLUMN publication_year INT NULL,
ADD COLUMN publisher VARCHAR(255) NULL,
ADD COLUMN language VARCHAR(50) NULL DEFAULT 'en';

-- Add index for ISBN
CREATE INDEX idx_books_isbn ON books(isbn);

-- Add index for publication year
CREATE INDEX idx_books_year ON books(publication_year);

