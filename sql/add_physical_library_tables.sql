-- Migration: Add physical library management tables
-- This adds support for physical books, loans, and user bans

-- Add is_banned field to users table
ALTER TABLE users ADD COLUMN is_banned BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE users ADD COLUMN ban_reason TEXT NULL;
ALTER TABLE users ADD COLUMN banned_at TIMESTAMP NULL;
ALTER TABLE users ADD COLUMN banned_by BIGINT UNSIGNED NULL;
ALTER TABLE users ADD CONSTRAINT fk_users_banned_by FOREIGN KEY (banned_by) REFERENCES users(user_id) ON DELETE SET NULL;

-- Physical books table (physical copies in the library)
CREATE TABLE IF NOT EXISTS physical_books (
    physical_book_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    book_id BIGINT UNSIGNED NOT NULL,
    inventory_number VARCHAR(100) NOT NULL UNIQUE,
    location VARCHAR(255) NULL COMMENT 'Shelf location in library',
    is_available BOOLEAN NOT NULL DEFAULT TRUE,
    notes TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_physical_books_book FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE,
    INDEX idx_physical_books_book (book_id),
    INDEX idx_physical_books_available (is_available)
);

-- Book loans table (tracking who borrowed physical books)
CREATE TABLE IF NOT EXISTS book_loans (
    loan_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    physical_book_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    loaned_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    due_date DATE NOT NULL,
    returned_at TIMESTAMP NULL,
    is_overdue BOOLEAN NOT NULL DEFAULT FALSE,
    notes TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_book_loans_physical_book FOREIGN KEY (physical_book_id) REFERENCES physical_books(physical_book_id) ON DELETE CASCADE,
    CONSTRAINT fk_book_loans_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_book_loans_user (user_id),
    INDEX idx_book_loans_due_date (due_date),
    INDEX idx_book_loans_overdue (is_overdue)
);

-- User bans table (tracking ban history)
CREATE TABLE IF NOT EXISTS user_bans (
    ban_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    banned_by BIGINT UNSIGNED NOT NULL,
    reason TEXT NOT NULL,
    banned_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    unbanned_at TIMESTAMP NULL,
    unbanned_by BIGINT UNSIGNED NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT fk_user_bans_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_user_bans_banned_by FOREIGN KEY (banned_by) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_user_bans_unbanned_by FOREIGN KEY (unbanned_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_user_bans_user (user_id),
    INDEX idx_user_bans_active (is_active)
);

