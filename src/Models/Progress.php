<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class Progress
{
    public static function forBookAndUser(int $bookId, int $userId): ?array
    {
        $stmt = db()->prepare('SELECT * FROM progress WHERE book_id = :book AND user_id = :user LIMIT 1');
        $stmt->execute(['book' => $bookId, 'user' => $userId]);
        $progress = $stmt->fetch(PDO::FETCH_ASSOC);
        return $progress ?: null;
    }

    public static function upsert(int $bookId, int $userId, array $data): void
    {
        $existing = self::forBookAndUser($bookId, $userId);

        if ($existing) {
            $stmt = db()->prepare('
                UPDATE progress
                SET last_page = :last_page,
                    progress_percentage = :percentage,
                    status = :status,
                    updated_at = NOW()
                WHERE id = :id
            ');

            $stmt->execute([
                'last_page' => $data['last_page'],
                'percentage' => $data['progress_percentage'],
                'status' => $data['status'],
                'id' => $existing['id'],
            ]);
            return;
        }

        $stmt = db()->prepare('
            INSERT INTO progress (user_id, book_id, last_page, progress_percentage, status)
            VALUES (:user, :book, :last_page, :percentage, :status)
        ');
        $stmt->execute([
            'user' => $userId,
            'book' => $bookId,
            'last_page' => $data['last_page'],
            'percentage' => $data['progress_percentage'],
            'status' => $data['status'],
        ]);
    }
}

