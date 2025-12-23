<?php
$stats = $stats ?? [];
$achievements = $achievements ?? [];
$allAchievements = $allAchievements ?? [];
?>
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100"><?= trans('my_reading_statistics') ?></h1>
    <p class="text-gray-600 dark:text-gray-400 mt-2"><?= trans('track_reading_progress') ?></p>
</div>

<!-- Main Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1"><?= trans('completed_books') ?></p>
                <p class="text-3xl font-bold text-primary-600 dark:text-primary-400"><?= number_format($stats['completed_books'] ?? 0) ?></p>
            </div>
            <div class="text-4xl">📚</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1"><?= trans('pages_read') ?></p>
                <p class="text-3xl font-bold text-primary-600 dark:text-primary-400"><?= number_format($stats['total_pages'] ?? 0) ?></p>
            </div>
            <div class="text-4xl">📄</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1"><?= trans('currently_reading') ?></p>
                <p class="text-3xl font-bold text-primary-600 dark:text-primary-400"><?= number_format($stats['currently_reading'] ?? 0) ?></p>
            </div>
            <div class="text-4xl">📖</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1"><?= trans('average_rating') ?></p>
                <p class="text-3xl font-bold text-primary-600 dark:text-primary-400"><?= number_format($stats['avg_rating'] ?? 0, 1) ?></p>
            </div>
            <div class="text-4xl">⭐</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Top Genres -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4"><?= trans('top_genres') ?></h2>
        <?php if (!empty($stats['top_genres'])): ?>
            <div class="space-y-3">
                <?php foreach ($stats['top_genres'] as $genre): ?>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700 dark:text-gray-300"><?= htmlspecialchars($genre['genre']) ?></span>
                        <span class="font-semibold text-primary-600 dark:text-primary-400"><?= (int) $genre['count'] ?> <?= trans('books') ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500 dark:text-gray-400"><?= trans('no_genre_data') ?></p>
        <?php endif; ?>
    </div>

    <!-- Achievements -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4"><?= trans('achievements') ?></h2>
        <div class="grid grid-cols-2 gap-3">
            <?php foreach ($allAchievements as $achievement): ?>
                <div class="p-3 rounded-lg border <?= $achievement['unlocked'] ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700' ?>">
                    <div class="text-2xl mb-1"><?= $achievement['unlocked'] ? $achievement['icon'] : '🔒' ?></div>
                    <div class="text-sm font-semibold text-gray-800 dark:text-gray-100"><?= htmlspecialchars($achievement['title']) ?></div>
                    <div class="text-xs text-gray-600 dark:text-gray-400"><?= htmlspecialchars($achievement['requirement']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Monthly Activity Chart -->
<?php if (!empty($stats['monthly_activity'])): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4"><?= trans('reading_activity') ?> (<?= trans('last_12_months') ?>)</h2>
        <div class="flex items-end gap-2 h-48">
            <?php 
            $maxBooks = max(array_column($stats['monthly_activity'], 'books_completed')) ?: 1;
            foreach ($stats['monthly_activity'] as $month): 
                $height = ($month['books_completed'] / $maxBooks) * 100;
            ?>
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-primary-200 dark:bg-primary-800 rounded-t" style="height: <?= $height ?>%"></div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-2 transform -rotate-45 origin-top-left whitespace-nowrap">
                        <?= htmlspecialchars($month['month']) ?>
                    </div>
                    <div class="text-sm font-semibold text-gray-800 dark:text-gray-100 mt-1"><?= (int) $month['books_completed'] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

