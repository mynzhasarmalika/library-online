<?php
$logs = $logs ?? [];
$pagination = $pagination ?? ['total' => 0, 'per_page' => 50, 'current_page' => 1, 'last_page' => 1];
$action = $action ?? '';
$userId = $userId ?? null;
?>
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100"><?= trans('activity_logs_title') ?></h1>
    <p class="text-gray-600 dark:text-gray-400 mt-2"><?= trans('activity_logs_description') ?></p>
</div>

<!-- Filters -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6 border border-gray-100 dark:border-gray-700">
    <form method="GET" action="/admin/logs" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('action_label') ?></label>
            <input
                type="text"
                name="action"
                value="<?= htmlspecialchars($action) ?>"
                placeholder="<?= trans('action_placeholder') ?>"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-200"
            >
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('user_id_label') ?></label>
            <input
                type="number"
                name="user_id"
                value="<?= $userId ? htmlspecialchars((string) $userId) : '' ?>"
                placeholder="<?= trans('user_id_placeholder') ?>"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-200"
            >
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                <?= trans('filter') ?>
            </button>
            <a href="/admin/logs" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                <?= trans('clear') ?>
            </a>
        </div>
    </form>
</div>

<!-- Logs Table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?= trans('time') ?></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?= trans('user') ?></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?= trans('action') ?></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?= trans('entity') ?></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?= trans('description') ?></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?= trans('ip_address') ?></th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <?= trans('no_logs_found') ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <?= date('Y-m-d H:i:s', strtotime($log['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php if ($log['user_id']): ?>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">
                                            <?= htmlspecialchars(($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? '')) ?>
                                        </div>
                                        <div class="text-gray-500 dark:text-gray-400 text-xs">
                                            <?= htmlspecialchars($log['user_email'] ?? '') ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-gray-400 italic"><?= trans('system') ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200">
                                    <?= htmlspecialchars($log['action']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <?php if ($log['entity_type'] && $log['entity_id']): ?>
                                    <?= htmlspecialchars($log['entity_type']) ?> #<?= $log['entity_id'] ?>
                                <?php else: ?>
                                    <span class="text-gray-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                <?= htmlspecialchars($log['description'] ?? '') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <?= htmlspecialchars($log['ip_address'] ?? '') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if ($pagination['last_page'] > 1): ?>
    <div class="mt-6 flex items-center justify-between">
        <div class="text-sm text-gray-700 dark:text-gray-300">
            <?= trans('showing') ?> <?= (($pagination['current_page'] - 1) * $pagination['per_page']) + 1 ?> <?= trans('to') ?> 
            <?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total']) ?> <?= trans('of') ?> 
            <?= $pagination['total'] ?> <?= trans('results') ?>
        </div>
        <div class="flex gap-2">
            <?php if ($pagination['current_page'] > 1): ?>
                <a href="?page=<?= $pagination['current_page'] - 1 ?><?= $action ? '&action=' . urlencode($action) : '' ?><?= $userId ? '&user_id=' . $userId : '' ?>" 
                   class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <?= trans('previous') ?>
                </a>
            <?php endif; ?>
            <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                <a href="?page=<?= $pagination['current_page'] + 1 ?><?= $action ? '&action=' . urlencode($action) : '' ?><?= $userId ? '&user_id=' . $userId : '' ?>" 
                   class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <?= trans('next') ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

