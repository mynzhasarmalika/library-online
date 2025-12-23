-- Migration: Add reservation support for physical books
-- Ensures students can reserve an available copy before visiting the library

CREATE TABLE IF NOT EXISTS book_reservations (
    reservation_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    book_id BIGINT UNSIGNED NOT NULL,
    physical_book_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    status ENUM('pending','ready','collected','cancelled','expired') NOT NULL DEFAULT 'pending',
    reserved_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    handled_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    notes TEXT NULL,
    CONSTRAINT fk_book_reservations_book FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE,
    CONSTRAINT fk_book_reservations_physical FOREIGN KEY (physical_book_id) REFERENCES physical_books(physical_book_id) ON DELETE CASCADE,
    CONSTRAINT fk_book_reservations_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_book_reservations_user (user_id),
    INDEX idx_book_reservations_status (status),
    INDEX idx_book_reservations_expires (expires_at)
);


