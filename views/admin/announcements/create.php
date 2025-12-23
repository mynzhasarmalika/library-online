<?php
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>
<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mb-8"><?= trans('create_announcement') ?></h1>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-8 border border-gray-100 dark:border-gray-700">
        <form method="POST" action="/admin/announcements">
            <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">

            <div class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('notification_title') ?> *</label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        value="<?= htmlspecialchars(old('title', '')) ?>"
                        required
                        maxlength="255"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    >
                    <?php if (isset($errors['title'])): ?>
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['title']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('notification_message') ?> *</label>
                    <textarea 
                        id="message" 
                        name="message" 
                        rows="6"
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    ><?= htmlspecialchars(old('message', '')) ?></textarea>
                    <?php if (isset($errors['message'])): ?>
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['message']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="link" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Link (Optional)</label>
                    <input 
                        type="text" 
                        id="link" 
                        name="link" 
                        value="<?= htmlspecialchars(old('link', '')) ?>"
                        placeholder="/books/123 or /"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    >
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Optional link for users to click (e.g., /books/123)</p>
                </div>

                <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg p-4">
                    <p class="text-sm text-emerald-800 dark:text-emerald-300">
                        <strong>ℹ️ Note:</strong> This announcement will be sent to all regular users (not admins or librarians).
                    </p>
                </div>
            </div>

            <div class="mt-8 flex gap-4">
                <button 
                    type="submit" 
                    class="bg-emerald-600 text-white px-6 py-2 rounded-lg hover:bg-emerald-700 font-medium shadow-md hover:shadow-lg transition-all"
                >
                    <?= trans('send_announcement') ?>
                </button>
                <a href="/admin/dashboard" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 font-medium">
                    <?= trans('cancel') ?>
                </a>
            </div>
        </form>
    </div>
</div>

