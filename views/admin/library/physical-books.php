<?php
$books = $books ?? [];
$pagination = $pagination ?? ['total' => 0, 'current_page' => 1, 'last_page' => 1];
$search = $search ?? '';
?>
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100"><?= trans('manage_physical_books') ?></h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2"><?= trans('manage_physical_copies') ?></p>
    </div>
    <a href="/admin/library/physical-books/create" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 shadow-md hover:shadow-lg transition-all">
        + <?= trans('manage_physical_books') ?>
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700">
    <form method="GET" action="/admin/library/physical-books" class="mb-6">
        <div class="flex gap-2">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="<?= trans('search_placeholder') ?>" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700"><?= trans('search') ?></button>
            <?php if ($search): ?>
                <a href="/admin/library/physical-books" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600"><?= trans('clear') ?></a>
            <?php endif; ?>
        </div>
    </form>

    <?php if (count($books) > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b dark:border-gray-700">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('inventory_number') ?></th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('books') ?></th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('location') ?></th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('status') ?></th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('active_loans') ?></th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300"><?= trans('actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900">
                            <td class="py-3 px-4 text-sm text-gray-700 dark:text-gray-300 font-mono"><?= htmlspecialchars($book['inventory_number']) ?></td>
                            <td class="py-3 px-4 text-sm">
                                <div class="font-semibold text-gray-800 dark:text-gray-100"><?= htmlspecialchars($book['title']) ?></div>
                                <div class="text-gray-600 dark:text-gray-400"><?= htmlspecialchars($book['author']) ?></div>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400"><?= htmlspecialchars($book['location'] ?? trans('location_not_provided')) ?></td>
                            <td class="py-3 px-4 text-sm">
                                <?php if ($book['is_available'] && empty($book['active_loans'])): ?>
                                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs font-medium"><?= trans('physical_copy_available') ?></span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full text-xs font-medium"><?= trans('physical_copy_on_loan') ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400"><?= (int) ($book['active_loans'] ?? 0) ?></td>
                            <td class="py-3 px-4 text-sm text-right">
                                <a href="/admin/library/physical-books/<?= (int) $book['physical_book_id'] ?>/edit" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 mr-3"><?= trans('edit') ?></a>
                                <form method="POST" action="/admin/library/physical-books/<?= (int) $book['physical_book_id'] ?>/delete" class="inline" onsubmit="return confirm('<?= htmlspecialchars(trans('are_you_sure_delete')) ?>')">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300"><?= trans('delete') ?></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($pagination['last_page'] > 1): ?>
            <div class="mt-6 flex items-center justify-center space-x-2">
                <?php for ($p = 1; $p <= $pagination['last_page']; $p++): ?>
                    <?php
                        $query = array_merge($_GET, ['page' => $p]);
                        $url = '?' . http_build_query($query);
                    ?>
                    <a href="<?= $url ?>" class="px-3 py-1 rounded-md border <?= $pagination['current_page'] === $p ? 'bg-primary-600 text-white border-primary-600' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center py-12">
            <p class="text-gray-600 dark:text-gray-400"><?= trans('no_physical_books_found') ?></p>
            <a href="/admin/library/physical-books/create" class="mt-4 inline-block text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300"><?= trans('add_first_physical_book') ?></a>
        </div>
    <?php endif; ?>
</div>

