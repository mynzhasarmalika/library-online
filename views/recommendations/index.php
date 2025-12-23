<?php
$recommended = $recommended ?? [];
$popular = $popular ?? [];
?>
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Book Recommendations</h1>
    <p class="text-gray-600 dark:text-gray-400 mt-2">Discover books tailored for you</p>
</div>

<!-- Recommended for You -->
<?php if (!empty($recommended)): ?>
    <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">Recommended for You</h2>
        <p class="text-gray-600 dark:text-gray-400 mb-6">Based on your favorite genres and reading history</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($recommended as $book): ?>
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
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-1 line-clamp-2 min-h-[3rem]"><?= htmlspecialchars($book['title']) ?></h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3"><?= htmlspecialchars($book['author']) ?></p>
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
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php else: ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-12 text-center border border-gray-100 dark:border-gray-700 mb-12">
        <p class="text-gray-600 dark:text-gray-400 mb-4">We need more information about your preferences to provide recommendations.</p>
        <p class="text-sm text-gray-500 dark:text-gray-500">Add some books to favorites or complete a few books to get personalized recommendations!</p>
    </div>
<?php endif; ?>

<!-- Popular Books -->
<?php if (!empty($popular)): ?>
    <section>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">Popular Books</h2>
        <p class="text-gray-600 dark:text-gray-400 mb-6">Trending books in the library</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($popular as $book): ?>
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
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-1 line-clamp-2 min-h-[3rem]"><?= htmlspecialchars($book['title']) ?></h3>
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
                                <span class="text-xs text-gray-500 dark:text-gray-400">❤️ <?= (int) ($book['favorites_count'] ?? 0) ?></span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

