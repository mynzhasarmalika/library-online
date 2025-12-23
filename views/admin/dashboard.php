<?php
$stats = $stats ?? [];
$isAdmin = $isAdmin ?? false;
$isLibrarian = $isLibrarian ?? false;
$title = $isAdmin ? trans('admin_dashboard') : trans('librarian_dashboard');
$roleColor = $isAdmin ? 'red' : 'emerald';
$roleBgClass = $isAdmin ? 'bg-red-800' : 'bg-emerald-500';
$roleTextClass = $isAdmin ? 'text-red-800' : 'text-emerald-600';
$roleBorderClass = $isAdmin ? 'border-red-800' : 'border-emerald-500';
?>
<div class="mb-8">
    <h1 class="text-3xl font-bold <?= $isAdmin ? 'text-red-800 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' ?>"><?= htmlspecialchars($title) ?></h1>
    <p class="text-gray-600 dark:text-gray-400 mt-2">
        <?php if ($isAdmin): ?>
            <?= trans('full_system_access') ?>
        <?php else: ?>
            <?= trans('manage_books') ?>
        <?php endif; ?>
    </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border <?= $isAdmin ? 'border-red-200 dark:border-red-800' : 'border-emerald-200 dark:border-emerald-800' ?>">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2"><?= trans('total_books') ?></h3>
        <p class="text-3xl font-bold <?= $isAdmin ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' ?>"><?= number_format($stats['books'] ?? 0) ?></p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border <?= $isAdmin ? 'border-red-200 dark:border-red-800' : 'border-emerald-200 dark:border-emerald-800' ?>">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2"><?= trans('total_users') ?></h3>
        <p class="text-3xl font-bold <?= $isAdmin ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' ?>"><?= number_format($stats['users'] ?? 0) ?></p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border <?= $isAdmin ? 'border-red-200 dark:border-red-800' : 'border-emerald-200 dark:border-emerald-800' ?>">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2"><?= trans('physical_books') ?></h3>
        <p class="text-3xl font-bold <?= $isAdmin ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' ?>"><?= number_format($stats['physicalBooks'] ?? 0) ?></p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border <?= $isAdmin ? 'border-red-200 dark:border-red-800' : 'border-emerald-200 dark:border-emerald-800' ?>">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2"><?= trans('active_loans') ?></h3>
        <p class="text-3xl font-bold <?= $isAdmin ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' ?>"><?= number_format($stats['activeLoans'] ?? 0) ?></p>
    </div>
    <?php if (($stats['overdueLoans'] ?? 0) > 0): ?>
        <div class="bg-red-50 dark:bg-red-900/20 rounded-lg shadow-sm p-6 border border-red-200 dark:border-red-800">
            <h3 class="text-lg font-semibold text-red-700 dark:text-red-300 mb-2"><?= trans('overdue_loans') ?></h3>
            <p class="text-3xl font-bold text-red-600 dark:text-red-400"><?= number_format($stats['overdueLoans'] ?? 0) ?></p>
        </div>
    <?php endif; ?>
    <?php if (($stats['bannedUsers'] ?? 0) > 0): ?>
        <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg shadow-sm p-6 border border-orange-200 dark:border-orange-800">
            <h3 class="text-lg font-semibold text-orange-700 dark:text-orange-300 mb-2"><?= trans('banned_users') ?></h3>
            <p class="text-3xl font-bold text-orange-600 dark:text-orange-400"><?= number_format($stats['bannedUsers'] ?? 0) ?></p>
        </div>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if ($isLibrarian && !$isAdmin): ?>
        <a href="/admin/books" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 hover:shadow-md transition border-2 border-emerald-200 dark:border-emerald-800 hover:border-emerald-300">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">📚 <?= trans('manage_books') ?></h2>
            <p class="text-gray-600 dark:text-gray-400"><?= trans('create_edit_delete_books') ?></p>
        </a>
    <?php endif; ?>
    
    <?php if ($isLibrarian && !$isAdmin): ?>
        <a href="/admin/library/physical-books" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 hover:shadow-md transition border-2 border-emerald-200 dark:border-emerald-800 hover:border-emerald-300">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">📖 <?= trans('manage_physical_books') ?></h2>
            <p class="text-gray-600 dark:text-gray-400"><?= trans('manage_physical_copies') ?></p>
        </a>
        
        <a href="/admin/library/reservations" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 hover:shadow-md transition border-2 border-emerald-200 dark:border-emerald-800 hover:border-emerald-300">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">📌 <?= trans('reservations') ?></h2>
            <p class="text-gray-600 dark:text-gray-400"><?= trans('view_reservations') ?></p>
        </a>
        
        <a href="/admin/library/loans" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 hover:shadow-md transition border-2 border-emerald-200 dark:border-emerald-800 hover:border-emerald-300">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">📋 <?= trans('book_loans') ?></h2>
            <p class="text-gray-600 dark:text-gray-400"><?= trans('track_loans_returns') ?></p>
        </a>
        
        <a href="/admin/library/bans" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 hover:shadow-md transition border-2 border-emerald-200 dark:border-emerald-800 hover:border-emerald-300">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">🚫 <?= trans('user_bans') ?></h2>
            <p class="text-gray-600 dark:text-gray-400"><?= trans('ban_unban_users') ?></p>
        </a>
        
        <a href="/admin/announcements/create" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 hover:shadow-md transition border-2 border-emerald-200 dark:border-emerald-800 hover:border-emerald-300">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">📢 <?= trans('send_announcement') ?></h2>
            <p class="text-gray-600 dark:text-gray-400"><?= trans('send_to_all_users') ?></p>
        </a>
    <?php endif; ?>
    
    <?php if ($isAdmin): ?>
        <a href="/admin/users" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 hover:shadow-md transition border-2 border-red-200 dark:border-red-800 hover:border-red-300">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">👥 <?= trans('manage_users') ?></h2>
            <p class="text-gray-600 dark:text-gray-400"><?= trans('view_manage_accounts') ?></p>
        </a>
    <?php endif; ?>
    
    <?php if ($isAdmin): ?>
        <a href="/admin/comments" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 hover:shadow-md transition border-2 border-red-200 dark:border-red-800 hover:border-red-300">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">💬 <?= trans('moderate_comments') ?></h2>
            <p class="text-gray-600 dark:text-gray-400"><?= trans('review_delete_comments') ?></p>
        </a>
        
        <a href="/admin/logs" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 hover:shadow-md transition border-2 border-red-200 dark:border-red-800 hover:border-red-300">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">📋 <?= trans('activity_logs') ?></h2>
            <p class="text-gray-600 dark:text-gray-400"><?= trans('view_system_activity') ?></p>
        </a>
    <?php endif; ?>
</div>

<!-- Analytics Section -->
<?php if (!empty($popularBooks) || !empty($activeReaders) || !empty($genreStats)): ?>
    <div class="mt-12">
        <h2 class="text-2xl font-bold <?= $isAdmin ? 'text-red-800 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' ?> mb-6"><?= trans('analytics') ?></h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Popular Books -->
            <?php if (!empty($popularBooks)): ?>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border <?= $isAdmin ? 'border-red-200 dark:border-red-800' : 'border-emerald-200 dark:border-emerald-800' ?>">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4"><?= trans('popular_books') ?></h3>
                    <div class="space-y-3">
                        <?php foreach (array_slice($popularBooks, 0, 5) as $index => $book): ?>
                            <div class="flex items-center justify-between p-2 hover:bg-gray-50 dark:hover:bg-gray-900 rounded">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl font-bold text-primary-600 dark:text-primary-400 w-8"><?= $index + 1 ?></span>
                                    <div>
                                        <div class="font-semibold text-gray-800 dark:text-gray-100"><?= htmlspecialchars($book['title']) ?></div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400"><?= htmlspecialchars($book['author']) ?></div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-semibold <?= $isAdmin ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' ?>">❤️ <?= (int) ($book['favorites_count'] ?? 0) ?></div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">⭐ <?= number_format((float) ($book['avg_rating'] ?? 0), 1) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Active Readers -->
            <?php if (!empty($activeReaders)): ?>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border <?= $isAdmin ? 'border-red-200 dark:border-red-800' : 'border-emerald-200 dark:border-emerald-800' ?>">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4"><?= trans('most_active_readers') ?></h3>
                    <div class="space-y-3">
                        <?php foreach ($activeReaders as $index => $reader): ?>
                            <div class="flex items-center justify-between p-2 hover:bg-gray-50 dark:hover:bg-gray-900 rounded">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl font-bold text-primary-600 dark:text-primary-400 w-8"><?= $index + 1 ?></span>
                                    <div>
                                        <div class="font-semibold text-gray-800 dark:text-gray-100"><?= htmlspecialchars($reader['first_name'] . ' ' . $reader['last_name']) ?></div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400"><?= htmlspecialchars($reader['email']) ?></div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-semibold <?= $isAdmin ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' ?>"><?= (int) $reader['books_read'] ?> <?= trans('books') ?></div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400"><?= number_format((float) $reader['pages_read']) ?> <?= trans('pages') ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Genre Statistics -->
        <?php if (!empty($genreStats)): ?>
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border <?= $isAdmin ? 'border-red-200 dark:border-red-800' : 'border-emerald-200 dark:border-emerald-800' ?>">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4"><?= trans('genre_statistics') ?></h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach (array_slice($genreStats, 0, 9) as $genre): ?>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="font-semibold text-gray-800 dark:text-gray-100 mb-2"><?= htmlspecialchars($genre['genre']) ?></div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                <div><?= trans('books') ?>: <span class="font-semibold"><?= (int) $genre['total_books'] ?></span></div>
                                <div><?= trans('favorites') ?>: <span class="font-semibold"><?= (int) $genre['total_favorites'] ?></span></div>
                                <div><?= trans('avg_rating') ?>: <span class="font-semibold"><?= number_format((float) $genre['avg_rating'], 1) ?> ⭐</span></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

