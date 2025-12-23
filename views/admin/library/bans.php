<?php
$bans = $bans ?? [];
?>
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100"><?= trans('user_bans') ?></h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2"><?= trans('ban_unban_users') ?></p>
    </div>
    <a href="/admin/library/bans/create" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 shadow-md hover:shadow-lg transition-all">
        + <?= trans('ban_user') ?>
    </a>
</div>

<?php if (count($bans) > 0): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('user') ?></th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('reason') ?></th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('banned_by') ?></th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('banned_at') ?></th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bans as $ban): ?>
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900">
                            <td class="py-3 px-4 text-sm">
                                <div class="font-semibold text-gray-800 dark:text-gray-100"><?= htmlspecialchars($ban['first_name'] . ' ' . $ban['last_name']) ?></div>
                                <div class="text-gray-600 dark:text-gray-400 text-xs"><?= htmlspecialchars($ban['email']) ?></div>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-700 dark:text-gray-300"><?= htmlspecialchars($ban['reason']) ?></td>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400"><?= htmlspecialchars($ban['banned_by_first_name'] ?? $ban['banned_by_email']) ?></td>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400"><?= date('M j, Y', strtotime($ban['banned_at'])) ?></td>
                            <td class="py-3 px-4 text-sm text-right">
                                <form method="POST" action="/admin/library/bans/<?= (int) $ban['user_id'] ?>/unban" class="inline" onsubmit="return confirm('<?= htmlspecialchars(trans('confirm_unban')) ?>')">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300"><?= trans('unban') ?></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-12 text-center border border-gray-100 dark:border-gray-700">
        <p class="text-gray-600 dark:text-gray-400"><?= trans('no_active_bans') ?></p>
    </div>
<?php endif; ?>

