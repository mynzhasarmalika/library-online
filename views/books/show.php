<?php
$user = auth();
$coverUrl = media_url($book['cover_image'] ?? null);
$averageRating = round((float) ($book['avg_rating'] ?? 0));
$ratingCount = (int) ($book['ratings_count'] ?? 0);
$bookId = (int) $book['book_id'];
$bookFileUrl = media_url($book['book_file'] ?? null);
$userReservation = $userReservation ?? null;
$isRegularUser = $user && !is_admin() && !is_librarian();
$reservationStatuses = [
    'pending' => trans('reservation_status_pending'),
    'ready' => trans('reservation_status_ready'),
    'collected' => trans('reservation_status_collected'),
    'cancelled' => trans('reservation_status_cancelled'),
    'expired' => trans('reservation_status_expired'),
];
?>
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 sm:p-8 border border-gray-100 dark:border-gray-700">
    <div class="flex flex-col md:flex-row gap-8">
        <div class="flex-shrink-0">
            <div class="w-64 mx-auto md:mx-0 aspect-[3/4] bg-gray-200 rounded-lg overflow-hidden flex items-center justify-center">
                <?php if ($coverUrl): ?>
                    <img src="<?= htmlspecialchars($coverUrl) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex-1">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-2"><?= htmlspecialchars($book['title']) ?></h1>
            <p class="text-xl text-gray-600 dark:text-gray-400 mb-4"><?= trans('by') ?> <?= htmlspecialchars($book['author']) ?></p>

            <div class="flex flex-wrap items-center gap-3 mb-4">
                <?php if (!empty($book['genre'])): ?>
                    <span class="inline-block bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-300 text-sm px-3 py-1 rounded-full font-medium"><?= htmlspecialchars($book['genre']) ?></span>
                <?php endif; ?>
                <?php if (!empty($book['isbn'])): ?>
                    <span class="text-sm text-gray-600 dark:text-gray-400">ISBN: <span class="font-mono"><?= htmlspecialchars($book['isbn']) ?></span></span>
                <?php endif; ?>
                <?php if (!empty($book['pages'])): ?>
                    <span class="text-sm text-gray-600 dark:text-gray-400"><?= (int) $book['pages'] ?> pages</span>
                <?php endif; ?>
                <?php if (!empty($book['publication_year'])): ?>
                    <span class="text-sm text-gray-600 dark:text-gray-400"><?= (int) $book['publication_year'] ?></span>
                <?php endif; ?>
                <?php if (!empty($book['publisher'])): ?>
                    <span class="text-sm text-gray-600 dark:text-gray-400"><?= htmlspecialchars($book['publisher']) ?></span>
                <?php endif; ?>
            </div>

            <div class="flex items-center mb-4">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <svg class="w-5 h-5 <?= $i <= $averageRating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' ?>" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                <?php endfor; ?>
                <span class="ml-2 text-gray-600 dark:text-gray-400 font-medium">(<?= number_format((float) ($book['avg_rating'] ?? 0), 1) ?>) - <?= $ratingCount ?> <?= trans('ratings') ?></span>
            </div>

            <?php if (!empty($book['description'])): ?>
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2"><?= trans('description') ?></h3>
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed"><?= nl2br(htmlspecialchars($book['description'])) ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($physicalBooks)): ?>
                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <?= trans('physical_library_availability') ?>
                        </h3>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                <span class="font-semibold"><?= $availableCount ?></span> <?= trans('copies_available') ?> <?= count($physicalBooks) ?> <?= trans('total') ?>
                            </p>
                            <?php if ($availableCount > 0): ?>
                                <p class="text-sm text-green-600 dark:text-green-400 font-medium">✓ <?= trans('book_available') ?></p>
                            <?php else: ?>
                                <p class="text-sm text-orange-600 dark:text-orange-400 font-medium">⚠ <?= trans('all_copies_on_loan') ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($isRegularUser): ?>
                        <?php if ($userReservation): ?>
                            <div class="bg-white dark:bg-gray-900 rounded-lg border border-blue-100 dark:border-blue-800 p-4 shadow-sm">
                                <div class="flex items-center justify-between gap-4 flex-wrap">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-300"><?= trans('reservation_status') ?>:</p>
                                        <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                            <?= htmlspecialchars($reservationStatuses[$userReservation['status']] ?? ucfirst($userReservation['status'])) ?>
                                        </p>
                                        <?php if (!empty($userReservation['expires_at']) && in_array($userReservation['status'], ['pending','ready'], true)): ?>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                <?= trans('reservation_expires_at', ['date' => date('M d, Y H:i', strtotime($userReservation['expires_at']))]) ?>
                                            </p>
                                        <?php endif; ?>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                            <?= trans('reservation_copy_info', [
                                                'inventory' => $userReservation['inventory_number'] ?? 'N/A',
                                                'location' => $userReservation['location'] ?? trans('location_not_provided'),
                                            ]) ?>
                                        </p>
                                    </div>
                                    <?php if (in_array($userReservation['status'], ['pending','ready'], true)): ?>
                                        <form method="POST" action="/reservations/<?= (int) $userReservation['reservation_id'] ?>" class="flex-shrink-0">
                                            <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="redirect_to" value="/books/<?= $bookId ?>">
                                            <button type="submit" class="px-4 py-2 rounded-lg bg-white text-red-600 border border-red-200 dark:border-red-500 dark:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 text-sm font-medium shadow">
                                                <?= trans('cancel_reservation') ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php elseif ($availableCount > 0): ?>
                            <form method="POST" action="/books/<?= $bookId ?>/reserve" class="flex flex-wrap items-center gap-3">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-blue-600 text-white font-medium shadow hover:bg-blue-700 transition">
                                    📚 <?= trans('reserve_physical_copy') ?>
                                </button>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    <?= trans('reservation_pickup_hint') ?>
                                </p>
                            </form>
                        <?php else: ?>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <?= trans('reservation_waitlist_info') ?>
                            </p>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                    <th class="py-2 pr-4"><?= trans('inventory_number') ?></th>
                                    <th class="py-2 pr-4"><?= trans('location') ?></th>
                                    <th class="py-2 pr-4"><?= trans('status') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($physicalBooks as $copy): ?>
                                    <?php
                                        $activeLoans = (int) ($copy['active_loans'] ?? 0);
                                        $activeReservations = (int) ($copy['active_reservations'] ?? 0);
                                        $statusLabel = trans('physical_copy_reserved');
                                        $statusClass = 'text-blue-600 dark:text-blue-400';

                                        if ($activeLoans > 0) {
                                            $statusLabel = trans('physical_copy_on_loan');
                                            $statusClass = 'text-orange-600 dark:text-orange-400';
                                        } elseif (!empty($copy['is_available']) && $activeLoans === 0 && $activeReservations === 0) {
                                            $statusLabel = trans('physical_copy_available');
                                            $statusClass = 'text-green-600 dark:text-green-400';
                                        } elseif ($activeReservations === 0 && empty($copy['is_available'])) {
                                            $statusLabel = trans('physical_copy_reserved');
                                            $statusClass = 'text-blue-600 dark:text-blue-400';
                                        }
                                    ?>
                                    <tr class="border-t border-blue-100 dark:border-blue-900/30 text-gray-700 dark:text-gray-200">
                                        <td class="py-2 pr-4 font-semibold"><?= htmlspecialchars($copy['inventory_number'] ?? '—') ?></td>
                                        <td class="py-2 pr-4"><?= htmlspecialchars($copy['location'] ?? trans('location_not_provided')) ?></td>
                                        <td class="py-2 pr-4 font-medium <?= $statusClass ?>"><?= $statusLabel ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <div class="flex flex-wrap gap-3 mb-6">
                <?php if ($user): ?>
                    <?php if ($bookFileUrl): ?>
                        <a href="/books/<?= $bookId ?>/read" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 font-medium shadow-md hover:shadow-lg transition-all"><?= trans('read_book') ?></a>
                        <a href="/books/<?= $bookId ?>/download" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 font-medium shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            <?= trans('download_book') ?>
                        </a>
                    <?php endif; ?>

                    <button 
                        type="button"
                        id="favoriteBtn"
                        data-book-id="<?= $bookId ?>"
                        data-endpoint="/books/<?= $bookId ?>/favorite"
                        data-csrf="<?= htmlspecialchars(csrf_token()) ?>"
                        data-favorited="<?= $isFavorite ? '1' : '0' ?>"
                        data-favorited-text="❤️ <?= htmlspecialchars(trans('favorited')) ?>"
                        data-add-to-fav-text="🤍 <?= htmlspecialchars(trans('add_to_favorites')) ?>"
                        data-added-toast="<?= htmlspecialchars(trans('favorite_added')) ?>"
                        data-removed-toast="<?= htmlspecialchars(trans('favorite_removed')) ?>"
                        data-error-text="<?= htmlspecialchars(trans('favorites_error')) ?>"
                        class="px-6 py-2 rounded-lg border-2 font-medium transition-all <?= $isFavorite ? 'border-red-500 text-red-600 bg-red-50 dark:bg-red-900/20 dark:border-red-400 dark:text-red-400 shadow-md' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-red-500 hover:text-red-600 dark:hover:border-red-400 dark:hover:text-red-400' ?>"
                    >
                        <span id="favoriteText"><?= $isFavorite ? '❤️ ' . trans('favorited') : '🤍 ' . trans('add_to_favorites') ?></span>
                    </button>
                <?php else: ?>
                    <a href="/login" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 font-medium shadow-md hover:shadow-lg transition-all"><?= trans('login_to_read') ?></a>
                <?php endif; ?>
            </div>

            <?php if ($user): ?>
                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3"><?= trans('your_rating') ?></h3>
                    <form method="POST" action="/books/<?= $bookId ?>/rating" class="flex items-center gap-2" data-rating-form>
                        <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <button type="submit" name="score" value="<?= $i ?>" class="text-2xl <?= $userRating && (int) $userRating['score'] >= $i ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' ?> hover:text-yellow-400 dark:hover:text-yellow-500 transition transform hover:scale-110">
                                ★
                            </button>
                        <?php endfor; ?>
                        <?php if ($userRating): ?>
                            <span class="ml-2 text-gray-600 dark:text-gray-400"><?= trans('you_rated_this') ?> <?= (int) $userRating['score'] ?> <?= (int) $userRating['score'] === 1 ? trans('star') : trans('stars') ?></span>
                        <?php endif; ?>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-8 border-t dark:border-gray-700 pt-8">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4"><?= trans('comments') ?></h2>

        <?php if ($user): ?>
            <form method="POST" action="/books/<?= $bookId ?>/comments" class="mb-6">
                <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                <textarea name="comment" rows="3" placeholder="<?= trans('write_comment') ?>" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                <button type="submit" class="mt-2 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 shadow-md hover:shadow-lg transition-all"><?= trans('post_comment') ?></button>
            </form>
        <?php else: ?>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                <a href="/login" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium"><?= trans('login') ?></a> <?= trans('login_to_comment') ?>
            </p>
        <?php endif; ?>

        <div class="space-y-4">
            <?php if ($comments): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <?php $avatarUrl = media_url($comment['avatar'] ?? null); ?>
                                <?php if ($avatarUrl): ?>
                                    <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" class="w-8 h-8 rounded-full object-cover ring-2 ring-primary-200 dark:ring-primary-800">
                                <?php else: ?>
                                    <div class="w-8 h-8 rounded-full bg-primary-500 dark:bg-primary-600 flex items-center justify-center text-white text-sm font-semibold">
                                        <?= htmlspecialchars(strtoupper(substr($comment['email'], 0, 1))) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-gray-100"><?= htmlspecialchars($comment['first_name'] ?? $comment['email']) ?></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars(time_ago($comment['created_at'])) ?></p>
                                </div>
                            </div>
                            <?php if ($user && ($comment['user_id'] == $user['user_id'] || is_admin())): ?>
                                <form method="POST" action="/comments/<?= (int) $comment['id'] ?>/delete" onsubmit="return confirm('<?= trans('are_you_sure_delete') ?>')" class="text-right">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 text-sm font-medium"><?= trans('delete') ?></button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line"><?= htmlspecialchars($comment['comment']) ?></p>
                        <div class="mt-3 flex items-center gap-4">
                            <button 
                                type="button"
                                onclick="toggleCommentLike(<?= (int) $comment['id'] ?>, this)"
                                class="flex items-center gap-1 text-sm <?= !empty($comment['is_liked']) ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400' ?> transition-colors"
                            >
                                <svg class="w-4 h-4" fill="<?= !empty($comment['is_liked']) ? 'currentColor' : 'none' ?>" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span class="likes-count"><?= (int) ($comment['likes_count'] ?? 0) ?></span>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-600 dark:text-gray-400 text-center py-8"><?= trans('no_comments_yet') ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($similarBooks)): ?>
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6"><?= trans('similar_books') ?></h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($similarBooks as $similar): ?>
                <div class="book-card bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-100 dark:border-gray-700">
                    <a href="/books/<?= (int) $similar['book_id'] ?>" class="block">
                        <div class="aspect-[3/4] bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 overflow-hidden relative group">
                            <?php $cover = media_url($similar['cover_image'] ?? null); ?>
                            <?php if ($cover): ?>
                                <img src="<?= htmlspecialchars($cover) ?>" alt="<?= htmlspecialchars($similar['title']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-500">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-1 line-clamp-2 min-h-[3rem]"><?= htmlspecialchars($similar['title']) ?></h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2"><?= htmlspecialchars($similar['author']) ?></p>
                            <div class="flex items-center">
                                <?php
                                $avg = round((float) ($similar['avg_rating'] ?? 0));
                                $cnt = (int) ($similar['ratings_count'] ?? 0);
                                ?>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <svg class="w-4 h-4 <?= $i <= $avg ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' ?>" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                <?php endfor; ?>
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">(<?= $cnt ?>)</span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php if ($user): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initFavoriteButton('favoriteBtn', <?= $bookId ?>, '<?= htmlspecialchars(csrf_token()) ?>');
});

function toggleCommentLike(commentId, button) {
    const csrfToken = '<?= htmlspecialchars(csrf_token()) ?>';
    const formData = new URLSearchParams();
    formData.append('_token', csrfToken);
    
    fetch(`/comments/${commentId}/like`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const svg = button.querySelector('svg');
            const countSpan = button.querySelector('.likes-count');
            
            if (data.is_liked) {
                button.className = button.className.replace('text-gray-600 dark:text-gray-400', 'text-red-600 dark:text-red-400');
                svg.setAttribute('fill', 'currentColor');
            } else {
                button.className = button.className.replace('text-red-600 dark:text-red-400', 'text-gray-600 dark:text-gray-400');
                svg.setAttribute('fill', 'none');
            }
            
            countSpan.textContent = data.likes_count;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred', 'error');
    });
}
</script>
<?php endif; ?>

