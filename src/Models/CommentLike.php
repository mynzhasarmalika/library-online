<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class CommentLike
{
    public static function toggle(int $commentId, int $userId): bool
    {
        // Check if already liked
        $stmt = db()->prepare('SELECT id FROM comment_likes WHERE comment_id = :comment_id AND user_id = :user_id LIMIT 1');
        $stmt->execute(['comment_id' => $commentId, 'user_id' => $userId]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Unlike
            $stmt = db()->prepare('DELETE FROM comment_likes WHERE comment_id = :comment_id AND user_id = :user_id');
            $stmt->execute(['comment_id' => $commentId, 'user_id' => $userId]);
            return false;
        } else {
            // Like
            $stmt = db()->prepare('INSERT INTO comment_likes (comment_id, user_id) VALUES (:comment_id, :user_id)');
            $stmt->execute(['comment_id' => $commentId, 'user_id' => $userId]);
            return true;
        }
    }

    public static function count(int $commentId): int
    {
        $stmt = db()->prepare('SELECT COUNT(*) FROM comment_likes WHERE comment_id = :comment_id');
        $stmt->execute(['comment_id' => $commentId]);
        return (int) $stmt->fetchColumn();
    }

    public static function isLiked(int $commentId, int $userId): bool
    {
        $stmt = db()->prepare('SELECT COUNT(*) FROM comment_likes WHERE comment_id = :comment_id AND user_id = :user_id');
        $stmt->execute(['comment_id' => $commentId, 'user_id' => $userId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public static function getLikesForComments(array $commentIds, int $userId): array
    {
        if (empty($commentIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($commentIds), '?'));
        $stmt = db()->prepare("
            SELECT comment_id, COUNT(*) as likes_count,
                   SUM(CASE WHEN user_id = ? THEN 1 ELSE 0 END) as is_liked
            FROM comment_likes
            WHERE comment_id IN ({$placeholders})
            GROUP BY comment_id
        ");
        $params = array_merge([$userId], $commentIds);
        $stmt->execute($params);
        
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[(int) $row['comment_id']] = [
                'likes_count' => (int) $row['likes_count'],
                'is_liked' => (int) $row['is_liked'] > 0,
            ];
        }
        return $result;
    }
}

