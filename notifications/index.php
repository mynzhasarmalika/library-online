<?php
$notifications = $notifications ?? [];
$unreadCount = $unreadCount ?? 0;
?>
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Notifications</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                <?php if ($unreadCount > 0): ?>
                    You have <?= $unreadCount ?> unread notification(s)
                <?php else: ?>
                    All caught up!
                <?php endif; ?>
            </p>
        </div>
        <?php if ($unreadCount > 0): ?>
            <form 
                method="POST" 
                action="/notifications/mark-all-read" 
                class="inline" 
                id="markAllNotificationsForm"
                data-success-message="Все уведомления отмечены как прочитанные"
                data-error-message="Не удалось отметить уведомления как прочитанные"
            >
                <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                    Mark All as Read
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php if (empty($notifications)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center border border-gray-100 dark:border-gray-700">
        <div class="text-6xl mb-4">🔔</div>
        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">No notifications</h3>
        <p class="text-gray-600 dark:text-gray-400">You're all caught up!</p>
    </div>
<?php else: ?>
    <div class="space-y-4">
        <?php foreach ($notifications as $notification): ?>
            <div 
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700 <?= empty($notification['is_read']) ? 'border-l-4 border-l-primary-600' : '' ?>" 
                id="notification-<?= (int) $notification['id'] ?>"
                <?= empty($notification['is_read']) ? 'data-notification-unread="1"' : '' ?>
            >
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                <?= htmlspecialchars($notification['title']) ?>
                            </h3>
                            <?php if (empty($notification['is_read'])): ?>
                                <span class="px-2 py-1 text-xs font-semibold bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200 rounded-full notification-badge-new">
                                    New
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-3">
                            <?= htmlspecialchars($notification['message']) ?>
                        </p>
                        <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                            <span><?= date('M d, Y H:i', strtotime($notification['created_at'])) ?></span>
                            <?php if ($notification['link']): ?>
                                <a href="<?= htmlspecialchars($notification['link']) ?>" class="text-primary-600 dark:text-primary-400 hover:underline">
                                    View →
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (empty($notification['is_read'])): ?>
                        <form 
                            method="POST" 
                            action="/notifications/<?= (int) $notification['id'] ?>/mark-read" 
                            class="ml-4 notification-mark-read-form"
                            data-notification-id="<?= (int) $notification['id'] ?>"
                            data-success-message="Уведомление отмечено как прочитанное"
                            data-error-message="Не удалось отметить уведомление как прочитанное"
                        >
                            <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                            <button type="submit" class="px-3 py-1 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                                Mark as read
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

