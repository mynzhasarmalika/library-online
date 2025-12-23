<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class BookLoan
{
    public static function find(int $id): ?array
    {
        $stmt = db()->prepare('
            SELECT bl.*, 
                   pb.inventory_number, pb.location,
                   b.title, b.author,
                   u.email, u.first_name, u.last_name
            FROM book_loans bl
            JOIN physical_books pb ON bl.physical_book_id = pb.physical_book_id
            JOIN books b ON pb.book_id = b.book_id
            JOIN users u ON bl.user_id = u.user_id
            WHERE bl.loan_id = :id
            LIMIT 1
        ');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function create(array $data): int
    {
        $dueDate = $data['due_date'] ?? date('Y-m-d', strtotime('+14 days')); // Default 2 weeks

        $stmt = db()->prepare('
            INSERT INTO book_loans (physical_book_id, user_id, due_date, notes)
            VALUES (:physical_book_id, :user_id, :due_date, :notes)
        ');
        $stmt->execute([
            'physical_book_id' => $data['physical_book_id'],
            'user_id' => $data['user_id'],
            'due_date' => $dueDate,
            'notes' => $data['notes'] ?? null,
        ]);

        // Mark physical book as unavailable
        PhysicalBook::update((int) $data['physical_book_id'], ['is_available' => false]);
        BookReservation::markCollectedForBook((int) $data['physical_book_id'], (int) $data['user_id']);

        return (int) db()->lastInsertId();
    }

    public static function return(int $loanId, ?int $returnedBy = null): void
    {
        $loan = self::find($loanId);
        if (!$loan) {
            return;
        }

        $stmt = db()->prepare('
            UPDATE book_loans 
            SET returned_at = NOW(), is_overdue = FALSE
            WHERE loan_id = :id
        ');
        $stmt->execute(['id' => $loanId]);

        // Mark physical book as available
        PhysicalBook::update((int) $loan['physical_book_id'], ['is_available' => true]);
    }

    public static function forUser(int $userId, bool $activeOnly = false): array
    {
        $where = $activeOnly ? 'AND bl.returned_at IS NULL' : '';
        
        $stmt = db()->prepare("
            SELECT bl.*, 
                   pb.inventory_number, pb.location,
                   b.title, b.author, b.book_id
            FROM book_loans bl
            JOIN physical_books pb ON bl.physical_book_id = pb.physical_book_id
            JOIN books b ON pb.book_id = b.book_id
            WHERE bl.user_id = :user_id {$where}
            ORDER BY bl.due_date ASC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getOverdue(): array
    {
        $stmt = db()->prepare('
            SELECT bl.*, 
                   pb.inventory_number, pb.location,
                   b.title, b.author,
                   u.email, u.first_name, u.last_name, u.user_id
            FROM book_loans bl
            JOIN physical_books pb ON bl.physical_book_id = pb.physical_book_id
            JOIN books b ON pb.book_id = b.book_id
            JOIN users u ON bl.user_id = u.user_id
            WHERE bl.returned_at IS NULL 
            AND bl.due_date < CURDATE()
            ORDER BY bl.due_date ASC
        ');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function updateOverdueStatus(bool $autoBan = false): void
    {
        // Get all overdue loans
        $overdueLoans = self::getOverdue();
        
        if (empty($overdueLoans)) {
            return;
        }

        // Update status
        $stmt = db()->prepare('
            UPDATE book_loans 
            SET is_overdue = TRUE 
            WHERE returned_at IS NULL 
            AND due_date < CURDATE()
            AND is_overdue = FALSE
        ');
        $stmt->execute();

        // Send notifications
        $userLoans = [];
        foreach ($overdueLoans as $loan) {
            $userId = (int) $loan['user_id'];
            if (!isset($userLoans[$userId])) {
                $userLoans[$userId] = [];
            }
            $userLoans[$userId][] = $loan;
        }

        foreach ($userLoans as $userId => $loans) {
            foreach ($loans as $loan) {
                \App\Models\Notification::notifyOverdue($userId, [
                    'title' => $loan['title'],
                    'due_date' => $loan['due_date'],
                ]);
            }

            // Auto-ban if enabled and user has overdue books
            if ($autoBan && !empty($loans)) {
                $user = \App\Models\User::find($userId);
                if ($user && empty($user['is_banned'])) {
                    $bannedBy = 0; // System
                    if (function_exists('auth')) {
                        $currentUser = auth();
                        if ($currentUser) {
                            $bannedBy = (int) ($currentUser['user_id'] ?? 0);
                        }
                    }
                    \App\Models\UserBan::ban($userId, $bannedBy, 'Automatic ban due to overdue books');
                }
            }
        }

        // Notify librarians
        \App\Models\Notification::notifyLibrarianOverdue($overdueLoans);
    }

    public static function paginate(int $perPage = 15, int $page = 1, ?string $status = null): array
    {
        $conditions = [];
        $params = [];

        if ($status === 'active') {
            $conditions[] = 'bl.returned_at IS NULL';
        } elseif ($status === 'returned') {
            $conditions[] = 'bl.returned_at IS NOT NULL';
        } elseif ($status === 'overdue') {
            $conditions[] = 'bl.returned_at IS NULL AND bl.due_date < CURDATE()';
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $countSql = "
            SELECT COUNT(*)
            FROM book_loans bl
            {$where}
        ";
        $stmt = db()->prepare($countSql);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $offset = max(0, ($page - 1) * $perPage);

        $sql = "
            SELECT bl.*, 
                   pb.inventory_number, pb.location,
                   b.title, b.author, b.book_id,
                   u.email, u.first_name, u.last_name, u.user_id
            FROM book_loans bl
            JOIN physical_books pb ON bl.physical_book_id = pb.physical_book_id
            JOIN books b ON pb.book_id = b.book_id
            JOIN users u ON bl.user_id = u.user_id
            {$where}
            ORDER BY bl.due_date ASC
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

    public static function countActiveForUser(int $userId): int
    {
        $stmt = db()->prepare('
            SELECT COUNT(*) 
            FROM book_loans 
            WHERE user_id = :user_id AND returned_at IS NULL
        ');
        $stmt->execute(['user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    public static function hasOverdue(int $userId): bool
    {
        $stmt = db()->prepare('
            SELECT COUNT(*) 
            FROM book_loans 
            WHERE user_id = :user_id 
            AND returned_at IS NULL 
            AND due_date < CURDATE()
        ');
        $stmt->execute(['user_id' => $userId]);
        return (int) $stmt->fetchColumn() > 0;
    }
}

