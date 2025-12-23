<?php
$books = $books ?? [];
$pagination = $pagination ?? ['current_page' => 1, 'last_page' => 1];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>
<?php
$isAdmin = is_admin();
$isLibrarian = is_librarian();
$roleColor = $isAdmin ? 'red' : 'emerald';
?>
<div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-bold <?= $isAdmin ? 'text-red-800 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' ?>"><?= trans('manage_books') ?></h1>
    <a href="/admin/books/create" class="<?= $isAdmin ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700' ?> text-white px-6 py-2 rounded-lg font-medium">
        <?= trans('add_new_book') ?>
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?= trans('cover') ?></th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?= trans('title') ?></th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?= trans('author') ?></th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?= trans('genre') ?></th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?= trans('rating') ?></th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?= trans('actions') ?></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if ($books): ?>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php $coverUrl = media_url($book['cover_image'] ?? null); ?>
                            <?php if ($coverUrl): ?>
                                <img src="<?= htmlspecialchars($coverUrl) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="w-16 h-20 object-cover rounded">
                            <?php else: ?>
                                <div class="w-16 h-20 bg-gray-200 rounded flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($book['title']) ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500"><?= htmlspecialchars($book['author']) ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-primary-100 text-primary-800"><?= htmlspecialchars($book['genre'] ?? 'N/A') ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <?php $avgRating = round((float) ($book['avg_rating'] ?? 0)); ?>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <svg class="w-4 h-4 <?= $i <= $avgRating ? 'text-yellow-400' : 'text-gray-300' ?>" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                <?php endfor; ?>
                                <span class="ml-1 text-sm text-gray-600">(<?= (int) ($book['ratings_count'] ?? 0) ?>)</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="/admin/books/<?= (int) $book['book_id'] ?>/edit" class="<?= $isAdmin ? 'text-red-600 hover:text-red-900' : 'text-emerald-600 hover:text-emerald-900' ?> mr-3"><?= trans('edit') ?></a>
                            <form action="/admin/books/<?= (int) $book['book_id'] ?>/delete" method="POST" class="inline" onsubmit="return confirm('<?= trans('are_you_sure_delete') ?>')">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="text-red-600 hover:text-red-900"><?= trans('delete') ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500"><?= trans('no_books_found') ?></td>
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

