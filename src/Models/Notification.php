<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class Notification
{
    public static function create(array $data): int
    {
        $stmt = db()->prepare('
            INSERT INTO notifications (user_id, type, title, message, link)
            VALUES (:user_id, :type, :title, :message, :link)
        ');
        $stmt->execute([
            'user_id' => $data['user_id'] ?? null,
            'type' => $data['type'],
            'title' => $data['title'],
            'message' => $data['message'],
            'link' => $data['link'] ?? null,
        ]);
        return (int) db()->lastInsertId();
    }

    public static function forUser(int $userId, bool $unreadOnly = false): array
    {
        $where = $unreadOnly ? 'AND is_read = FALSE' : '';
        $stmt = db()->prepare("
            SELECT * FROM notifications
            WHERE user_id = :user_id {$where}
            ORDER BY created_at DESC
            LIMIT 50
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function markAsRead(int $notificationId, int $userId): void
    {
        $stmt = db()->prepare('
            UPDATE notifications
            SET is_read = TRUE
            WHERE id = :id AND user_id = :user_id
        ');
        $stmt->execute(['id' => $notificationId, 'user_id' => $userId]);
    }

    public static function markAllAsRead(int $userId): void
    {
        $stmt = db()->prepare('
            UPDATE notifications
            SET is_read = TRUE
            WHERE user_id = :user_id AND is_read = FALSE
        ');
        $stmt->execute(['user_id' => $userId]);
    }

    public static function countUnread(int $userId): int
    {
        $stmt = db()->prepare('
            SELECT COUNT(*) FROM notifications
            WHERE user_id = :user_id AND is_read = FALSE
        ');
        $stmt->execute(['user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    public static function notifyOverdue(int $userId, array $loan): void
    {
        self::create([
            'user_id' => $userId,
            'type' => 'overdue',
            'title' => 'Overdue Book',
            'message' => "The book '{$loan['title']}' is overdue. Please return it as soon as possible.",
            'link' => "/my-loans",
        ]);
    }

    public static function notifyReminder(int $userId, array $loan, int $daysLeft): void
    {
        self::create([
            'user_id' => $userId,
            'type' => 'reminder',
            'title' => 'Book Due Soon',
            'message' => "The book '{$loan['title']}' is due in {$daysLeft} day(s).",
            'link' => "/my-loans",
        ]);
    }

    public static function notifyBan(int $userId, string $reason): void
    {
        self::create([
            'user_id' => $userId,
            'type' => 'ban',
            'title' => 'Account Banned',
            'message' => "Your account has been banned. Reason: {$reason}",
            'link' => "/profile",
        ]);
    }

    public static function notifyUnban(int $userId): void
    {
        self::create([
            'user_id' => $userId,
            'type' => 'unban',
            'title' => 'Account Unbanned',
            'message' => 'Your account has been unbanned. You can now access all features.',
            'link' => "/",
        ]);
    }

    public static function notifyLibrarianOverdue(array $overdueLoans): void
    {
        // Notify all librarians about overdue loans
        $stmt = db()->query("
            SELECT user_id FROM users 
            WHERE role IN ('admin', 'librarian')
        ");
        $librarians = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $count = count($overdueLoans);
        foreach ($librarians as $librarianId) {
            self::create([
                'user_id' => (int) $librarianId,
                'type' => 'overdue_alert',
                'title' => 'Overdue Loans Alert',
                'message' => "There are {$count} overdue book loan(s) that need attention.",
                'link' => "/admin/library/loans?status=overdue",
            ]);
        }
    }

    /**
     * Send notification to all users (for announcements)
     */
    public static function sendToAllUsers(string $type, string $title, string $message, ?string $link = null): int
    {
        $stmt = db()->query("SELECT user_id FROM users WHERE role = 'user'");
        $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $count = 0;
        foreach ($userIds as $userId) {
            self::create([
                'user_id' => (int) $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'link' => $link,
            ]);
            $count++;
        }
        
        return $count;
    }
}

