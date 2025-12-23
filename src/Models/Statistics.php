<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class Statistics
{
    public static function getUserReadingStats(int $userId): array
    {
        // Completed books count
        $stmt = db()->prepare('
            SELECT COUNT(*) 
            FROM progress 
            WHERE user_id = :user_id AND status = "completed"
        ');
        $stmt->execute(['user_id' => $userId]);
        $completedBooks = (int) $stmt->fetchColumn();

        // Total pages read (estimated based on progress)
        $stmt = db()->prepare('
            SELECT SUM(
                CASE 
                    WHEN b.pages IS NOT NULL THEN (b.pages * p.progress_percentage / 100)
                    ELSE 0
                END
            ) as total_pages
            FROM progress p
            JOIN books b ON p.book_id = b.book_id
            WHERE p.user_id = :user_id AND p.status = "completed"
        ');
        $stmt->execute(['user_id' => $userId]);
        $totalPages = (float) ($stmt->fetchColumn() ?? 0);

        // Average rating given
        $stmt = db()->prepare('
            SELECT AVG(score) 
            FROM ratings 
            WHERE user_id = :user_id
        ');
        $stmt->execute(['user_id' => $userId]);
        $avgRating = (float) ($stmt->fetchColumn() ?? 0);

        // Currently reading count
        $stmt = db()->prepare('
            SELECT COUNT(*) 
            FROM progress 
            WHERE user_id = :user_id AND status = "reading"
        ');
        $stmt->execute(['user_id' => $userId]);
        $currentlyReading = (int) $stmt->fetchColumn();

        // Favorites count
        $stmt = db()->prepare('
            SELECT COUNT(*) 
            FROM favorites 
            WHERE user_id = :user_id
        ');
        $stmt->execute(['user_id' => $userId]);
        $favoritesCount = (int) $stmt->fetchColumn();

        // Top genres
        $stmt = db()->prepare('
            SELECT b.genre, COUNT(*) as count
            FROM progress p
            JOIN books b ON p.book_id = b.book_id
            WHERE p.user_id = :user_id AND p.status = "completed" AND b.genre IS NOT NULL
            GROUP BY b.genre
            ORDER BY count DESC
            LIMIT 5
        ');
        $stmt->execute(['user_id' => $userId]);
        $topGenres = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Reading activity by month
        $stmt = db()->prepare('
            SELECT 
                DATE_FORMAT(updated_at, "%Y-%m") as month,
                COUNT(*) as books_completed
            FROM progress
            WHERE user_id = :user_id 
            AND status = "completed"
            AND updated_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(updated_at, "%Y-%m")
            ORDER BY month ASC
        ');
        $stmt->execute(['user_id' => $userId]);
        $monthlyActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'completed_books' => $completedBooks,
            'total_pages' => (int) $totalPages,
            'avg_rating' => round($avgRating, 2),
            'currently_reading' => $currentlyReading,
            'favorites_count' => $favoritesCount,
            'top_genres' => $topGenres,
            'monthly_activity' => $monthlyActivity,
        ];
    }

    public static function getPopularBooks(int $limit = 10): array
    {
        $stmt = db()->prepare('
            SELECT 
                b.*,
                (SELECT IFNULL(AVG(r.score), 0) FROM ratings r WHERE r.book_id = b.book_id) AS avg_rating,
                (SELECT COUNT(*) FROM ratings r WHERE r.book_id = b.book_id) AS ratings_count,
                (SELECT COUNT(*) FROM favorites f WHERE f.book_id = b.book_id) AS favorites_count,
                (SELECT COUNT(*) FROM progress p WHERE p.book_id = b.book_id AND p.status = "completed") AS completed_count
            FROM books b
            ORDER BY 
                (SELECT COUNT(*) FROM favorites f WHERE f.book_id = b.book_id) DESC,
                (SELECT IFNULL(AVG(r.score), 0) FROM ratings r WHERE r.book_id = b.book_id) DESC
            LIMIT :limit
        ');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getActiveReaders(int $limit = 10): array
    {
        $stmt = db()->prepare('
            SELECT 
                u.user_id,
                u.email,
                u.first_name,
                u.last_name,
                COUNT(DISTINCT p.book_id) as books_read,
                SUM(CASE WHEN b.pages IS NOT NULL THEN (b.pages * p.progress_percentage / 100) ELSE 0 END) as pages_read
            FROM users u
            JOIN progress p ON u.user_id = p.user_id
            JOIN books b ON p.book_id = b.book_id
            WHERE p.status = "completed"
            AND p.updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY u.user_id
            ORDER BY books_read DESC, pages_read DESC
            LIMIT :limit
        ');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getGenreStatistics(): array
    {
        $stmt = db()->query('
            SELECT 
                genre,
                COUNT(*) as total_books,
                (SELECT COUNT(*) FROM favorites f JOIN books b2 ON f.book_id = b2.book_id WHERE b2.genre = b.genre) as total_favorites,
                (SELECT IFNULL(AVG(r.score), 0) FROM ratings r JOIN books b3 ON r.book_id = b3.book_id WHERE b3.genre = b.genre) as avg_rating
            FROM books b
            WHERE genre IS NOT NULL
            GROUP BY genre
            ORDER BY total_books DESC
        ');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getLoanStatistics(string $period = 'month'): array
    {
        $dateFormat = match($period) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            'year' => '%Y',
            default => '%Y-%m',
        };

        $stmt = db()->prepare("
            SELECT 
                DATE_FORMAT(loaned_at, :format) as period,
                COUNT(*) as loans_count,
                COUNT(CASE WHEN returned_at IS NULL THEN 1 END) as active_loans,
                COUNT(CASE WHEN due_date < CURDATE() AND returned_at IS NULL THEN 1 END) as overdue_loans
            FROM book_loans
            WHERE loaned_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(loaned_at, :format)
            ORDER BY period ASC
        ");
        $stmt->execute(['format' => $dateFormat]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getSimilarBooks(int $bookId, int $limit = 5): array
    {
        $book = Book::find($bookId);
        if (!$book || empty($book['genre'])) {
            return [];
        }

        $stmt = db()->prepare('
            SELECT 
                b.*,
                (SELECT IFNULL(AVG(r.score), 0) FROM ratings r WHERE r.book_id = b.book_id) AS avg_rating,
                (SELECT COUNT(*) FROM ratings r WHERE r.book_id = b.book_id) AS ratings_count
            FROM books b
            WHERE b.genre = :genre
            AND b.book_id != :book_id
            ORDER BY 
                (SELECT IFNULL(AVG(r.score), 0) FROM ratings r WHERE r.book_id = b.book_id) DESC,
                (SELECT COUNT(*) FROM ratings r WHERE r.book_id = b.book_id) DESC
            LIMIT :limit
        ');
        $stmt->bindValue(':genre', $book['genre']);
        $stmt->bindValue(':book_id', $bookId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getRecommendedBooks(int $userId, int $limit = 10): array
    {
        // Get user's favorite genres
        $stmt = db()->prepare('
            SELECT b.genre, COUNT(*) as count
            FROM favorites f
            JOIN books b ON f.book_id = b.book_id
            WHERE f.user_id = :user_id AND b.genre IS NOT NULL
            GROUP BY b.genre
            ORDER BY count DESC
            LIMIT 3
        ');
        $stmt->execute(['user_id' => $userId]);
        $favoriteGenres = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($favoriteGenres)) {
            // If no favorites, return popular books
            return self::getPopularBooks($limit);
        }

        $placeholders = implode(',', array_fill(0, count($favoriteGenres), '?'));
        
        $stmt = db()->prepare("
            SELECT 
                b.*,
                (SELECT IFNULL(AVG(r.score), 0) FROM ratings r WHERE r.book_id = b.book_id) AS avg_rating,
                (SELECT COUNT(*) FROM ratings r WHERE r.book_id = b.book_id) AS ratings_count
            FROM books b
            WHERE b.genre IN ({$placeholders})
            AND b.book_id NOT IN (
                SELECT book_id FROM favorites WHERE user_id = ?
            )
            AND b.book_id NOT IN (
                SELECT book_id FROM progress WHERE user_id = ? AND status = 'completed'
            )
            ORDER BY 
                (SELECT IFNULL(AVG(r.score), 0) FROM ratings r WHERE r.book_id = b.book_id) DESC
            LIMIT ?
        ");
        
        $params = array_merge($favoriteGenres, [$userId, $userId]);
        $paramIndex = 1;
        foreach ($params as $value) {
            $stmt->bindValue($paramIndex, $value);
            $paramIndex++;
        }
        $stmt->bindValue($paramIndex, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

