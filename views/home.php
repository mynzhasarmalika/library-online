<?php
$filters = $filters ?? ['search' => '', 'genres' => [], 'author' => ''];
$bookItems = $books['data'] ?? [];
$pagination = $books['pagination'] ?? ['total' => 0, 'current_page' => 1, 'last_page' => 1];
$isAdmin = is_admin();
$isLibrarian = is_librarian();
?>
<?php if ($isAdmin || $isLibrarian): ?>
    <div class="mb-8 <?= $isAdmin ? 'bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-800' : 'bg-emerald-50 dark:bg-emerald-900/20 border-2 border-emerald-200 dark:border-emerald-800' ?> rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold <?= $isAdmin ? 'text-red-800 dark:text-red-400' : 'text-emerald-700 dark:text-emerald-400' ?> mb-2">
                    <?= $isAdmin ? '🔴 ' . trans('admin_dashboard') : '🟢 ' . trans('librarian_dashboard') ?>
                </h2>
                <p class="<?= $isAdmin ? 'text-red-700 dark:text-red-300' : 'text-emerald-700 dark:text-emerald-300' ?>">
                    <?= $isAdmin ? trans('full_system_access') : trans('manage_books_users') ?>
                </p>
            </div>
            <a href="/admin/dashboard" class="<?= $isAdmin ? 'bg-red-800 hover:bg-red-900' : 'bg-emerald-600 hover:bg-emerald-700' ?> text-white px-6 py-3 rounded-lg font-semibold transition-colors shadow-md">
                <?= $isAdmin ? trans('admin') : trans('librarian') ?> →
            </a>
        </div>
    </div>
<?php endif; ?>
<!-- Recommendations Banner -->
<?php if (!empty($recommended) && !$isAdmin && !$isLibrarian): ?>
        <div class="mb-8 bg-gradient-to-r from-primary-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">📚 <?= trans('recommended_for_you') ?></h2>
                    <p class="text-primary-100"><?= trans('based_on_preferences') ?></p>
                </div>
                <a href="/recommendations" class="bg-white text-primary-600 px-6 py-2 rounded-lg font-semibold hover:bg-primary-50 transition-colors">
                    <?= trans('view_all') ?> →
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <?php foreach (array_slice($recommended, 0, 4) as $book): ?>
                    <a href="/books/<?= (int) $book['book_id'] ?>" class="bg-white/10 backdrop-blur-sm rounded-lg p-3 hover:bg-white/20 transition-colors">
                        <div class="font-semibold text-sm line-clamp-2"><?= htmlspecialchars($book['title']) ?></div>
                        <div class="text-xs text-primary-100 mt-1"><?= htmlspecialchars($book['author']) ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

<?php if ($isAdmin || $isLibrarian): ?>
    <!-- For Admin and Librarian: Show only dashboard prompt, no catalog -->
    <div class="text-center py-12">
        <p class="text-gray-600 dark:text-gray-400 mb-4">
            <?= $isAdmin ? trans('full_system_access') : trans('manage_books') ?>
        </p>
        <a href="/admin/dashboard" class="inline-block <?= $isAdmin ? 'bg-red-800 hover:bg-red-900' : 'bg-emerald-600 hover:bg-emerald-700' ?> text-white px-8 py-3 rounded-lg font-semibold transition-colors shadow-md">
            <?= $isAdmin ? '🔴 ' . trans('admin_dashboard') : '🟢 ' . trans('librarian_dashboard') ?> →
        </a>
    </div>
<?php else: ?>
    <!-- For regular users: Show book catalog -->
<div class="flex flex-col lg:flex-row gap-6">
    <aside class="lg:w-64 flex-shrink-0">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 sticky top-20 border border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4"><?= trans('filters') ?></h2>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('genre_filter') ?></label>
                <form method="GET" action="/" id="filterForm" data-auto-submit>
                    <?php if (!empty($filters['search'])): ?>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($filters['search']) ?>">
                    <?php endif; ?>
                    <?php if (!empty($filters['author'])): ?>
                        <input type="hidden" name="author" value="<?= htmlspecialchars($filters['author']) ?>">
                    <?php endif; ?>
                    <?php if (!empty($_GET['sort'])): ?>
                        <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort']) ?>">
                    <?php endif; ?>
                    <div class="space-y-2 max-h-64 overflow-y-auto pr-2">
                        <?php if (empty($genres)): ?>
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic"><?= trans('no_genres_available') ?></p>
                        <?php else: ?>
                            <?php foreach ($genres as $genre): ?>
                                <?php if (!empty(trim($genre))): ?>
                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            name="genres[]"
                                            value="<?= htmlspecialchars(trim($genre)) ?>"
                                            <?= in_array(trim($genre), array_map('trim', $filters['genres'] ?? []), true) ? 'checked' : '' ?>
                                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                        >
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300"><?= htmlspecialchars(trim($genre)) ?></span>
                                    </label>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('author_filter') ?></label>
                <form method="GET" action="/" data-auto-submit>
                    <?php if (!empty($filters['search'])): ?>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($filters['search']) ?>">
                    <?php endif; ?>
                    <?php foreach ($filters['genres'] ?? [] as $genre): ?>
                        <input type="hidden" name="genres[]" value="<?= htmlspecialchars($genre) ?>">
                    <?php endforeach; ?>
                    <?php if (!empty($_GET['sort'])): ?>
                        <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort']) ?>">
                    <?php endif; ?>
                    <select
                        name="author"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    >
                        <option value=""><?= trans('all_authors') ?></option>
                        <?php foreach ($authors as $author): ?>
                            <option value="<?= htmlspecialchars($author) ?>" <?= ($filters['author'] ?? '') === $author ? 'selected' : '' ?>>
                                <?= htmlspecialchars($author) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <?php if (!empty($filters['genres']) || !empty($filters['author']) || !empty($filters['search'])): ?>
                <a href="/" class="mt-4 block text-center text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium transition-colors">
                    <?= trans('clear_all_filters') ?>
                </a>
            <?php endif; ?>
        </div>
    </aside>

    <div class="flex-1">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100"><?= trans('book_catalog') ?></h1>
                <?php if (!empty($filters['search']) || !empty($filters['genres']) || !empty($filters['author'])): ?>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                        <?= str_replace(':count', (int) $pagination['total'], trans('found_books')) ?>
                    </p>
                <?php endif; ?>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300"><?= trans('sort_by') ?></label>
                <form method="GET" action="/" class="inline" data-auto-submit>
                    <?php if (!empty($filters['search'])): ?>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($filters['search']) ?>">
                    <?php endif; ?>
                    <?php foreach ($filters['genres'] ?? [] as $genre): ?>
                        <input type="hidden" name="genres[]" value="<?= htmlspecialchars($genre) ?>">
                    <?php endforeach; ?>
                    <?php if (!empty($filters['author'])): ?>
                        <input type="hidden" name="author" value="<?= htmlspecialchars($filters['author']) ?>">
                    <?php endif; ?>
                    <select name="sort" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                        <option value="newest" <?= ($_GET['sort'] ?? 'newest') === 'newest' ? 'selected' : '' ?>><?= trans('newest_first') ?></option>
                        <option value="oldest" <?= ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>><?= trans('oldest_first') ?></option>
                        <option value="rating" <?= ($_GET['sort'] ?? '') === 'rating' ? 'selected' : '' ?>><?= trans('highest_rated') ?></option>
                        <option value="title" <?= ($_GET['sort'] ?? '') === 'title' ? 'selected' : '' ?>><?= trans('title_az') ?></option>
                    </select>
                </form>
            </div>
        </div>

        <?php if (count($bookItems) > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($bookItems as $book): ?>
                    <div class="book-card bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-100 dark:border-gray-700">
                        <a href="/books/<?= (int) $book['book_id'] ?>" class="block">
                            <div class="aspect-[3/4] bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 overflow-hidden relative group">
                                <?php $cover = media_url($book['cover_image'] ?? null); ?>
                                <?php if ($cover): ?>
                                    <img src="<?= htmlspecialchars($cover) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-500">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($book['genre'])): ?>
                                    <div class="absolute top-2 right-2">
                                        <span class="bg-primary-600 text-white text-xs px-2 py-1 rounded-full shadow-lg"><?= htmlspecialchars($book['genre']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-1 line-clamp-2 min-h-[3rem]" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"><?= htmlspecialchars($book['title']) ?></h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3"><?= htmlspecialchars($book['author']) ?></p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <?php
                                        $average = round((float) ($book['avg_rating'] ?? 0));
                                        $count = (int) ($book['ratings_count'] ?? 0);
                                        ?>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <svg class="w-4 h-4 <?= $i <= $average ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' ?>" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        <?php endfor; ?>
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">(<?= $count ?>)</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($pagination['last_page'] > 1): ?>
                <div class="mt-8 flex items-center justify-center space-x-2">
                    <?php for ($p = 1; $p <= $pagination['last_page']; $p++): ?>
                        <?php
                            $query = array_merge($_GET, ['page' => $p]);
                            $url = '?' . http_build_query($query);
                        ?>
                        <a href="<?= $url ?>"
                           class="px-3 py-1 rounded-md border <?= $pagination['current_page'] === $p ? 'bg-primary-600 text-white border-primary-600' : 'border-gray-300 text-gray-700' ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-12 text-center border border-gray-100 dark:border-gray-700">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100"><?= trans('no_books_found_message') ?></h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?= trans('try_adjusting_filters') ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

