-- Таблица для хранения переводов книг
CREATE TABLE IF NOT EXISTS book_translations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    book_id BIGINT UNSIGNED NOT NULL,
    language VARCHAR(10) NOT NULL DEFAULT 'en',
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    description TEXT NULL,
    genre VARCHAR(255) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY book_translations_unique (book_id, language),
    CONSTRAINT fk_book_translations_book FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE,
    INDEX idx_book_translations_language (language),
    INDEX idx_book_translations_book (book_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Миграция существующих данных: создаем записи для английского языка из основной таблицы books
INSERT INTO book_translations (book_id, language, title, author, description, genre)
SELECT book_id, 'en', title, author, description, genre
FROM books
WHERE NOT EXISTS (
    SELECT 1 FROM book_translations bt WHERE bt.book_id = books.book_id AND bt.language = 'en'
);

