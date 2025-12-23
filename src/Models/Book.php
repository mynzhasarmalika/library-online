<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class Book
{
    /**
     * Check if translations table exists
     */
    private static function translationsTableExists(): bool
    {
        static $exists = null;
        if ($exists !== null) {
            return $exists;
        }
        
        try {
            $stmt = db()->query("SHOW TABLES LIKE 'book_translations'");
            $exists = $stmt->rowCount() > 0;
            return $exists;
        } catch (\PDOException $e) {
            $exists = false;
            return false;
        }
    }
    
    /**
     * Get translated content for a book
     * Falls back to English if translation doesn't exist
     */
    private static function getTranslatedContent(int $bookId, string $language, string $field): ?string
    {
        if (!self::translationsTableExists()) {
            return null;
        }
        
        try {
            $stmt = db()->prepare('
                SELECT ' . $field . ' FROM book_translations 
                WHERE book_id = :book_id AND language = :language
                LIMIT 1
            ');
            $stmt->execute(['book_id' => $bookId, 'language' => $language]);
            $result = $stmt->fetchColumn();
            
            if ($result) {
                return $result;
            }
            
            // Fallback to English
            if ($language !== 'en') {
                $stmt = db()->prepare('
                    SELECT ' . $field . ' FROM book_translations 
                    WHERE book_id = :book_id AND language = "en"
                    LIMIT 1
                ');
                $stmt->execute(['book_id' => $bookId]);
                $result = $stmt->fetchColumn();
            }
            
            return $result ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }
    
    /**
     * Apply translations to book data
     */
    private static function applyTranslations(array $book, ?string $language = null): array
    {
        $language = $language ?? get_language();
        $bookId = (int) $book['book_id'];
        
        // Get translation
        $stmt = db()->prepare('
            SELECT title, author, description, genre 
            FROM book_translations 
            WHERE book_id = :book_id AND language = :language
            LIMIT 1
        ');
        $stmt->execute(['book_id' => $bookId, 'language' => $language]);
        $translation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // If translation exists, use it; otherwise fallback to English or original
        if ($translation) {
            $book['title'] = $translation['title'] ?: $book['title'];
            $book['author'] = $translation['author'] ?: $book['author'];
            $book['description'] = $translation['description'] ?: $book['description'];
            $book['genre'] = $translation['genre'] ?: $book['genre'];
        } elseif ($language !== 'en') {
            // Try English fallback
            $stmt = db()->prepare('
                SELECT title, author, description, genre 
                FROM book_translations 
                WHERE book_id = :book_id AND language = "en"
                LIMIT 1
            ');
            $stmt->execute(['book_id' => $bookId]);
            $enTranslation = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($enTranslation) {
                $book['title'] = $enTranslation['title'] ?: $book['title'];
                $book['author'] = $enTranslation['author'] ?: $book['author'];
                $book['description'] = $enTranslation['description'] ?: $book['description'];
                $book['genre'] = $enTranslation['genre'] ?: $book['genre'];
            }
        }
        
        return $book;
    }

    public static function paginate(array $filters, int $perPage = 12, int $page = 1, string $sort = 'newest'): array
    {
        $language = get_language();
        $hasTranslations = self::translationsTableExists();
        $conditions = [];
        $params = [];

        if (!empty($filters['search'])) {
            if ($hasTranslations) {
                // Search in translations
                $conditions[] = '(
                    EXISTS (
                        SELECT 1 FROM book_translations bt 
                        WHERE bt.book_id = b.book_id 
                        AND bt.language = :search_lang 
                        AND (bt.title LIKE :search OR bt.author LIKE :search OR bt.description LIKE :search)
                    )
                    OR (b.title LIKE :search OR b.author LIKE :search OR b.description LIKE :search)
                )';
                $params['search_lang'] = $language;
            } else {
            $conditions[] = '(b.title LIKE :search OR b.author LIKE :search OR b.description LIKE :search)';
            }
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['genres'])) {
            $genrePlaceholders = [];
            foreach ($filters['genres'] as $idx => $genre) {
                $key = "genre{$idx}";
                $genrePlaceholders[] = ":{$key}";
                $params[$key] = $genre;
            }
            if ($genrePlaceholders) {
                if ($hasTranslations) {
                    $conditions[] = '(
                        EXISTS (
                            SELECT 1 FROM book_translations bt 
                            WHERE bt.book_id = b.book_id 
                            AND bt.language = :genre_lang 
                            AND bt.genre IN (' . implode(',', $genrePlaceholders) . ')
                        )
                        OR b.genre IN (' . implode(',', $genrePlaceholders) . ')
                    )';
                    $params['genre_lang'] = $language;
                } else {
                $conditions[] = 'b.genre IN (' . implode(',', $genrePlaceholders) . ')';
                }
            }
        }

        if (!empty($filters['author'])) {
            if ($hasTranslations) {
                $conditions[] = '(
                    EXISTS (
                        SELECT 1 FROM book_translations bt 
                        WHERE bt.book_id = b.book_id 
                        AND bt.language = :author_lang 
                        AND bt.author LIKE :author
                    )
                    OR b.author LIKE :author
                )';
                $params['author_lang'] = $language;
            } else {
            $conditions[] = 'b.author LIKE :author';
            }
            $params['author'] = '%' . $filters['author'] . '%';
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $countSql = "SELECT COUNT(*) FROM books b {$where}";
        $stmt = db()->prepare($countSql);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $offset = max(0, ($page - 1) * $perPage);

        // Determine sort order
        $orderBy = match($sort) {
            'oldest' => 'b.created_at ASC',
            'rating' => 'avg_rating DESC, ratings_count DESC',
            'title' => $hasTranslations ? 'COALESCE(bt.title, b.title) ASC' : 'b.title ASC',
            default => 'b.created_at DESC', // newest
        };

        if ($hasTranslations) {
            $sql = "
                SELECT 
                    b.*,
                    bt.title AS trans_title,
                    bt.author AS trans_author,
                    bt.description AS trans_description,
                    bt.genre AS trans_genre,
                    (
                        SELECT IFNULL(AVG(r1.score), 0)
                        FROM ratings r1
                        WHERE r1.book_id = b.book_id
                    ) AS avg_rating,
                    (
                        SELECT COUNT(*)
                        FROM ratings r2
                        WHERE r2.book_id = b.book_id
                    ) AS ratings_count
                FROM books b
                LEFT JOIN book_translations bt ON bt.book_id = b.book_id AND bt.language = :lang
                {$where}
                ORDER BY {$orderBy}
                LIMIT :limit OFFSET :offset
            ";
            $params['lang'] = $language;
        } else {
        $sql = "
            SELECT 
                b.*,
                (
                    SELECT IFNULL(AVG(r1.score), 0)
                    FROM ratings r1
                    WHERE r1.book_id = b.book_id
                ) AS avg_rating,
                (
                    SELECT COUNT(*)
                    FROM ratings r2
                    WHERE r2.book_id = b.book_id
                ) AS ratings_count
            FROM books b
            {$where}
            ORDER BY {$orderBy}
            LIMIT :limit OFFSET :offset
        ";
        }

        $stmt = db()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Apply translations if table exists
        if ($hasTranslations) {
            foreach ($books as &$book) {
                if (!empty($book['trans_title'])) {
                    $book['title'] = $book['trans_title'];
                }
                if (!empty($book['trans_author'])) {
                    $book['author'] = $book['trans_author'];
                }
                if (!empty($book['trans_description'])) {
                    $book['description'] = $book['trans_description'];
                }
                if (!empty($book['trans_genre'])) {
                    $book['genre'] = $book['trans_genre'];
                }
                unset($book['trans_title'], $book['trans_author'], $book['trans_description'], $book['trans_genre']);
            }
        }

        return [
            'data' => $books,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => max(1, (int) ceil($total / $perPage)),
            ],
        ];
    }

    public static function genres(): array
    {
        try {
            $hasTranslations = self::translationsTableExists();
            $language = get_language();
            
            $allGenres = [];
            
            if ($hasTranslations) {
                // Get genres from translations for current language
                $stmt = db()->prepare('
                    SELECT DISTINCT genre
                    FROM book_translations
                    WHERE language = :lang
                    AND genre IS NOT NULL
                    AND genre != \'\'
                ');
                $stmt->execute(['lang' => $language]);
                $translatedGenres = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $allGenres = array_merge($allGenres, $translatedGenres);
            }
            
            // Always get genres from main books table
            $stmt = db()->query('
                SELECT DISTINCT genre 
                FROM books 
                WHERE genre IS NOT NULL 
                AND genre != \'\'
            ');
            $bookGenres = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $allGenres = array_merge($allGenres, $bookGenres);
            
            // Filter, trim, and clean genres
            $filteredGenres = [];
            foreach ($allGenres as $genre) {
                if ($genre === null) continue;
                $trimmed = trim((string) $genre);
                if (!empty($trimmed) && strlen($trimmed) > 0) {
                    $filteredGenres[] = $trimmed;
                }
            }
            
            // Remove duplicates, sort, and return
            $filteredGenres = array_unique($filteredGenres);
            sort($filteredGenres);
            
            return array_values($filteredGenres);
        } catch (\PDOException $e) {
            error_log('Error fetching genres: ' . $e->getMessage());
            // Fallback: try very simple query
            try {
                $stmt = db()->query('SELECT DISTINCT genre FROM books WHERE genre IS NOT NULL ORDER BY genre ASC');
                $genres = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $filtered = [];
                foreach ($genres as $g) {
                    $t = trim((string)$g);
                    if (!empty($t)) $filtered[] = $t;
                }
                return array_values(array_unique($filtered));
            } catch (\PDOException $e2) {
                error_log('Fallback genre query also failed: ' . $e2->getMessage());
                return [];
            }
        }
    }

    public static function authors(): array
    {
        $hasTranslations = self::translationsTableExists();
        if ($hasTranslations) {
            $language = get_language();
            $stmt = db()->prepare('
                SELECT DISTINCT COALESCE(bt.author, b.author) AS author
                FROM books b
                LEFT JOIN book_translations bt ON bt.book_id = b.book_id AND bt.language = :lang
                WHERE COALESCE(bt.author, b.author) IS NOT NULL 
                AND CHAR_LENGTH(COALESCE(bt.author, b.author)) > 0
                ORDER BY author ASC
            ');
            $stmt->execute(['lang' => $language]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } else {
            $stmt = db()->query('SELECT DISTINCT author FROM books WHERE author IS NOT NULL AND CHAR_LENGTH(author) > 0 ORDER BY author ASC');
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
    }

    public static function find(int $bookId): ?array
    {
        $stmt = db()->prepare('SELECT * FROM books WHERE book_id = :id LIMIT 1');
        $stmt->execute(['id' => $bookId]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        return $book ?: null;
    }

    public static function detail(int $bookId): ?array
    {
        $hasTranslations = self::translationsTableExists();
        
        if ($hasTranslations) {
            $language = get_language();
            $stmt = db()->prepare('
                SELECT 
                    b.*,
                    bt.title AS trans_title,
                    bt.author AS trans_author,
                    bt.description AS trans_description,
                    bt.genre AS trans_genre,
                    (
                        SELECT IFNULL(AVG(r1.score), 0)
                        FROM ratings r1
                        WHERE r1.book_id = b.book_id
                    ) AS avg_rating,
                    (
                        SELECT COUNT(*)
                        FROM ratings r2
                        WHERE r2.book_id = b.book_id
                    ) AS ratings_count
                FROM books b
                LEFT JOIN book_translations bt ON bt.book_id = b.book_id AND bt.language = :lang
                WHERE b.book_id = :id
                LIMIT 1
            ');
            $stmt->execute(['id' => $bookId, 'lang' => $language]);
            $book = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$book) {
                return null;
            }
            
            // Apply translations
            if (!empty($book['trans_title'])) {
                $book['title'] = $book['trans_title'];
            }
            if (!empty($book['trans_author'])) {
                $book['author'] = $book['trans_author'];
            }
            if (!empty($book['trans_description'])) {
                $book['description'] = $book['trans_description'];
            }
            if (!empty($book['trans_genre'])) {
                $book['genre'] = $book['trans_genre'];
            }
            unset($book['trans_title'], $book['trans_author'], $book['trans_description'], $book['trans_genre']);
        } else {
        $stmt = db()->prepare('
            SELECT 
                b.*,
                (
                    SELECT IFNULL(AVG(r1.score), 0)
                    FROM ratings r1
                    WHERE r1.book_id = b.book_id
                ) AS avg_rating,
                (
                    SELECT COUNT(*)
                    FROM ratings r2
                    WHERE r2.book_id = b.book_id
                ) AS ratings_count
            FROM books b
            WHERE b.book_id = :id
            LIMIT 1
        ');
        $stmt->execute(['id' => $bookId]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return $book ?: null;
    }

    public static function create(array $data): int
    {
        // Insert base book record (use English as default)
        $stmt = db()->prepare('
            INSERT INTO books (title, author, description, genre, cover_image, book_file, isbn, pages, publication_year, publisher, language)
            VALUES (:title, :author, :description, :genre, :cover_image, :book_file, :isbn, :pages, :publication_year, :publisher, :language)
        ');
        
        // Get English values
        $titleEn = $data['title_en'] ?? $data['title'] ?? '';
        $authorEn = $data['author_en'] ?? $data['author'] ?? '';
        $descriptionEn = $data['description_en'] ?? $data['description'] ?? null;
        $genreEn = $data['genre_en'] ?? $data['genre'] ?? null;
        
        // Ensure genre is not empty string - convert to null if empty
        $genreEn = !empty(trim((string)($genreEn ?? ''))) ? trim((string)$genreEn) : null;
        
        $stmt->execute([
            'title' => $titleEn,
            'author' => $authorEn,
            'description' => $descriptionEn,
            'genre' => $genreEn,
            'cover_image' => $data['cover_image'] ?? null,
            'book_file' => $data['book_file'] ?? null,
            'isbn' => $data['isbn'] ?? null,
            'pages' => $data['pages'] ?? null,
            'publication_year' => $data['publication_year'] ?? null,
            'publisher' => $data['publisher'] ?? null,
            'language' => $data['language'] ?? 'en',
        ]);
        $bookId = (int) db()->lastInsertId();
        
        // Save translations if table exists
        if (self::translationsTableExists()) {
            $languages = ['en', 'ru', 'kk'];
            foreach ($languages as $lang) {
                $titleKey = "title_{$lang}";
                $authorKey = "author_{$lang}";
                $descriptionKey = "description_{$lang}";
                $genreKey = "genre_{$lang}";
                
                // Use language-specific fields if provided, otherwise use default
                $title = $data[$titleKey] ?? ($lang === 'en' ? $titleEn : '');
                $author = $data[$authorKey] ?? ($lang === 'en' ? $authorEn : '');
                $description = $data[$descriptionKey] ?? ($lang === 'en' ? $descriptionEn : null);
                $genre = $data[$genreKey] ?? ($lang === 'en' ? $genreEn : null);
                
                // Ensure genre is not empty string
                $genre = !empty(trim((string)($genre ?? ''))) ? trim((string)$genre) : null;
                
                if ($title || $author) {
                    try {
                        $stmt = db()->prepare('
                            INSERT INTO book_translations (book_id, language, title, author, description, genre)
                            VALUES (:book_id, :language, :title, :author, :description, :genre)
                            ON DUPLICATE KEY UPDATE
                                title = VALUES(title),
                                author = VALUES(author),
                                description = VALUES(description),
                                genre = VALUES(genre),
                                updated_at = NOW()
                        ');
                        $stmt->execute([
                            'book_id' => $bookId,
                            'language' => $lang,
                            'title' => $title,
                            'author' => $author,
                            'description' => $description,
                            'genre' => $genre,
                        ]);
                    } catch (\PDOException $e) {
                        // Silently fail if table doesn't exist or other error
                        error_log('Failed to save translation: ' . $e->getMessage());
                    }
                }
            }
        }
        
        return $bookId;
    }

    public static function update(int $bookId, array $data): void
    {
        $fields = [];
        $params = ['id' => $bookId];

        $allowed = ['cover_image', 'book_file', 'isbn', 'pages', 'publication_year', 'publisher', 'language'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = :{$field}";
                $params[$field] = $data[$field];
            }
        }

        if ($fields) {
        $sql = 'UPDATE books SET ' . implode(', ', $fields) . ', updated_at = NOW() WHERE book_id = :id';
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        }
        
        // Update translations if table exists
        if (self::translationsTableExists()) {
            $languages = ['en', 'ru', 'kk'];
            foreach ($languages as $lang) {
                $titleKey = "title_{$lang}";
                $authorKey = "author_{$lang}";
                $descriptionKey = "description_{$lang}";
                $genreKey = "genre_{$lang}";
                
                if (isset($data[$titleKey]) || isset($data[$authorKey]) || isset($data[$descriptionKey]) || isset($data[$genreKey])) {
                    $title = $data[$titleKey] ?? null;
                    $author = $data[$authorKey] ?? null;
                    $description = $data[$descriptionKey] ?? null;
                    $genre = $data[$genreKey] ?? null;
                    
                    try {
                        $stmt = db()->prepare('
                            INSERT INTO book_translations (book_id, language, title, author, description, genre)
                            VALUES (:book_id, :language, :title, :author, :description, :genre)
                            ON DUPLICATE KEY UPDATE
                                title = COALESCE(VALUES(title), title),
                                author = COALESCE(VALUES(author), author),
                                description = COALESCE(VALUES(description), description),
                                genre = COALESCE(VALUES(genre), genre),
                                updated_at = NOW()
                        ');
                        $stmt->execute([
                            'book_id' => $bookId,
                            'language' => $lang,
                            'title' => $title,
                            'author' => $author,
                            'description' => $description,
                            'genre' => $genre,
                        ]);
                    } catch (\PDOException $e) {
                        // Silently fail if table doesn't exist or other error
                        error_log('Failed to update translation: ' . $e->getMessage());
                    }
                }
            }
        }
    }
    
    /**
     * Get all translations for a book
     */
    public static function getTranslations(int $bookId): array
    {
        if (!self::translationsTableExists()) {
            return [];
        }
        
        try {
            $stmt = db()->prepare('
                SELECT language, title, author, description, genre
                FROM book_translations
                WHERE book_id = :book_id
                ORDER BY language
            ');
            $stmt->execute(['book_id' => $bookId]);
            $translations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $result = [];
            foreach ($translations as $trans) {
                $result[$trans['language']] = [
                    'title' => $trans['title'],
                    'author' => $trans['author'],
                    'description' => $trans['description'],
                    'genre' => $trans['genre'],
                ];
            }
            
            return $result;
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function delete(int $bookId): void
    {
        $book = self::find($bookId);
        if ($book) {
            if ($book['cover_image']) {
                delete_media($book['cover_image']);
            }
            if ($book['book_file']) {
                delete_media($book['book_file']);
            }
        }

        $stmt = db()->prepare('DELETE FROM books WHERE book_id = :id');
        $stmt->execute(['id' => $bookId]);
    }

    public static function count(): int
    {
        $stmt = db()->query('SELECT COUNT(*) FROM books');
        return (int) $stmt->fetchColumn();
    }

    public static function adminPaginate(int $perPage = 15, int $page = 1): array
    {
        $countSql = 'SELECT COUNT(*) FROM books';
        $stmt = db()->query($countSql);
        $total = (int) $stmt->fetchColumn();

        $offset = max(0, ($page - 1) * $perPage);

        $hasTranslations = self::translationsTableExists();
        
        if ($hasTranslations) {
            $language = get_language();
            $sql = '
                SELECT 
                    b.*,
                    bt.title AS trans_title,
                    bt.author AS trans_author,
                    bt.description AS trans_description,
                    bt.genre AS trans_genre,
                    (
                        SELECT IFNULL(AVG(r1.score), 0)
                        FROM ratings r1
                        WHERE r1.book_id = b.book_id
                    ) AS avg_rating,
                    (
                        SELECT COUNT(*)
                        FROM ratings r2
                        WHERE r2.book_id = b.book_id
                    ) AS ratings_count
                FROM books b
                LEFT JOIN book_translations bt ON bt.book_id = b.book_id AND bt.language = :lang
                ORDER BY b.created_at DESC
                LIMIT :limit OFFSET :offset
            ';

            $stmt = db()->prepare($sql);
            $stmt->bindValue(':lang', $language);
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Apply translations
            foreach ($books as &$book) {
                if (!empty($book['trans_title'])) {
                    $book['title'] = $book['trans_title'];
                }
                if (!empty($book['trans_author'])) {
                    $book['author'] = $book['trans_author'];
                }
                if (!empty($book['trans_description'])) {
                    $book['description'] = $book['trans_description'];
                }
                if (!empty($book['trans_genre'])) {
                    $book['genre'] = $book['trans_genre'];
                }
                unset($book['trans_title'], $book['trans_author'], $book['trans_description'], $book['trans_genre']);
            }
        } else {
        $sql = '
            SELECT 
                b.*,
                (
                    SELECT IFNULL(AVG(r1.score), 0)
                    FROM ratings r1
                    WHERE r1.book_id = b.book_id
                ) AS avg_rating,
                (
                    SELECT COUNT(*)
                    FROM ratings r2
                    WHERE r2.book_id = b.book_id
                ) AS ratings_count
            FROM books b
            ORDER BY b.created_at DESC
            LIMIT :limit OFFSET :offset
        ';

        $stmt = db()->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return [
            'data' => $books,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => max(1, (int) ceil($total / $perPage)),
            ],
        ];
    }
}

