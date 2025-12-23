<?php
$loans = $loans ?? [];
$hasOverdue = $hasOverdue ?? false;
?>
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">My Book Loans</h1>
    <p class="text-gray-600 dark:text-gray-400 mt-2">View your borrowed physical books</p>
</div>

<?php if ($hasOverdue): ?>
    <div class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-6">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <span class="font-semibold">You have overdue books! Please return them as soon as possible.</span>
        </div>
    </div>
<?php endif; ?>

<?php if (count($loans) > 0): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Book</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Inventory #</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Loaned Date</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Due Date</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loans as $loan): ?>
                        <?php
                            $dueDate = strtotime($loan['due_date']);
                            $isOverdue = $dueDate < time() && empty($loan['returned_at']);
                            $daysUntilDue = $isOverdue ? 0 : (int) floor(($dueDate - time()) / 86400);
                        ?>
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900 <?= $isOverdue ? 'bg-red-50 dark:bg-red-900/20' : '' ?>">
                            <td class="py-3 px-4 text-sm">
                                <div class="font-semibold text-gray-800 dark:text-gray-100"><?= htmlspecialchars($loan['title']) ?></div>
                                <div class="text-gray-600 dark:text-gray-400"><?= htmlspecialchars($loan['author']) ?></div>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400 font-mono"><?= htmlspecialchars($loan['inventory_number']) ?></td>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400"><?= date('M j, Y', strtotime($loan['loaned_at'])) ?></td>
                            <td class="py-3 px-4 text-sm">
                                <div class="font-medium <?= $isOverdue ? 'text-red-600 dark:text-red-400' : ($daysUntilDue <= 3 ? 'text-orange-600 dark:text-orange-400' : 'text-gray-700 dark:text-gray-300') ?>">
                                    <?= date('M j, Y', $dueDate) ?>
                                </div>
                                <?php if ($isOverdue): ?>
                                    <div class="text-xs text-red-600 dark:text-red-400">Overdue</div>
                                <?php elseif ($daysUntilDue <= 3): ?>
                                    <div class="text-xs text-orange-600 dark:text-orange-400">Due in <?= $daysUntilDue ?> day(s)</div>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-sm">
                                <?php if ($isOverdue): ?>
                                    <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full text-xs font-medium">Overdue</span>
                                <?php elseif ($daysUntilDue <= 3): ?>
                                    <span class="px-2 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 rounded-full text-xs font-medium">Due Soon</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs font-medium">Active</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-12 text-center border border-gray-100 dark:border-gray-700">
        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No active loans</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You don't have any borrowed books at the moment.</p>
    </div>
<?php endif; ?>

