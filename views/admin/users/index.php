<?php
$users = $users ?? [];
$pagination = $pagination ?? ['current_page' => 1, 'last_page' => 1];
$currentUserId = (int) (auth()['user_id'] ?? 0);
$isAdmin = is_admin();
$isLibrarian = is_librarian();
?>
<h1 class="text-3xl font-bold <?= $isAdmin ? 'text-red-800 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' ?> mb-8"><?= trans('manage_users') ?></h1>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?= trans('user') ?></th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?= trans('email') ?></th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?= trans('role') ?></th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?= trans('joined') ?></th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?= trans('actions') ?></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if ($users): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <?php $avatarUrl = media_url($user['avatar'] ?? null); ?>
                                <?php if ($avatarUrl): ?>
                                    <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" class="w-10 h-10 rounded-full">
                                <?php else: ?>
                                    <?php 
                                    $userRole = strtolower($user['role'] ?? 'user');
                                    $avatarBg = get_role_color_classes($userRole, 'bg');
                                    ?>
                                    <div class="w-10 h-10 rounded-full <?= $avatarBg ?> flex items-center justify-center text-white font-semibold">
                                        <?= htmlspecialchars(strtoupper(substr($user['email'], 0, 1))) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: $user['email']) ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500"><?= htmlspecialchars($user['email']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if (can_manage_roles()): ?>
                                <div class="flex items-center gap-2">
                                    <?php 
                                    $userRole = strtolower($user['role'] ?? 'user');
                                    echo role_badge($userRole, true);
                                    ?>
                                    <form method="POST" action="/admin/users/<?= (int) $user['user_id'] ?>/role" class="inline" id="role-form-<?= (int) $user['user_id'] ?>">
                                        <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                                        <input type="hidden" name="_method" value="PUT">
                                        <select 
                                            name="role" 
                                            onchange="if(confirm('<?= trans('are_you_sure_change_role') ?> ' + this.value + '?')) { this.form.submit(); } else { this.value = '<?= htmlspecialchars($user['role'] ?? 'user') ?>'; }"
                                            class="text-sm rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 px-2 py-1 <?= $isAdmin ? 'border-red-300' : 'border-emerald-300' ?>"
                                        >
                                            <option value="user" <?= ($user['role'] ?? 'user') === 'user' ? 'selected' : '' ?>><?= trans('user') ?></option>
                                            <option value="librarian" <?= ($user['role'] ?? '') === 'librarian' ? 'selected' : '' ?>><?= trans('librarian') ?></option>
                                            <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>><?= trans('admin') ?></option>
                                        </select>
                                    </form>
                                </div>
                            <?php else: ?>
                                <?php 
                                $userRole = strtolower($user['role'] ?? 'user');
                                echo role_badge($userRole, true);
                                ?>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= htmlspecialchars(date('M d, Y', strtotime($user['created_at'] ?? 'now'))) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <?php 
                            $canDelete = (int) $user['user_id'] !== $currentUserId;
                            if (!is_admin()) {
                                // Librarian cannot delete admin or librarian accounts
                                $canDelete = $canDelete && !in_array($user['role'] ?? 'user', ['admin', 'librarian'], true);
                            }
                            ?>
                            <?php if ($canDelete): ?>
                                <form action="/admin/users/<?= (int) $user['user_id'] ?>/delete" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="text-red-600 hover:text-red-900"><?= trans('delete') ?></button>
                                </form>
                            <?php else: ?>
                                <span class="text-gray-400">
                                    <?php if ((int) $user['user_id'] === $currentUserId): ?>
                                        <?= trans('current_user') ?>
                                    <?php else: ?>
                                        <?= trans('protected') ?>
                                    <?php endif; ?>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500"><?= trans('no_users_found') ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($pagination['last_page'] > 1): ?>
    <div class="mt-6 flex justify-center gap-2">
        <?php if ($pagination['current_page'] > 1): ?>
            <a href="?page=<?= $pagination['current_page'] - 1 ?>" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"><?= trans('previous') ?></a>
        <?php endif; ?>
        <span class="px-4 py-2"><?= trans('page') ?> <?= $pagination['current_page'] ?> <?= trans('of') ?> <?= $pagination['last_page'] ?></span>
        <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
            <a href="?page=<?= $pagination['current_page'] + 1 ?>" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"><?= trans('next') ?></a>
        <?php endif; ?>
    </div>
<?php endif; ?>

