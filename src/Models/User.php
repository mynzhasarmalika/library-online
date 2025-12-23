<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class User
{
    public static function findByEmail(string $email): ?array
    {
        $stmt = db()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public static function find(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM users WHERE user_id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = db()->prepare('
            INSERT INTO users (email, password, password_hash, role, first_name, last_name, avatar)
            VALUES (:email, :password, :password_hash, :role, :first_name, :last_name, :avatar)
        ');

        $stmt->execute([
            'email' => $data['email'],
            'password' => $data['password'],
            'password_hash' => $data['password_hash'],
            'role' => $data['role'] ?? 'user',
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'avatar' => $data['avatar'] ?? null,
        ]);

        return (int) db()->lastInsertId();
    }

    public static function updateFields(int $userId, array $fields): void
    {
        if (!$fields) {
            return;
        }

        $columns = [];
        $params = ['id' => $userId];

        foreach ($fields as $column => $value) {
            $columns[] = "{$column} = :{$column}";
            $params[$column] = $value;
        }

        $sql = 'UPDATE users SET ' . implode(', ', $columns) . ', updated_at = NOW() WHERE user_id = :id';
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
    }

    public static function checkPassword(int $userId, string $plain): bool
    {
        $stmt = db()->prepare('SELECT password FROM users WHERE user_id = :id LIMIT 1');
        $stmt->execute(['id' => $userId]);
        $hash = $stmt->fetchColumn();
        return $hash ? password_verify($plain, $hash) : false;
    }

    public static function count(): int
    {
        $stmt = db()->query('SELECT COUNT(*) FROM users');
        return (int) $stmt->fetchColumn();
    }

    public static function paginate(int $perPage = 15, int $page = 1): array
    {
        $countSql = 'SELECT COUNT(*) FROM users';
        $stmt = db()->query($countSql);
        $total = (int) $stmt->fetchColumn();

        $offset = max(0, ($page - 1) * $perPage);

        $sql = 'SELECT * FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
        $stmt = db()->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $users,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => max(1, (int) ceil($total / $perPage)),
            ],
        ];
    }

    public static function delete(int $userId): void
    {
        $user = self::find($userId);
        if ($user && $user['avatar']) {
            delete_media($user['avatar']);
        }

        $stmt = db()->prepare('DELETE FROM users WHERE user_id = :id');
        $stmt->execute(['id' => $userId]);
    }
}

