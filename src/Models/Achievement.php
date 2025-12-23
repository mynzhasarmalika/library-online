<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class Achievement
{
    public static function checkAchievements(int $userId): array
    {
        $achievements = [];

        // Get user stats
        $stats = Statistics::getUserReadingStats($userId);

        // First Book
        if ($stats['completed_books'] >= 1) {
            $achievements[] = [
                'id' => 'first_book',
                'title' => 'First Steps',
                'description' => 'Completed your first book',
                'icon' => '📖',
                'unlocked' => true,
            ];
        }

        // Bookworm (10 books)
        if ($stats['completed_books'] >= 10) {
            $achievements[] = [
                'id' => 'bookworm',
                'title' => 'Bookworm',
                'description' => 'Completed 10 books',
                'icon' => '🐛',
                'unlocked' => true,
            ];
        }

        // Avid Reader (50 books)
        if ($stats['completed_books'] >= 50) {
            $achievements[] = [
                'id' => 'avid_reader',
                'title' => 'Avid Reader',
                'description' => 'Completed 50 books',
                'icon' => '📚',
                'unlocked' => true,
            ];
        }

        // Master Reader (100 books)
        if ($stats['completed_books'] >= 100) {
            $achievements[] = [
                'id' => 'master_reader',
                'title' => 'Master Reader',
                'description' => 'Completed 100 books',
                'icon' => '👑',
                'unlocked' => true,
            ];
        }

        // Page Turner (1000 pages)
        if ($stats['total_pages'] >= 1000) {
            $achievements[] = [
                'id' => 'page_turner',
                'title' => 'Page Turner',
                'description' => 'Read 1000 pages',
                'icon' => '📄',
                'unlocked' => true,
            ];
        }

        // Book Collector (10 favorites)
        if ($stats['favorites_count'] >= 10) {
            $achievements[] = [
                'id' => 'book_collector',
                'title' => 'Book Collector',
                'description' => 'Added 10 books to favorites',
                'icon' => '⭐',
                'unlocked' => true,
            ];
        }

        // Critic (10 ratings)
        $stmt = db()->prepare('SELECT COUNT(*) FROM ratings WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        $ratingsCount = (int) $stmt->fetchColumn();

        if ($ratingsCount >= 10) {
            $achievements[] = [
                'id' => 'critic',
                'title' => 'Critic',
                'description' => 'Rated 10 books',
                'icon' => '⭐',
                'unlocked' => true,
            ];
        }

        // Social Butterfly (10 comments)
        $stmt = db()->prepare('SELECT COUNT(*) FROM comments WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        $commentsCount = (int) $stmt->fetchColumn();

        if ($commentsCount >= 10) {
            $achievements[] = [
                'id' => 'social_butterfly',
                'title' => 'Social Butterfly',
                'description' => 'Left 10 comments',
                'icon' => '💬',
                'unlocked' => true,
            ];
        }

        return $achievements;
    }

    public static function getAllAchievements(): array
    {
        return [
            [
                'id' => 'first_book',
                'title' => 'First Steps',
                'description' => 'Complete your first book',
                'icon' => '📖',
                'requirement' => '1 completed book',
            ],
            [
                'id' => 'bookworm',
                'title' => 'Bookworm',
                'description' => 'Complete 10 books',
                'icon' => '🐛',
                'requirement' => '10 completed books',
            ],
            [
                'id' => 'avid_reader',
                'title' => 'Avid Reader',
                'description' => 'Complete 50 books',
                'icon' => '📚',
                'requirement' => '50 completed books',
            ],
            [
                'id' => 'master_reader',
                'title' => 'Master Reader',
                'description' => 'Complete 100 books',
                'icon' => '👑',
                'requirement' => '100 completed books',
            ],
            [
                'id' => 'page_turner',
                'title' => 'Page Turner',
                'description' => 'Read 1000 pages',
                'icon' => '📄',
                'requirement' => '1000 pages read',
            ],
            [
                'id' => 'book_collector',
                'title' => 'Book Collector',
                'description' => 'Add 10 books to favorites',
                'icon' => '⭐',
                'requirement' => '10 favorites',
            ],
            [
                'id' => 'critic',
                'title' => 'Critic',
                'description' => 'Rate 10 books',
                'icon' => '⭐',
                'requirement' => '10 ratings',
            ],
            [
                'id' => 'social_butterfly',
                'title' => 'Social Butterfly',
                'description' => 'Leave 10 comments',
                'icon' => '💬',
                'requirement' => '10 comments',
            ],
        ];
    }
}

