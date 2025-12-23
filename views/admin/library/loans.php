<?php
$loans = $loans ?? [];
$pagination = $pagination ?? ['total' => 0, 'current_page' => 1, 'last_page' => 1];
$status = $status ?? null;
?>
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100"><?= trans('book_loans') ?></h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2"><?= trans('track_loans_returns') ?></p>
    </div>
    <a href="/admin/library/loans/create" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 shadow-md hover:shadow-lg transition-all">
        + <?= trans('loan_book') ?>
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700">
    <div class="flex gap-2 mb-6">
        <a href="/admin/library/loans" class="px-4 py-2 rounded-lg <?= !$status ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' ?>"><?= trans('all') ?></a>
        <a href="/admin/library/loans?status=active" class="px-4 py-2 rounded-lg <?= $status === 'active' ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' ?>"><?= trans('active') ?></a>
        <a href="/admin/library/loans?status=overdue" class="px-4 py-2 rounded-lg <?= $status === 'overdue' ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' ?>"><?= trans('overdue') ?></a>
        <a href="/admin/library/loans?status=returned" class="px-4 py-2 rounded-lg <?= $status === 'returned' ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' ?>"><?= trans('returned') ?></a>
    </div>

    <?php if (count($loans) > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b dark:border-gray-700">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('books') ?></th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('user') ?></th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('loaned_at') ?></th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('due_date') ?></th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('status') ?></th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loans as $loan): ?>
                        <?php
                            $dueDate = strtotime($loan['due_date']);
                            $isOverdue = $dueDate < time() && empty($loan['returned_at']);
                        ?>
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900 <?= $isOverdue ? 'bg-red-50 dark:bg-red-900/20' : '' ?>">
                            <td class="py-3 px-4 text-sm">
                                <div class="font-semibold text-gray-800 dark:text-gray-100"><?= htmlspecialchars($loan['title']) ?></div>
                                <div class="text-gray-600 dark:text-gray-400 text-xs">#<?= htmlspecialchars($loan['inventory_number']) ?></div>
                            </td>
                            <td class="py-3 px-4 text-sm">
                                <div class="text-gray-800 dark:text-gray-100"><?= htmlspecialchars($loan['first_name'] . ' ' . $loan['last_name']) ?></div>
                                <div class="text-gray-600 dark:text-gray-400 text-xs"><?= htmlspecialchars($loan['email']) ?></div>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400"><?= date('M j, Y', strtotime($loan['loaned_at'])) ?></td>
                            <td class="py-3 px-4 text-sm <?= $isOverdue ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-700 dark:text-gray-300' ?>">
                                <?= date('M j, Y', $dueDate) ?>
                            </td>
                            <td class="py-3 px-4 text-sm">
                                <?php if ($loan['returned_at']): ?>
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs font-medium"><?= trans('returned') ?></span>
                                <?php elseif ($isOverdue): ?>
                                    <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full text-xs font-medium"><?= trans('overdue') ?></span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs font-medium"><?= trans('active') ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-sm text-right">
                                <?php if (empty($loan['returned_at'])): ?>
                                    <form method="POST" action="/admin/library/loans/<?= (int) $loan['loan_id'] ?>/return" class="inline" onsubmit="return confirm('<?= htmlspecialchars(trans('confirm_return')) ?>')">
                                        <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                                        <button type="submit" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300"><?= trans('return_book') ?></button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-gray-400 dark:text-gray-600"><?= trans('returned') ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-12">
            <p class="text-gray-600 dark:text-gray-400"><?= trans('no_loans_found') ?></p>
        </div>
    <?php endif; ?>
</div>

