<?php
$reservations = $reservations ?? [];
$status = $status ?? null;
?>
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100"><?= trans('reservation_management') ?></h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2"><?= trans('view_reservations') ?></p>
    </div>
    <a href="/admin/library/loans/create" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 shadow-md hover:shadow-lg transition-all">
        + <?= trans('loan_book') ?>
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700">
    <div class="flex gap-2 mb-6">
        <a href="/admin/library/reservations" class="px-4 py-2 rounded-lg <?= !$status ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' ?>">
            <?= trans('all_reservations') ?>
        </a>
        <a href="/admin/library/reservations?status=pending" class="px-4 py-2 rounded-lg <?= $status === 'pending' ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' ?>">
            <?= trans('reservation_status_pending') ?>
        </a>
        <a href="/admin/library/reservations?status=ready" class="px-4 py-2 rounded-lg <?= $status === 'ready' ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' ?>">
            <?= trans('reservation_status_ready') ?>
        </a>
    </div>

    <?php if (count($reservations) > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b dark:border-gray-700">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('book') ?></th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('user') ?></th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('reserved_at') ?></th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('expires_at') ?></th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('status') ?></th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('inventory_number') ?></th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                        <?php
                            $reservationStatus = $reservation['status'] ?? 'pending';
                            $isPending = $reservationStatus === 'pending';
                            $isReady = $reservationStatus === 'ready';
                        ?>
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900">
                            <td class="py-3 px-4 text-sm">
                                <div class="font-semibold text-gray-800 dark:text-gray-100"><?= htmlspecialchars($reservation['title']) ?></div>
                                <div class="text-gray-600 dark:text-gray-400 text-xs"><?= htmlspecialchars($reservation['author']) ?></div>
                            </td>
                            <td class="py-3 px-4 text-sm">
                                <div class="text-gray-800 dark:text-gray-100"><?= htmlspecialchars(trim(($reservation['first_name'] ?? '') . ' ' . ($reservation['last_name'] ?? ''))) ?></div>
                                <div class="text-gray-600 dark:text-gray-400 text-xs"><?= htmlspecialchars($reservation['email']) ?></div>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">
                                <?= date('M j, Y H:i', strtotime($reservation['reserved_at'])) ?>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">
                                <?= $reservation['expires_at'] ? date('M j, Y H:i', strtotime($reservation['expires_at'])) : '—' ?>
                            </td>
                            <td class="py-3 px-4 text-sm">
                                <?php if ($reservationStatus === 'ready'): ?>
                                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs font-medium">
                                        <?= trans('reservation_status_ready') ?>
                                    </span>
                                <?php elseif ($reservationStatus === 'pending'): ?>
                                    <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-full text-xs font-medium">
                                        <?= trans('reservation_status_pending') ?>
                                    </span>
                                <?php elseif ($reservationStatus === 'collected'): ?>
                                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full text-xs font-medium">
                                        <?= trans('reservation_status_collected') ?>
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs font-medium">
                                        <?= htmlspecialchars(ucfirst($reservationStatus)) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">
                                #<?= htmlspecialchars($reservation['inventory_number']) ?>
                            </td>
                            <td class="py-3 px-4 text-sm text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <?php if ($isPending): ?>
                                        <form method="POST" action="/admin/library/reservations/<?= (int) $reservation['reservation_id'] ?>/mark-ready" class="inline">
                                            <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                                            <button type="submit" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-700 dark:hover:text-yellow-300 text-sm">
                                                <?= trans('mark_as_ready') ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($isReady || $isPending): ?>
                                        <a href="/admin/library/loans/create?reservation_id=<?= (int) $reservation['reservation_id'] ?>&user_id=<?= (int) $reservation['user_id'] ?>&physical_book_id=<?= (int) $reservation['physical_book_id'] ?>" 
                                           class="bg-green-600 text-white px-3 py-1 rounded-lg hover:bg-green-700 text-sm font-medium shadow-sm hover:shadow transition-all">
                                            <?= trans('issue_book') ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-12">
            <p class="text-gray-600 dark:text-gray-400"><?= trans('no_active_reservations') ?></p>
        </div>
    <?php endif; ?>
</div>

