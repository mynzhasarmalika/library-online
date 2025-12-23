<?php
$favoriteBooks = $favorites ?? [];
$readingBooks = $currentlyReading ?? [];
$completedBooks = $completed ?? [];
$reservations = $reservations ?? [];
$reservationStatuses = [
    'pending' => trans('reservation_status_pending'),
    'ready' => trans('reservation_status_ready'),
    'collected' => trans('reservation_status_collected'),
    'cancelled' => trans('reservation_status_cancelled'),
    'expired' => trans('reservation_status_expired'),
];

function render_book_card(array $book): void
{
    $cover = media_url($book['cover_image'] ?? null);
    ?>
    <a href="/books/<?= (int) $book['book_id'] ?>" class="book-card bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition border border-gray-100 dark:border-gray-700">
        <div class="aspect-[3/4] bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 overflow-hidden relative group">
            <?php if ($cover): ?>
                <img src="<?= htmlspecialchars($cover) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
            <?php else: ?>
                <div class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-500">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            <?php endif; ?>
        </div>
        <div class="p-4">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-1 line-clamp-2 min-h-[3rem]" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"><?= htmlspecialchars($book['title']) ?></h3>
            <p class="text-sm text-gray-600 dark:text-gray-400"><?= htmlspecialchars($book['author']) ?></p>
            <?php if (!empty($book['progress_percentage'])): ?>
                <div class="mt-3">
                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                        <span>Status: <?= htmlspecialchars(ucfirst($book['status'])) ?></span>
                        <span class="font-semibold"><?= number_format((float) $book['progress_percentage'], 1) ?>%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-primary-500 to-primary-600 h-2 rounded-full transition-all duration-500" style="width: <?= min(100, (float) $book['progress_percentage']) ?>%"></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </a>
    <?php
}
?>

<div class="space-y-12">
    <section>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100"><?= trans('my_library_heading') ?></h1>
                <p class="text-gray-600 dark:text-gray-400"><?= trans('my_library_subheading') ?></p>
            </div>
            <?php if (!empty($favoriteBooks)): ?>
                <div class="flex gap-2">
                    <a href="/export/favorites?format=csv" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 text-sm font-medium shadow-md hover:shadow-lg transition-all">
                        📥 Export CSV
                    </a>
                    <a href="/export/favorites?format=json" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 text-sm font-medium shadow-md hover:shadow-lg transition-all">
                        📥 Export JSON
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100"><?= trans('reservations') ?></h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm"><?= trans('reservations_subtitle') ?></p>
            </div>
            <?php if ($reservations): ?>
                <span class="text-sm text-gray-500 dark:text-gray-400 font-medium"><?= count($reservations) ?> <?= trans('reservations_count_label') ?></span>
            <?php endif; ?>
        </div>
        <?php if ($reservations): ?>
            <div class="space-y-4">
                <?php foreach ($reservations as $reservation): ?>
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4 shadow-sm">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                <a href="/books/<?= (int) $reservation['book_id'] ?>" class="hover:text-primary-600 dark:hover:text-primary-400 transition">
                                    <?= htmlspecialchars($reservation['title']) ?>
                                </a>
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1"><?= htmlspecialchars($reservation['author']) ?></p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <?= trans('reservation_copy_info', [
                                    'inventory' => $reservation['inventory_number'] ?? 'N/A',
                                    'location' => $reservation['location'] ?? trans('location_not_provided'),
                                ]) ?>
                            </p>
                            <?php if (!empty($reservation['expires_at']) && in_array($reservation['status'], ['pending','ready'], true)): ?>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <?= trans('reservation_expires_at', ['date' => date('M d, Y H:i', strtotime($reservation['expires_at']))]) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                <?php
                                    echo match ($reservation['status']) {
                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200',
                                        'ready' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                        'collected' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                        'expired' => 'bg-gray-200 text-gray-700 dark:bg-gray-900/40 dark:text-gray-300',
                                        default => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                    };
                                ?>
                            ">
                                <?= htmlspecialchars($reservationStatuses[$reservation['status']] ?? ucfirst($reservation['status'])) ?>
                            </span>
                            <?php if (in_array($reservation['status'], ['pending','ready'], true)): ?>
                                <form method="POST" action="/reservations/<?= (int) $reservation['reservation_id'] ?>">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline font-medium">
                                        <?= trans('cancel_reservation') ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-dashed border-gray-200 dark:border-gray-700 p-8 text-center text-gray-500 dark:text-gray-400">
                <?= trans('no_reservations_yet') ?>
            </div>
        <?php endif; ?>
    </section>

    <section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Currently Reading</h2>
            <?php if ($readingBooks): ?>
                <span class="text-sm text-gray-500 dark:text-gray-400 font-medium"><?= count($readingBooks) ?> book(s)</span>
            <?php endif; ?>
        </div>
        <?php if ($readingBooks): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($readingBooks as $book): ?>
                    <?php render_book_card($book); ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-dashed border-gray-200 dark:border-gray-700 p-8 text-center text-gray-500 dark:text-gray-400">
                Nothing here yet. Start reading a book to see it here.
            </div>
        <?php endif; ?>
    </section>

    <section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Favorites</h2>
            <?php if ($favoriteBooks): ?>
                <span class="text-sm text-gray-500 dark:text-gray-400 font-medium"><?= count($favoriteBooks) ?> book(s)</span>
            <?php endif; ?>
        </div>
        <?php if ($favoriteBooks): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($favoriteBooks as $book): ?>
                    <?php render_book_card($book); ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-dashed border-gray-200 dark:border-gray-700 p-8 text-center text-gray-500 dark:text-gray-400">
                Save books to favorites to quickly access them here.
            </div>
        <?php endif; ?>
    </section>

    <section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Completed</h2>
            <?php if ($completedBooks): ?>
                <span class="text-sm text-gray-500 dark:text-gray-400 font-medium"><?= count($completedBooks) ?> book(s)</span>
            <?php endif; ?>
        </div>
        <?php if ($completedBooks): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($completedBooks as $book): ?>
                    <?php render_book_card($book); ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-dashed border-gray-200 dark:border-gray-700 p-8 text-center text-gray-500 dark:text-gray-400">
                Finish a book to celebrate your progress!
            </div>
        <?php endif; ?>
    </section>
</div>

