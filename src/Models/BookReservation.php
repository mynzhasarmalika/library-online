<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use RuntimeException;

final class BookReservation
{
    private const ACTIVE_STATUSES = ['pending', 'ready'];

    public static function create(int $bookId, int $userId, ?string $notes = null): int
    {
        $availableCopies = PhysicalBook::getAvailable($bookId);
        if (empty($availableCopies)) {
            throw new RuntimeException('No available copies to reserve.');
        }

        $copy = $availableCopies[0];
        $expiresAt = date('Y-m-d H:i:s', strtotime('+2 days'));

        $stmt = db()->prepare('
            INSERT INTO book_reservations (book_id, physical_book_id, user_id, expires_at, notes)
            VALUES (:book_id, :physical_book_id, :user_id, :expires_at, :notes)
        ');
        $stmt->execute([
            'book_id' => $bookId,
            'physical_book_id' => $copy['physical_book_id'],
            'user_id' => $userId,
            'expires_at' => $expiresAt,
            'notes' => $notes,
        ]);

        PhysicalBook::update((int) $copy['physical_book_id'], ['is_available' => false]);

        return (int) db()->lastInsertId();
    }

    public static function hasActiveReservation(int $userId, int $bookId): bool
    {
        $stmt = db()->prepare('
            SELECT COUNT(*)
            FROM book_reservations
            WHERE user_id = :user AND book_id = :book AND status IN ("pending","ready")
        ');
        $stmt->execute(['user' => $userId, 'book' => $bookId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public static function forUser(int $userId): array
    {
        self::expireOldReservations();

        $stmt = db()->prepare('
            SELECT br.*, b.title, b.author, b.cover_image, pb.inventory_number, pb.location
            FROM book_reservations br
            JOIN books b ON br.book_id = b.book_id
            JOIN physical_books pb ON pb.physical_book_id = br.physical_book_id
            WHERE br.user_id = :user
            ORDER BY br.reserved_at DESC
        ');
        $stmt->execute(['user' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function userReservationForBook(int $userId, int $bookId): ?array
    {
        self::expireOldReservations();

        $stmt = db()->prepare('
            SELECT br.*, pb.inventory_number, pb.location
            FROM book_reservations br
            JOIN physical_books pb ON br.physical_book_id = pb.physical_book_id
            WHERE br.user_id = :user
            AND br.book_id = :book
            ORDER BY br.reserved_at DESC
            LIMIT 1
        ');
        $stmt->execute(['user' => $userId, 'book' => $bookId]);
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
        return $reservation ?: null;
    }

    public static function findAllActive(?string $status = null): array
    {
        self::expireOldReservations();

        $where = '';
        $params = [];
        
        if ($status && in_array($status, ['pending', 'ready', 'collected', 'cancelled', 'expired'], true)) {
            $where = 'WHERE br.status = :status';
            $params['status'] = $status;
        } else {
            $where = 'WHERE br.status IN ("pending", "ready")';
        }

        $stmt = db()->prepare("
            SELECT br.*, 
                   b.title, b.author, b.cover_image,
                   pb.inventory_number, pb.location,
                   u.email, u.first_name, u.last_name
            FROM book_reservations br
            JOIN books b ON br.book_id = b.book_id
            JOIN physical_books pb ON pb.physical_book_id = br.physical_book_id
            JOIN users u ON br.user_id = u.user_id
            {$where}
            ORDER BY br.reserved_at DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $reservationId): ?array
    {
        $stmt = db()->prepare('
            SELECT br.*, 
                   b.title, b.author,
                   pb.inventory_number, pb.location,
                   u.email, u.first_name, u.last_name
            FROM book_reservations br
            JOIN books b ON br.book_id = b.book_id
            JOIN physical_books pb ON pb.physical_book_id = br.physical_book_id
            JOIN users u ON br.user_id = u.user_id
            WHERE br.reservation_id = :id
            LIMIT 1
        ');
        $stmt->execute(['id' => $reservationId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function markReady(int $reservationId): bool
    {
        $reservation = self::find($reservationId);
        if (!$reservation || $reservation['status'] !== 'pending') {
            return false;
        }

        $stmt = db()->prepare('
            UPDATE book_reservations
            SET status = "ready", handled_at = NOW()
            WHERE reservation_id = :id
        ');
        $stmt->execute(['id' => $reservationId]);
        return true;
    }

    public static function cancel(int $reservationId, int $userId): bool
    {
        $reservation = self::findForUser($reservationId, $userId);
        if (!$reservation || !in_array($reservation['status'], self::ACTIVE_STATUSES, true)) {
            return false;
        }

        $stmt = db()->prepare('
            UPDATE book_reservations
            SET status = "cancelled", cancelled_at = NOW()
            WHERE reservation_id = :id
        ');
        $stmt->execute(['id' => $reservationId]);

        self::releasePhysicalBook((int) $reservation['physical_book_id']);

        return true;
    }

    public static function markCollectedForBook(int $physicalBookId, int $userId): void
    {
        $stmt = db()->prepare('
            UPDATE book_reservations
            SET status = "collected", handled_at = NOW()
            WHERE physical_book_id = :physical_book_id
            AND user_id = :user_id
            AND status IN ("pending","ready")
        ');
        $stmt->execute([
            'physical_book_id' => $physicalBookId,
            'user_id' => $userId,
        ]);
    }

    public static function expireOldReservations(): void
    {
        $stmt = db()->query('
            SELECT reservation_id, physical_book_id
            FROM book_reservations
            WHERE status = "pending"
            AND expires_at IS NOT NULL
            AND expires_at < NOW()
        ');
        $expired = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$expired) {
            return;
        }

        $ids = array_column($expired, 'reservation_id');
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $update = db()->prepare("
            UPDATE book_reservations
            SET status = 'expired'
            WHERE reservation_id IN ({$placeholders})
        ");
        $update->execute($ids);

        foreach ($expired as $reservation) {
            self::releasePhysicalBook((int) $reservation['physical_book_id']);
        }
    }

    private static function findForUser(int $reservationId, int $userId): ?array
    {
        $stmt = db()->prepare('
            SELECT *
            FROM book_reservations
            WHERE reservation_id = :id AND user_id = :user
            LIMIT 1
        ');
        $stmt->execute(['id' => $reservationId, 'user' => $userId]);
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
        return $reservation ?: null;
    }

    private static function releasePhysicalBook(int $physicalBookId): void
    {
        PhysicalBook::update($physicalBookId, ['is_available' => true]);
    }
}


