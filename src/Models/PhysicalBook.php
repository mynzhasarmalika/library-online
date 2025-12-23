<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class PhysicalBook
{
    public static function find(int $id): ?array
    {
        $stmt = db()->prepare('
            SELECT pb.*, b.title, b.author, b.genre
            FROM physical_books pb
            JOIN books b ON pb.book_id = b.book_id
            WHERE pb.physical_book_id = :id
            LIMIT 1
        ');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function findByBook(int $bookId): array
    {
        $stmt = db()->prepare('
            SELECT pb.*, 
                   (SELECT COUNT(*) FROM book_loans bl WHERE bl.physical_book_id = pb.physical_book_id AND bl.returned_at IS NULL) as active_loans,
                   (SELECT COUNT(*) FROM book_reservations br WHERE br.physical_book_id = pb.physical_book_id AND br.status IN ("pending","ready")) as active_reservations
            FROM physical_books pb
            WHERE pb.book_id = :book_id
            ORDER BY pb.inventory_number
        ');
        $stmt->execute(['book_id' => $bookId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(array $data): int
    {
        $stmt = db()->prepare('
            INSERT INTO physical_books (book_id, inventory_number, location, is_available, notes)
            VALUES (:book_id, :inventory_number, :location, :is_available, :notes)
        ');
        $stmt->execute([
            'book_id' => $data['book_id'],
            'inventory_number' => $data['inventory_number'],
            'location' => $data['location'] ?? null,
            'is_available' => $data['is_available'] ?? true,
            'notes' => $data['notes'] ?? null,
        ]);
        return (int) db()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $fields = [];
        $params = ['id' => $id];

        $allowed = ['inventory_number', 'location', 'is_available', 'notes'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = :{$field}";
                $params[$field] = $data[$field];
            }
        }

        if (!$fields) {
            return;
        }

        $sql = 'UPDATE physical_books SET ' . implode(', ', $fields) . ', updated_at = NOW() WHERE physical_book_id = :id';
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
    }

    public static function delete(int $id): void
    {
        $stmt = db()->prepare('DELETE FROM physical_books WHERE physical_book_id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function getAvailable(int $bookId): array
    {
        $stmt = db()->prepare('
            SELECT pb.*
            FROM physical_books pb
            WHERE pb.book_id = :book_id 
            AND pb.is_available = TRUE
            AND pb.physical_book_id NOT IN (
                SELECT bl.physical_book_id 
                FROM book_loans bl 
                WHERE bl.returned_at IS NULL
            )
        ');
        $stmt->execute(['book_id' => $bookId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function countAvailable(int $bookId): int
    {
        $stmt = db()->prepare('
            SELECT COUNT(*)
            FROM physical_books pb
            WHERE pb.book_id = :book_id 
            AND pb.is_available = TRUE
            AND pb.physical_book_id NOT IN (
                SELECT bl.physical_book_id 
                FROM book_loans bl 
                WHERE bl.returned_at IS NULL
            )
        ');
        $stmt->execute(['book_id' => $bookId]);
        return (int) $stmt->fetchColumn();
    }

    public static function paginate(int $perPage = 15, int $page = 1, ?string $search = null): array
    {
        $conditions = [];
        $params = [];

        if ($search) {
            $conditions[] = '(b.title LIKE :search OR b.author LIKE :search OR pb.inventory_number LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $countSql = "
            SELECT COUNT(*)
            FROM physical_books pb
            JOIN books b ON pb.book_id = b.book_id
            {$where}
        ";
        $stmt = db()->prepare($countSql);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $offset = max(0, ($page - 1) * $perPage);

        $sql = "
            SELECT pb.*, b.title, b.author, b.genre,
                   (SELECT COUNT(*) FROM book_loans bl WHERE bl.physical_book_id = pb.physical_book_id AND bl.returned_at IS NULL) as active_loans,
                   (SELECT br.user_id FROM book_reservations br WHERE br.physical_book_id = pb.physical_book_id AND br.status IN ('pending','ready') ORDER BY br.reserved_at DESC LIMIT 1) AS reserved_user_id,
                   (SELECT u.email FROM book_reservations br JOIN users u ON u.user_id = br.user_id WHERE br.physical_book_id = pb.physical_book_id AND br.status IN ('pending','ready') ORDER BY br.reserved_at DESC LIMIT 1) AS reserved_user_email
            FROM physical_books pb
            JOIN books b ON pb.book_id = b.book_id
            {$where}
            ORDER BY pb.created_at DESC
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
}

