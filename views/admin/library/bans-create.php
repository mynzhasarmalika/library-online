<?php
$user = $user ?? null;
$users = $users ?? [];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Ban User</h1>
    <p class="text-gray-600 dark:text-gray-400 mt-2">Restrict user access to the system</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700">
    <form method="POST" action="/admin/library/bans" class="space-y-6">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">

        <div>
            <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">User *</label>
            <select name="user_id" id="user_id" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <option value="">Select a user...</option>
                <?php foreach ($users as $u): ?>
                    <?php if ($u['role'] === 'user' && empty($u['is_banned'])): ?>
                        <option value="<?= (int) $u['user_id'] ?>" <?= $user && (int) $user['user_id'] === (int) $u['user_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?> (<?= htmlspecialchars($u['email']) ?>)
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['user_id'])): ?>
                <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['user_id']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason for Ban *</label>
            <textarea name="reason" id="reason" rows="4" required placeholder="Explain why this user is being banned..." class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars(old('reason', '')) ?></textarea>
            <?php if (isset($errors['reason'])): ?>
                <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['reason']) ?></p>
            <?php endif; ?>
        </div>

        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <p class="text-sm text-red-700 dark:text-red-300">
                <strong>Warning:</strong> Banned users will not be able to log in or access the system until they are unbanned.
            </p>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 shadow-md hover:shadow-lg transition-all">Ban User</button>
            <a href="/admin/library/bans" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">Cancel</a>
        </div>
    </form>
</div>

