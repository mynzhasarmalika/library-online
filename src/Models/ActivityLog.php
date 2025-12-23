<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class ActivityLog
{
    public static function log(string $action, ?string $entityType = null, ?int $entityId = null, ?string $description = null, ?int $userId = null): void
    {
        $stmt = db()->prepare('
            INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, user_agent)
            VALUES (:user_id, :action, :entity_type, :entity_id, :description, :ip_address, :user_agent)
        ');
        $stmt->execute([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ]);
    }

    public static function paginate(int $perPage = 50, int $page = 1, ?string $action = null, ?int $userId = null): array
    {
        $conditions = [];
        $params = [];

        if ($action) {
            $conditions[] = 'action = :action';
            $params['action'] = $action;
        }

        if ($userId) {
            $conditions[] = 'user_id = :user_id';
            $params['user_id'] = $userId;
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $countSql = "SELECT COUNT(*) FROM activity_logs {$where}";
        $stmt = db()->prepare($countSql);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $offset = max(0, ($page - 1) * $perPage);

        $sql = "
            SELECT 
                al.*,
                u.email as user_email,
                u.first_name,
                u.last_name
            FROM activity_logs al
            LEFT JOIN users u ON al.user_id = u.user_id
            {$where}
            ORDER BY al.created_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = db()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => max(1, (int) ceil($total / $perPage)),
            ],
        ];
    }

    public static function getRecent(int $limit = 20): array
    {
        $stmt = db()->prepare("
            SELECT 
                al.*,
                u.email as user_email,
                u.first_name,
                u.last_name
            FROM activity_logs al
            LEFT JOIN users u ON al.user_id = u.user_id
            ORDER BY al.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

