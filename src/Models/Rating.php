<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class Rating
{
    public static function forBookAndUser(int $bookId, int $userId): ?array
    {
        $stmt = db()->prepare('SELECT * FROM ratings WHERE book_id = :book AND user_id = :user LIMIT 1');
        $stmt->execute(['book' => $bookId, 'user' => $userId]);
        $rating = $stmt->fetch(PDO::FETCH_ASSOC);
        return $rating ?: null;
    }

    public static function set(int $bookId, int $userId, int $score): void
    {
        $exists = self::forBookAndUser($bookId, $userId);

        if ($exists) {
            $stmt = db()->prepare('UPDATE ratings SET score = :score, updated_at = NOW() WHERE id = :id');
            $stmt->execute(['score' => $score, 'id' => $exists['id']]);
            return;
        }

        $stmt = db()->prepare('INSERT INTO ratings (user_id, book_id, score) VALUES (:user, :book, :score)');
        $stmt->execute(['user' => $userId, 'book' => $bookId, 'score' => $score]);
    }
}

