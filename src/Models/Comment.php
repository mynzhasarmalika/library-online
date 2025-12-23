<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class Comment
{
    public static function forBook(int $bookId, ?int $userId = null): array
    {
        $stmt = db()->prepare('
            SELECT 
                c.id,
                c.comment,
                c.created_at,
                c.user_id,
                u.email,
                u.first_name,
                u.avatar,
                (SELECT COUNT(*) FROM comment_likes cl WHERE cl.comment_id = c.id) as likes_count
            FROM comments c
            JOIN users u ON u.user_id = c.user_id
            WHERE c.book_id = :book
            ORDER BY c.created_at DESC
        ');
        $stmt->execute(['book' => $bookId]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Add like status for current user
        if ($userId && !empty($comments)) {
            $commentIds = array_column($comments, 'id');
            $likes = \App\Models\CommentLike::getLikesForComments($commentIds, $userId);
            foreach ($comments as &$comment) {
                $comment['is_liked'] = $likes[(int) $comment['id']]['is_liked'] ?? false;
                $comment['likes_count'] = (int) ($likes[(int) $comment['id']]['likes_count'] ?? $comment['likes_count'] ?? 0);
            }
        }

        return $comments;
    }

    public static function create(int $bookId, int $userId, string $content): void
    {
        $stmt = db()->prepare('INSERT INTO comments (book_id, user_id, comment) VALUES (:book, :user, :content)');
        $stmt->execute(['book' => $bookId, 'user' => $userId, 'content' => $content]);
    }

    public static function find(int $commentId): ?array
    {
        $stmt = db()->prepare('SELECT * FROM comments WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $commentId]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);
        return $comment ?: null;
    }

    public static function delete(int $commentId): void
    {
        $stmt = db()->prepare('DELETE FROM comments WHERE id = :id');
        $stmt->execute(['id' => $commentId]);
    }

    public static function count(): int
    {
        $stmt = db()->query('SELECT COUNT(*) FROM comments');
        return (int) $stmt->fetchColumn();
    }

    public static function adminPaginate(int $perPage = 20, int $page = 1): array
    {
        $countSql = 'SELECT COUNT(*) FROM comments';
        $stmt = db()->query($countSql);
        $total = (int) $stmt->fetchColumn();

        $offset = max(0, ($page - 1) * $perPage);

        $sql = '
            SELECT 
                c.*,
                u.email,
                u.first_name,
                u.avatar,
                b.title AS book_title,
                b.book_id
            FROM comments c
            JOIN users u ON u.user_id = c.user_id
            JOIN books b ON b.book_id = c.book_id
            ORDER BY c.created_at DESC
            LIMIT :limit OFFSET :offset
        ';

        $stmt = db()->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $comments,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => max(1, (int) ceil($total / $perPage)),
            ],
        ];
    }
}

