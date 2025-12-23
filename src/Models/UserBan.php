<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class UserBan
{
    public static function ban(int $userId, int $bannedBy, string $reason): void
    {
        db()->beginTransaction();
        try {
            // Create ban record
            $stmt = db()->prepare('
                INSERT INTO user_bans (user_id, banned_by, reason, is_active)
                VALUES (:user_id, :banned_by, :reason, TRUE)
            ');
            $stmt->execute([
                'user_id' => $userId,
                'banned_by' => $bannedBy,
                'reason' => $reason,
            ]);

            // Update user record
            $stmt = db()->prepare('
                UPDATE users 
                SET is_banned = TRUE, 
                    ban_reason = :reason, 
                    banned_at = NOW(), 
                    banned_by = :banned_by
                WHERE user_id = :user_id
            ');
            $stmt->execute([
                'user_id' => $userId,
                'reason' => $reason,
                'banned_by' => $bannedBy,
            ]);

            db()->commit();
        } catch (\Exception $e) {
            db()->rollBack();
            throw $e;
        }
    }

    public static function unban(int $userId, int $unbannedBy): void
    {
        db()->beginTransaction();
        try {
            // Update active ban records
            $stmt = db()->prepare('
                UPDATE user_bans 
                SET is_active = FALSE, 
                    unbanned_at = NOW(), 
                    unbanned_by = :unbanned_by
                WHERE user_id = :user_id AND is_active = TRUE
            ');
            $stmt->execute([
                'user_id' => $userId,
                'unbanned_by' => $unbannedBy,
            ]);

            // Update user record
            $stmt = db()->prepare('
                UPDATE users 
                SET is_banned = FALSE, 
                    ban_reason = NULL, 
                    banned_at = NULL, 
                    banned_by = NULL
                WHERE user_id = :user_id
            ');
            $stmt->execute(['user_id' => $userId]);

            db()->commit();
        } catch (\Exception $e) {
            db()->rollBack();
            throw $e;
        }
    }

    public static function getHistory(int $userId): array
    {
        $stmt = db()->prepare('
            SELECT ub.*,
                   banned_by_user.email as banned_by_email,
                   banned_by_user.first_name as banned_by_first_name,
                   unbanned_by_user.email as unbanned_by_email,
                   unbanned_by_user.first_name as unbanned_by_first_name
            FROM user_bans ub
            LEFT JOIN users banned_by_user ON ub.banned_by = banned_by_user.user_id
            LEFT JOIN users unbanned_by_user ON ub.unbanned_by = unbanned_by_user.user_id
            WHERE ub.user_id = :user_id
            ORDER BY ub.banned_at DESC
        ');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getActiveBans(): array
    {
        $stmt = db()->prepare('
            SELECT ub.*,
                   u.email, u.first_name, u.last_name,
                   banned_by_user.email as banned_by_email,
                   banned_by_user.first_name as banned_by_first_name
            FROM user_bans ub
            JOIN users u ON ub.user_id = u.user_id
            LEFT JOIN users banned_by_user ON ub.banned_by = banned_by_user.user_id
            WHERE ub.is_active = TRUE
            ORDER BY ub.banned_at DESC
        ');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

